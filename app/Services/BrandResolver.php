<?php

namespace App\Services;

use App\Models\Brand;
use Illuminate\Support\Str;

class BrandResolver
{
    public function resolve(?string $value): ?Brand
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        $slug = Str::slug($value);
        $brand = Brand::query()->where('slug', $slug)->first()
            ?: Brand::query()->whereHas('aliases', fn ($query) => $query->where('slug', $slug))->first();

        if ($brand) {
            return $brand;
        }

        return Brand::create([
            'name' => $value,
            'slug' => $slug,
            'status' => 'pending',
        ]);
    }
}
