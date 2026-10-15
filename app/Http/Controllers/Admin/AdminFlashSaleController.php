<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FlashSaleCampaign;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminFlashSaleController extends Controller
{
    public function index(): View
    {
        $manualCampaigns = FlashSaleCampaign::query()
            ->where('schedule_type', 'range')
            ->withCount('items')
            ->latest()
            ->paginate(20);
        $automationCampaigns = FlashSaleCampaign::query()
            ->where('schedule_type', 'recurring')
            ->withCount('items')
            ->latest()
            ->paginate(20, ['*'], 'automation_page');

        return view('admin.flash-sales.index', compact('manualCampaigns', 'automationCampaigns'));
    }

    public function create(): View
    {
        return $this->createSchedule();
    }

    public function createSchedule(): View
    {
        return view('admin.flash-sales.form', [
            'campaign' => new FlashSaleCampaign,
            'mode' => 'manual',
        ]);
    }

    public function createAutomation(): View
    {
        return view('admin.flash-sales.form', [
            'campaign' => new FlashSaleCampaign,
            'mode' => 'automation',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        FlashSaleCampaign::create($this->validatedData($request));

        return redirect()->route('admin.flash-sales.index')->with('success', 'Đã tạo đợt Flash Sale.');
    }

    public function edit(int $id): View
    {
        $campaign = FlashSaleCampaign::findOrFail($id);

        return view('admin.flash-sales.form', [
            'campaign' => $campaign,
            'mode' => $campaign->schedule_type === 'recurring' ? 'automation' : 'manual',
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        FlashSaleCampaign::findOrFail($id)->update($this->validatedData($request));

        return redirect()->route('admin.flash-sales.index')->with('success', 'Đã cập nhật đợt Flash Sale.');
    }

    public function toggle(int $id): RedirectResponse
    {
        $campaign = FlashSaleCampaign::findOrFail($id);
        $campaign->update(['is_active' => ! $campaign->is_active]);

        return back()->with('success', $campaign->is_active ? 'Đã bật đợt Flash Sale.' : 'Đã tắt đợt Flash Sale.');
    }

    public function destroy(int $id): RedirectResponse
    {
        FlashSaleCampaign::findOrFail($id)->delete();

        return back()->with('success', 'Đã xóa đợt Flash Sale và các sản phẩm đăng ký trong đợt.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'schedule_type' => 'required|in:range,recurring',
            'starts_at' => 'nullable|date|required_if:schedule_type,range',
            'ends_at' => 'nullable|date|after:starts_at|required_if:schedule_type,range',
            'recurring_dates' => 'nullable|string',
            'daily_starts_at' => 'nullable|date_format:H:i|required_if:schedule_type,recurring',
            'daily_ends_at' => 'nullable|date_format:H:i|required_if:schedule_type,recurring',
            'all_matching_dates' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        if ($data['schedule_type'] === 'range') {
            return [
                'name' => $data['name'],
                'schedule_type' => 'range',
                'starts_at' => $data['starts_at'],
                'ends_at' => $data['ends_at'],
                'recurring_dates' => null,
                'daily_starts_at' => null,
                'daily_ends_at' => null,
                'is_active' => $request->boolean('is_active'),
            ];
        }

        if (! $request->boolean('all_matching_dates') && blank($data['recurring_dates'] ?? null)) {
            throw ValidationException::withMessages([
                'recurring_dates' => 'Hãy chọn tất cả ngày trùng hoặc nhập ít nhất một ngày lặp.',
            ]);
        }

        return [
            'name' => $data['name'],
            'schedule_type' => 'recurring',
            'starts_at' => null,
            'ends_at' => null,
            'recurring_dates' => $request->boolean('all_matching_dates')
                ? $this->allMatchingDates()
                : $this->parseRecurringDates($data['recurring_dates']),
            'daily_starts_at' => $data['daily_starts_at'],
            'daily_ends_at' => $data['daily_ends_at'],
            'is_active' => $request->boolean('is_active'),
        ];
    }

    /**
     * @return list<string>
     */
    private function allMatchingDates(): array
    {
        return collect(range(1, 12))
            ->map(fn (int $number): string => sprintf('%02d/%02d', $number, $number))
            ->all();
    }

    /**
     * Parse Vietnamese day/month input such as "01/01, 02/02, 03/03".
     *
     * @return list<string>
     */
    private function parseRecurringDates(string $value): array
    {
        $dates = [];
        foreach (preg_split('/[,;\s]+/', trim($value)) ?: [] as $part) {
            if ($part === '') {
                continue;
            }

            $pieces = preg_split('/[\/-]/', $part);
            if (count($pieces) !== 2 || ! ctype_digit($pieces[0]) || ! ctype_digit($pieces[1])) {
                throw ValidationException::withMessages([
                    'recurring_dates' => 'Ngày lặp phải có dạng dd/mm, ví dụ: 01/01, 02/02, 03/03.',
                ]);
            }

            $day = (int) $pieces[0];
            $month = (int) $pieces[1];
            if (! checkdate($month, $day, 2024)) {
                throw ValidationException::withMessages([
                    'recurring_dates' => "Ngày {$part} không hợp lệ.",
                ]);
            }

            $dates[] = sprintf('%02d/%02d', $month, $day);
        }

        return array_values(array_unique($dates));
    }
}
