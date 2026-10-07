<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class ProductAiEnrichmentService
{
    public function isConfigured(): bool
    {
        return filled(config('services.ai.api_key'));
    }

    /**
     * Extract product metadata from an external OpenAI-compatible API.
     * The result is application metadata; it does not create categories.
     */
    public function analyze(Product $product): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('AI_API_KEY is not configured.');
        }

        $product->loadMissing('category');

        $response = Http::withToken((string) config('services.ai.api_key'))
            ->acceptJson()
            ->timeout((int) config('services.ai.timeout', 30))
            ->post(rtrim((string) config('services.ai.base_url'), '/').'/chat/completions', [
                'model' => config('services.ai.model'),
                'temperature' => 0,
                'response_format' => ['type' => 'json_object'],
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Classify marketplace products and return JSON only. Do not invent values unsupported by the input. Use null when unknown. Internal tags are normalized labels. Expected keys: product_type, brand, series, gender, segment, attributes, tags, confidence.',
                    ],
                    [
                        'role' => 'user',
                        'content' => json_encode([
                            'category' => $product->category?->name,
                            'category_slug' => $product->category?->slug,
                            'name' => $product->name,
                            'brand' => $product->brand,
                            'description' => $product->description,
                            'specs' => $product->specs,
                            'features' => $product->features,
                        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    ],
                ],
            ])
            ->throw();

        $content = data_get($response->json(), 'choices.0.message.content');
        if (! is_string($content) || trim($content) === '') {
            throw new RuntimeException('AI response did not contain message content.');
        }

        $content = trim(preg_replace('/^```(?:json)?|```$/m', '', $content));
        $metadata = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        return $this->normalize($metadata);
    }

    private function normalize(array $metadata): array
    {
        $allowedKeys = ['product_type', 'brand', 'series', 'gender', 'segment', 'attributes', 'tags', 'confidence'];
        $metadata = array_intersect_key($metadata, array_flip($allowedKeys));

        $metadata['attributes'] = is_array($metadata['attributes'] ?? null) ? $metadata['attributes'] : [];
        $metadata['tags'] = array_values(array_filter(array_map(
            static fn ($tag) => is_string($tag) ? trim(mb_strtolower($tag)) : null,
            is_array($metadata['tags'] ?? null) ? $metadata['tags'] : []
        )));
        $metadata['confidence'] = is_numeric($metadata['confidence'] ?? null)
            ? max(0, min(1, (float) $metadata['confidence']))
            : null;

        foreach (['product_type', 'brand', 'series', 'gender', 'segment'] as $key) {
            if (isset($metadata[$key]) && ! is_string($metadata[$key])) {
                $metadata[$key] = null;
            }
        }

        return $metadata;
    }
}
