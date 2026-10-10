<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\PersonalizedRecommendationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatAiController extends Controller
{
    /**
     * Handle incoming chat requests to Groq AI.
     */
    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
            'history' => ['nullable', 'array', 'max:10'],
            'history.*.role' => ['required_with:history', 'string', 'in:user,assistant'],
            'history.*.content' => ['required_with:history', 'string', 'max:5000'],
        ]);

        $apiKey = config('services.groq.api_key');
        if (! $apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'Trợ lý AI chưa được kích hoạt API key trên hệ thống.',
            ], 503);
        }

        // Tìm kiếm các sản phẩm phù hợp theo nội dung câu hỏi của khách hàng (RAG gọn nhẹ)
        $userMessage = mb_strtolower($validated['message']);
        $productQuery = Product::query()
            ->select(['id', 'name', 'slug', 'price', 'original_price', 'rating', 'description'])
            ->where('status', 'active');

        // Bóc tách các từ khóa quan trọng
        $keywords = array_filter(
            preg_split('/[\s,\?!]+/u', $userMessage),
            fn ($w) => mb_strlen($w) >= 2 && ! in_array($w, ['tôi', 'bạn', 'mình', 'muốn', 'mua', 'nào', 'sao', 'gì', 'cho', 'với', 'các', 'những', 'được', 'không', 'này', 'nên', 'cần'])
        );

        $matchedProducts = collect();
        if (! empty($keywords)) {
            $matchedQuery = clone $productQuery;
            $matchedQuery->where(function ($q) use ($keywords) {
                foreach ($keywords as $kw) {
                    $q->orWhere('name', 'ilike', "%{$kw}%")
                        ->orWhere('description', 'ilike', "%{$kw}%")
                        ->orWhere('brand', 'ilike', "%{$kw}%");
                }
            });
            $matchedProducts = $matchedQuery->take(10)->get();
        }

        // Nếu không khớp từ khóa cụ thể hoặc ít kết quả, kết hợp thêm các sản phẩm hot/nổi bật
        if ($matchedProducts->count() < 12) {
            $fallbackProducts = (clone $productQuery)
                ->whereNotIn('id', $matchedProducts->pluck('id'))
                ->orderByDesc('reviews_count')
                ->take(12 - $matchedProducts->count())
                ->get();
            $matchedProducts = $matchedProducts->concat($fallbackProducts);
        }

        $sampleProducts = $matchedProducts
            ->map(fn ($p) => "- {$p->name} (Giá: ".number_format($p->price, 0, ',', '.')."đ, Đánh giá: {$p->rating}⭐, Link: /product/{$p->slug})")
            ->implode("\n");

        $systemPrompt = <<<PROMPT
Bạn là ShopMart AI - Trợ lý mua sắm trực tuyến thông minh, nhiệt tình và thân thiện của sàn thương mại điện tử ShopMart.
Nhiệm vụ của bạn:
1. Tư vấn, giải đáp thắc mắc về sản phẩm, chính sách mua sắm, đổi trả và khuyến mãi tại ShopMart.
2. Khi người dùng hỏi tìm mua bất kỳ sản phẩm nào (ví dụ: điện thoại, laptop, thời trang, mỹ phẩm...), hãy phân tích nhu cầu và ưu tiên giới thiệu các sản phẩm có thật trong danh sách dưới đây.
3. Khi giới thiệu sản phẩm có trong danh sách, BẮT BUỘC chèn đường link dạng markdown [Tên sản phẩm](/product/slug) và nêu giá tham khảo kèm ưu điểm nổi bật để khách bấm xem chi tiết ngay.
4. Nếu khách hỏi sản phẩm nào ShopMart chưa có trong danh sách, hãy thông báo lịch sự và gợi ý các mẫu tương đương gần nhất đang có.

Danh sách sản phẩm tiêu biểu & có sẵn trên sàn ShopMart:
{$sampleProducts}

Quy tắc giao tiếp:
- Sử dụng tiếng Việt chuẩn mực, lịch sự, xưng "ShopMart AI" hoặc "mình" và gọi khách là "bạn" hoặc "quý khách".
- Câu trả lời súc tích, cấu trúc rõ ràng với icon sinh động, định dạng gạch đầu dòng dễ nhìn.
- Không bịa đặt đường link hoặc thông tin sai lệch ngoài nền tảng ShopMart.
PROMPT;

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        // Append recent conversation history
        if (! empty($validated['history'])) {
            foreach ($validated['history'] as $item) {
                $messages[] = [
                    'role' => $item['role'],
                    'content' => $item['content'],
                ];
            }
        }

        // Append current user message
        $messages[] = [
            'role' => 'user',
            'content' => $validated['message'],
        ];

        try {
            $response = Http::withToken($apiKey)
                ->timeout(20)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => config('services.groq.model', 'openai/gpt-oss-120b'),
                    'messages' => $messages,
                    'temperature' => 0.7,
                    'max_tokens' => 800,
                ]);

            if ($response->successful()) {
                $reply = $response->json('choices.0.message.content') ?? 'Xin lỗi, tôi chưa thể trả lời lúc này.';

                app(PersonalizedRecommendationService::class)->track(
                    auth()->user(),
                    'chat_inquiry',
                    null,
                    ['message' => mb_substr($validated['message'], 0, 100)]
                );

                return response()->json([
                    'success' => true,
                    'reply' => $reply,
                ]);
            }

            Log::error('Groq AI API Error: '.$response->body());

            return response()->json([
                'success' => false,
                'message' => 'Hệ thống AI đang bận, vui lòng thử lại sau giây lát.',
            ], 500);
        } catch (\Throwable $e) {
            Log::error('ChatAiController exception: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Không thể kết nối đến máy chủ AI: '.$e->getMessage(),
            ], 500);
        }
    }
}
