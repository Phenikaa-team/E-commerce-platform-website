<?php

if (! function_exists('project_asset_base_url')) {
    function project_asset_base_url(): string
    {
        $localBase = rtrim((string) config('app.url'), '/');
        if ((string) config('filesystems.image_upload_disk', 'public') !== 'supabase') {
            return $localBase;
        }

        $url = rtrim((string) config('services.supabase.url'), '/');
        $bucket = (string) config('services.supabase.bucket', 'images');

        if ($url === '' || $bucket === '') {
            return $localBase;
        }

        return $url.'/storage/v1/object/public/'.rawurlencode($bucket).'/project-assets/public';
    }
}

if (! function_exists('project_asset')) {
    function project_asset(string $path): string
    {
        $path = ltrim($path, '/');

        if (! str_starts_with($path, 'images/') && ! str_starts_with($path, 'icons/')) {
            return asset($path);
        }

        $base = project_asset_base_url();
        $localBase = rtrim((string) config('app.url'), '/');

        if ($base === $localBase) {
            return asset($path);
        }

        $encodedPath = implode('/', array_map('rawurlencode', explode('/', $path)));

        return $base.'/'.$encodedPath;
    }
}

if (! function_exists('project_asset_value')) {
    function project_asset_value(?string $value): ?string
    {
        if (empty($value)) {
            return $value;
        }

        $valueParts = parse_url($value);
        $appParts = parse_url((string) config('app.url'));
        $path = $valueParts['path'] ?? $value;
        $valueHost = $valueParts['host'] ?? null;
        $appHost = $appParts['host'] ?? null;

        if ($valueHost !== null && $appHost !== null && strcasecmp($valueHost, $appHost) !== 0) {
            return $value;
        }

        $path = ltrim($path, '/');
        if (str_starts_with($path, 'images/') || str_starts_with($path, 'icons/')) {
            return project_asset($path);
        }

        return $value;
    }
}
