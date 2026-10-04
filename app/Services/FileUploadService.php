<?php

namespace App\Services;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    /**
     * Allowed image MIME types.
     */
    public const ALLOWED_IMAGE_MIMES = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif',
        'image/avif',
    ];

    /**
     * Upload an image file to the specified storage disk and directory.
     * Optionally deletes the previous file if it was stored locally.
     *
     * @return array{url: string, path: string, disk: string, original_name: string, mime_type: string, size: int}
     */
    public static function upload(
        UploadedFile $file,
        string $folder = 'uploads',
        ?string $oldUrlOrPath = null,
        ?string $disk = null
    ): array {
        $disk ??= (string) config('filesystems.image_upload_disk', 'public');

        // Safe filename generation using UUID to prevent collisions and path traversal
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'jpg');
        $safeName = Str::uuid().'.'.$extension;

        // Store file onto specified disk
        $path = trim($folder, '/').'/'.$safeName;

        if ($disk === 'supabase') {
            static::uploadToSupabase($file, $path);
            $url = static::supabaseObjectUrl($path);
        } else {
            $path = $file->storeAs($folder, $safeName, $disk);

            /** @var FilesystemAdapter $storage */
            $storage = Storage::disk($disk);
            $url = $storage->url($path);
        }

        // Delete old file if provided and stored locally
        if (! empty($oldUrlOrPath)) {
            static::delete($oldUrlOrPath, $disk);
        }

        return [
            'url' => $url,
            'path' => $path,
            'disk' => $disk,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => (string) ($file->getClientMimeType() ?: $file->getMimeType() ?: 'application/octet-stream'),
            'size' => (int) $file->getSize(),
        ];
    }

    /**
     * Upload multiple files to a directory.
     *
     * @param  array<UploadedFile>  $files
     * @return array<int, array{url: string, path: string, disk: string, original_name: string, mime_type: string, size: int}>
     */
    public static function uploadMultiple(array $files, string $folder, ?string $disk = null): array
    {
        $uploaded = [];
        try {
            foreach ($files as $file) {
                if ($file instanceof UploadedFile) {
                    $uploaded[] = static::upload($file, $folder, null, $disk);
                }
            }
        } catch (\Throwable $e) {
            // Clean up any files already uploaded during this failed batch
            foreach ($uploaded as $item) {
                static::delete($item['path'], $disk);
            }
            throw $e;
        }

        return $uploaded;
    }

    /**
     * Safely delete a file from storage given its URL or relative path.
     * Ignores external URLs (e.g. Unsplash) and placeholder assets.
     */
    public static function delete(?string $urlOrPath, ?string $disk = null): bool
    {
        if (empty($urlOrPath)) {
            return false;
        }

        $disk ??= (string) config('filesystems.image_upload_disk', 'public');

        if ($disk === 'supabase') {
            $supabasePath = static::supabasePathFromUrl($urlOrPath);
            if ($supabasePath !== null) {
                return static::deleteFromSupabase($supabasePath);
            }

            // Allow old local /storage URLs to be cleaned up after switching disks.
            $disk = 'public';
        }

        // Never delete placeholders or asset paths.
        if (str_contains($urlOrPath, 'placeholders/') || str_contains($urlOrPath, '/images/')) {
            return false;
        }

        // External URLs (Unsplash, CDN, etc.) cannot be deleted from local disk
        if (str_starts_with($urlOrPath, 'http://') || str_starts_with($urlOrPath, 'https://')) {
            // Check if it matches APP_URL/storage
            $storagePrefix = url('/storage').'/';
            if (str_starts_with($urlOrPath, $storagePrefix)) {
                $relative = substr($urlOrPath, strlen($storagePrefix));

                return Storage::disk($disk)->exists($relative) && Storage::disk($disk)->delete($relative);
            }

            return false;
        }

        // Normalize /storage/... or storage/... to relative disk path
        $relative = ltrim($urlOrPath, '/');
        if (str_starts_with($relative, 'storage/')) {
            $relative = substr($relative, 8);
        }

        if (Storage::disk($disk)->exists($relative)) {
            return Storage::disk($disk)->delete($relative);
        }

        return false;
    }

    /**
     * Clean up a list of paths or URLs if a subsequent operation fails.
     *
     * @param  array<string>  $pathsOrUrls
     */
    public static function cleanup(array $pathsOrUrls, ?string $disk = null): void
    {
        foreach ($pathsOrUrls as $item) {
            try {
                static::delete($item, $disk);
            } catch (\Throwable $e) {
                Log::warning('Failed to cleanup file: '.$item.' - '.$e->getMessage());
            }
        }
    }

    private static function supabaseConfig(): array
    {
        $url = rtrim((string) config('services.supabase.url'), '/');
        $key = (string) config('services.supabase.key');
        $bucket = (string) config('services.supabase.bucket', 'images');

        if ($url === '' || $key === '' || $bucket === '') {
            throw new \RuntimeException('Supabase Storage requires SUPABASE_URL, SUPABASE_SERVICE_ROLE_KEY, and SUPABASE_STORAGE_BUCKET.');
        }

        return [$url, $key, $bucket];
    }

    private static function uploadToSupabase(UploadedFile $file, string $path): void
    {
        [$url, $key, $bucket] = static::supabaseConfig();
        $response = Http::withHeaders([
            'apikey' => $key,
            'Authorization' => 'Bearer '.$key,
            'Content-Type' => $file->getMimeType() ?: 'application/octet-stream',
            'x-upsert' => 'false',
        ])->withBody($file->getContent(), $file->getMimeType() ?: 'application/octet-stream')
            ->put($url.'/storage/v1/object/'.rawurlencode($bucket).'/'.static::encodePath($path));

        $response->throw();
    }

    private static function deleteFromSupabase(string $path): bool
    {
        [$url, $key, $bucket] = static::supabaseConfig();
        $response = Http::withHeaders([
            'apikey' => $key,
            'Authorization' => 'Bearer '.$key,
        ])->delete($url.'/storage/v1/object/'.rawurlencode($bucket), ['prefixes' => [$path]]);

        return $response->successful();
    }

    private static function supabaseObjectUrl(string $path): string
    {
        [$url,, $bucket] = static::supabaseConfig();

        return $url.'/storage/v1/object/public/'.rawurlencode($bucket).'/'.static::encodePath($path);
    }

    private static function supabasePathFromUrl(string $value): ?string
    {
        [$url,, $bucket] = static::supabaseConfig();
        $prefix = $url.'/storage/v1/object/public/'.rawurlencode($bucket).'/';
        if (! str_starts_with($value, $prefix)) {
            return null;
        }

        return rawurldecode(substr($value, strlen($prefix)));
    }

    private static function encodePath(string $path): string
    {
        return implode('/', array_map('rawurlencode', explode('/', $path)));
    }
}
