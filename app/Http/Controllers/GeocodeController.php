<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GeocodeController extends Controller
{
    /**
     * Reverse geocode latitude and longitude to Vietnamese address components.
     */
    public function reverse(Request $request): JsonResponse
    {
        $lat = (float) $request->query('lat');
        $lng = (float) $request->query('lng');

        if (! $lat || ! $lng || abs($lat) > 90 || abs($lng) > 180) {
            return response()->json(['success' => false, 'message' => 'Tọa độ không hợp lệ.'], 400);
        }

        $cacheKey = 'geocode_rev_'.round($lat, 4).'_'.round($lng, 4);

        $result = Cache::remember($cacheKey, 86400, function () use ($lat, $lng) {
            return $this->performReverseGeocode($lat, $lng);
        });

        return response()->json($result);
    }

    /**
     * Detect approximate location via IP address.
     */
    public function ipLocation(Request $request): JsonResponse
    {
        $ip = $request->ip();
        $isLocal = in_array($ip, ['127.0.0.1', '::1'])
            || str_starts_with($ip, '192.168.')
            || str_starts_with($ip, '10.')
            || str_starts_with($ip, '172.');

        $url = $isLocal ? 'http://ip-api.com/json/' : "http://ip-api.com/json/{$ip}";

        try {
            $response = Http::timeout(4)->get($url);
            if ($response->successful()) {
                $data = $response->json();
                if (($data['status'] ?? '') === 'success') {
                    $lat = (float) ($data['lat'] ?? 21.0285);
                    $lng = (float) ($data['lon'] ?? 105.8542);
                    $regionName = $data['regionName'] ?? ($data['city'] ?? 'Hà Nội');

                    return response()->json([
                        'success' => true,
                        'lat' => $lat,
                        'lng' => $lng,
                        'city' => $data['city'] ?? 'Hà Nội',
                        'province' => $this->normalizeProvince($regionName),
                        'source' => 'ip',
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // Silently fallback to default
        }

        return response()->json([
            'success' => true,
            'lat' => 21.0285,
            'lng' => 105.8542,
            'city' => 'Hà Nội',
            'province' => 'TP. Hà Nội',
            'source' => 'default',
        ]);
    }

    /**
     * Perform reverse geocoding with multi-provider fallback.
     *
     * @return array<string, mixed>
     */
    private function performReverseGeocode(float $lat, float $lng): array
    {
        // 1. Try Nominatim (OpenStreetMap) with Vietnamese language preference
        try {
            $resp = Http::withHeaders(['User-Agent' => 'ShopMart/1.0 (support@shopmart.vn)'])
                ->timeout(5)
                ->get('https://nominatim.openstreetmap.org/reverse', [
                    'lat' => $lat,
                    'lon' => $lng,
                    'format' => 'json',
                    'accept-language' => 'vi',
                    'addressdetails' => 1,
                ]);

            if ($resp->successful()) {
                $data = $resp->json();
                $addr = $data['address'] ?? [];

                $rawProvince = $addr['city'] ?? $addr['state'] ?? $addr['province'] ?? '';
                $province = $this->normalizeProvince($rawProvince);

                $district = $addr['district']
                    ?? $addr['city_district']
                    ?? $addr['county']
                    ?? $addr['town']
                    ?? $addr['municipality']
                    ?? '';

                $ward = $addr['suburb']
                    ?? $addr['quarter']
                    ?? $addr['neighbourhood']
                    ?? $addr['village']
                    ?? $addr['hamlet']
                    ?? '';

                // Build specific detail without coordinates numbers
                $houseNumber = $addr['house_number'] ?? '';
                $road = $addr['road'] ?? $addr['street'] ?? $addr['pedestrian'] ?? '';
                $amenity = $addr['amenity'] ?? $addr['shop'] ?? $addr['building'] ?? '';

                $detailParts = array_filter([$houseNumber ? 'Số '.$houseNumber : null, $road, $amenity]);
                if (! empty($detailParts)) {
                    $detail = implode(', ', $detailParts);
                } elseif (! empty($ward)) {
                    $detail = 'Khu vực '.$ward;
                } elseif (! empty($district)) {
                    $detail = 'Khu vực '.$district;
                } else {
                    $detail = 'Khu vực '.$province;
                }

                $fullDisplay = $data['display_name'] ?? implode(', ', array_filter([$detail, $ward, $district, $province]));

                return [
                    'success' => true,
                    'detail' => $detail,
                    'ward' => $ward,
                    'district' => $district,
                    'province' => $province,
                    'full_address' => $fullDisplay,
                    'lat' => $lat,
                    'lng' => $lng,
                    'provider' => 'nominatim',
                ];
            }
        } catch (\Throwable $e) {
            // Try next provider
        }

        // 2. Try BigDataCloud
        try {
            $resp = Http::timeout(4)->get('https://api.bigdatacloud.net/data/reverse-geocode-client', [
                'latitude' => $lat,
                'longitude' => $lng,
                'localityLanguage' => 'vi',
            ]);

            if ($resp->successful()) {
                $data = $resp->json();
                $admin = $data['localityInfo']['administrative'] ?? [];

                $subdivision = $data['principalSubdivision'] ?? '';
                $province = $this->normalizeProvince($subdivision);

                $district = '';
                $ward = '';
                foreach ($admin as $item) {
                    $order = $item['order'] ?? 0;
                    $name = $item['name'] ?? '';
                    if ($order === 4 && ! $district) {
                        $district = $name;
                    } elseif ($order >= 5 && ! $ward) {
                        $ward = $name;
                    }
                }

                $locality = $data['locality'] ?? '';
                $detail = $locality ?: ($ward ? 'Khu vực '.$ward : ($district ? 'Khu vực '.$district : 'Khu vực '.$province));

                return [
                    'success' => true,
                    'detail' => $detail,
                    'ward' => $ward,
                    'district' => $district,
                    'province' => $province,
                    'full_address' => implode(', ', array_filter([$detail, $ward, $district, $province])),
                    'lat' => $lat,
                    'lng' => $lng,
                    'provider' => 'bigdatacloud',
                ];
            }
        } catch (\Throwable $e) {
            // Try next
        }

        // 3. Fallback: Intelligent coordinate-based Vietnamese district/province detection
        $fallback = $this->detectDistrictFromCoords($lat, $lng);

        return [
            'success' => true,
            'detail' => $fallback['detail'],
            'ward' => $fallback['ward'],
            'district' => $fallback['district'],
            'province' => $fallback['province'],
            'full_address' => $fallback['full_address'],
            'lat' => $lat,
            'lng' => $lng,
            'provider' => 'coordinate_lookup',
        ];
    }

    /**
     * Map coordinates to Vietnamese province and district offline.
     *
     * @return array{province: string, district: string, ward: string, detail: string, full_address: string}
     */
    private function detectDistrictFromCoords(float $lat, float $lng): array
    {
        $province = 'TP. Hà Nội';
        $district = '';
        $ward = '';

        if ($lat >= 20.4 && $lat <= 21.5 && $lng >= 105.2 && $lng <= 106.3) {
            $province = 'TP. Hà Nội';
            if ($lat >= 20.65 && $lat <= 20.85 && $lng >= 105.70 && $lng <= 105.88) {
                $district = 'Ứng Hòa';
            } elseif ($lat >= 20.90 && $lat <= 21.00 && $lng >= 105.72 && $lng <= 105.80) {
                $district = 'Hà Đông';
            } elseif ($lat >= 21.00 && $lat <= 21.07 && $lng >= 105.76 && $lng <= 105.82) {
                $district = 'Cầu Giấy';
            } elseif ($lat >= 21.01 && $lat <= 21.05 && $lng >= 105.83 && $lng <= 105.87) {
                $district = 'Hoàn Kiếm';
            } elseif ($lat >= 21.00 && $lat <= 21.05 && $lng >= 105.80 && $lng <= 105.84) {
                $district = 'Đống Đa';
            } elseif ($lat >= 21.02 && $lat <= 21.08 && $lng >= 105.80 && $lng <= 105.85) {
                $district = 'Ba Đình';
            } elseif ($lat >= 20.97 && $lat <= 21.02 && $lng >= 105.83 && $lng <= 105.88) {
                $district = 'Hai Bà Trưng';
            } elseif ($lat >= 20.97 && $lat <= 21.02 && $lng >= 105.78 && $lng <= 105.83) {
                $district = 'Thanh Xuân';
            } elseif ($lat >= 20.96 && $lat <= 21.02 && $lng >= 105.82 && $lng <= 105.87) {
                $district = 'Hoàng Mai';
            } elseif ($lat >= 21.00 && $lat <= 21.10 && $lng >= 105.86 && $lng <= 105.95) {
                $district = 'Long Biên';
            } elseif ($lat >= 21.02 && $lat <= 21.12 && $lng >= 105.70 && $lng <= 105.78) {
                $district = 'Bắc Từ Liêm';
            } elseif ($lat >= 20.98 && $lat <= 21.05 && $lng >= 105.72 && $lng <= 105.78) {
                $district = 'Nam Từ Liêm';
            } elseif ($lat >= 21.05 && $lat <= 21.12 && $lng >= 105.78 && $lng <= 105.85) {
                $district = 'Tây Hồ';
            }
        } elseif ($lat >= 10.3 && $lat <= 11.2 && $lng >= 106.3 && $lng <= 107.1) {
            $province = 'TP. Hồ Chí Minh';
            if ($lat >= 10.75 && $lat <= 10.80 && $lng >= 106.68 && $lng <= 106.72) {
                $district = 'Quận 1';
            } elseif ($lat >= 10.76 && $lat <= 10.80 && $lng >= 106.66 && $lng <= 106.70) {
                $district = 'Quận 3';
            } elseif ($lat >= 10.78 && $lat <= 10.85 && $lng >= 106.68 && $lng <= 106.73) {
                $district = 'Bình Thạnh';
            } elseif ($lat >= 10.78 && $lat <= 10.88 && $lng >= 106.72 && $lng <= 106.85) {
                $district = 'TP. Thủ Đức';
            } elseif ($lat >= 10.71 && $lat <= 10.76 && $lng >= 106.69 && $lng <= 106.75) {
                $district = 'Quận 7';
            } elseif ($lat >= 10.78 && $lat <= 10.83 && $lng >= 106.64 && $lng <= 106.68) {
                $district = 'Tân Bình';
            }
        } elseif ($lat >= 15.8 && $lat <= 16.3 && $lng >= 108.0 && $lng <= 108.4) {
            $province = 'TP. Đà Nẵng';
            $district = 'Hải Châu';
        } else {
            $province = ($lat >= 16.0) ? 'TP. Hà Nội' : 'TP. Hồ Chí Minh';
        }

        $detail = $district ? 'Khu vực '.$district : 'Khu vực trung tâm';
        $fullAddress = implode(', ', array_filter([$district, $province, 'Việt Nam']));

        return [
            'province' => $province,
            'district' => $district,
            'ward' => $ward,
            'detail' => $detail,
            'full_address' => $fullAddress,
        ];
    }

    /**
     * Normalize province name to match the application's select dropdown values.
     */
    private function normalizeProvince(string $input): string
    {
        $input = trim($input);
        if (empty($input)) {
            return 'TP. Hà Nội';
        }

        if (stripos($input, 'Hà Nội') !== false || stripos($input, 'Hanoi') !== false) {
            return 'TP. Hà Nội';
        }
        if (stripos($input, 'Hồ Chí Minh') !== false || stripos($input, 'Ho Chi Minh') !== false || stripos($input, 'Sài Gòn') !== false) {
            return 'TP. Hồ Chí Minh';
        }
        if (stripos($input, 'Đà Nẵng') !== false || stripos($input, 'Da Nang') !== false) {
            return 'TP. Đà Nẵng';
        }
        if (stripos($input, 'Hải Phòng') !== false || stripos($input, 'Hai Phong') !== false) {
            return 'TP. Hải Phòng';
        }
        if (stripos($input, 'Cần Thơ') !== false || stripos($input, 'Can Tho') !== false) {
            return 'TP. Cần Thơ';
        }

        $provinces = [
            'An Giang', 'Bà Rịa - Vũng Tàu', 'Bắc Giang', 'Bắc Kạn', 'Bạc Liêu', 'Bắc Ninh',
            'Bến Tre', 'Bình Định', 'Bình Dương', 'Bình Phước', 'Bình Thuận', 'Cà Mau',
            'Cao Bằng', 'Đắk Lắk', 'Đắk Nông', 'Điện Biên', 'Đồng Nai', 'Đồng Tháp',
            'Gia Lai', 'Hà Giang', 'Hà Nam', 'Hà Tĩnh', 'Hải Dương', 'Hậu Giang',
            'Hòa Bình', 'Hưng Yên', 'Khánh Hòa', 'Kiên Giang', 'Kon Tum', 'Lai Châu',
            'Lâm Đồng', 'Lạng Sơn', 'Lào Cai', 'Long An', 'Nam Định', 'Nghệ An',
            'Ninh Bình', 'Ninh Thuận', 'Phú Thọ', 'Phú Yên', 'Quảng Bình', 'Quảng Nam',
            'Quảng Ngãi', 'Quảng Ninh', 'Quảng Trị', 'Sóc Trăng', 'Sơn La', 'Tây Ninh',
            'Thái Bình', 'Thái Nguyên', 'Thanh Hóa', 'Thừa Thiên Huế', 'Tiền Giang',
            'Trà Vinh', 'Tuyên Quang', 'Vĩnh Long', 'Vĩnh Phúc', 'Yên Bái',
        ];

        foreach ($provinces as $p) {
            if (stripos($input, $p) !== false || stripos($p, $input) !== false) {
                return $p;
            }
        }

        return $input;
    }
}
