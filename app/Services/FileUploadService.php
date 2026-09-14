<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
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
        string $disk = 'public'
    ): array {
        // Safe filename generation using UUID to prevent collisions and path traversal
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'jpg');
        $safeName = Str::uuid().'.'.$extension;

        // Store file onto specified disk
        $path = $file->storeAs($folder, $safeName, $disk);

        // Delete old file if provided and stored locally
        if (! empty($oldUrlOrPath)) {
            static::delete($oldUrlOrPath, $disk);
        }

        return [
            'url' => '/storage/'.$path,
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
    public static function uploadMultiple(array $files, string $folder, string $disk = 'public'): array
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
    public static function delete(?string $urlOrPath, string $disk = 'public'): bool
    {
        if (empty($urlOrPath)) {
            return false;
        }

        // Never delete placeholders or asset paths
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
    public static function cleanup(array $pathsOrUrls, string $disk = 'public'): void
    {
        foreach ($pathsOrUrls as $item) {
            try {
                static::delete($item, $disk);
            } catch (\Throwable $e) {
                Log::warning('Failed to cleanup file: '.$item.' - '.$e->getMessage());
            }
        }
    }
}
