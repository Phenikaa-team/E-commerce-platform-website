<?php

namespace App\Jobs;

use App\Models\Product;
use App\Services\ProductAiEnrichmentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class AnalyzeProductWithAi implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $backoff = 30;

    public function __construct(public int $productId) {}

    public function handle(ProductAiEnrichmentService $service): void
    {
        $product = Product::with('category')->find($this->productId);
        if (! $product) {
            return;
        }

        if (! $service->isConfigured()) {
            $product->forceFill([
                'ai_analysis_status' => 'skipped',
                'ai_analysis_error' => 'AI_API_KEY is not configured.',
            ])->saveQuietly();

            return;
        }

        $product->forceFill([
            'ai_analysis_status' => 'processing',
            'ai_analysis_error' => null,
        ])->saveQuietly();

        try {
            $metadata = $service->analyze($product);

            $product->forceFill([
                'ai_metadata' => $metadata,
                'ai_analysis_status' => 'completed',
                'ai_analyzed_at' => now(),
                'ai_analysis_error' => null,
            ])->saveQuietly();
        } catch (Throwable $exception) {
            $product->forceFill([
                'ai_analysis_status' => 'failed',
                'ai_analysis_error' => mb_substr($exception->getMessage(), 0, 2000),
            ])->saveQuietly();

            throw $exception;
        }
    }
}
