<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

#[Signature('images:push-to-supabase {--dry-run : List image files without uploading them} {--check : Verify Supabase bucket access without uploading}')]
#[Description('Upload project image assets to Supabase Storage while preserving their paths')]
class PushProjectImagesToSupabaseCommand extends Command
{
    /** @var array<string, string> */
    private const CONTENT_TYPES = [
        'avif' => 'image/avif',
        'gif' => 'image/gif',
        'ico' => 'image/x-icon',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
        'svg' => 'image/svg+xml',
        'webp' => 'image/webp',
    ];

    public function handle(): int
    {
        $projectRoot = base_path();
        $sourceRoots = ['public/images', 'public/icons', 'docs/screenshots'];
        $files = [];

        foreach ($sourceRoots as $relativeRoot) {
            $root = $projectRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativeRoot);
            if (! is_dir($root)) {
                continue;
            }

            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
            foreach ($iterator as $file) {
                if (! $file instanceof SplFileInfo || ! $file->isFile()) {
                    continue;
                }

                $extension = strtolower($file->getExtension());
                if (! isset(self::CONTENT_TYPES[$extension]) || $file->getSize() === 0) {
                    continue;
                }

                $files[] = [
                    'file' => $file->getPathname(),
                    'path' => str_replace(DIRECTORY_SEPARATOR, '/', substr($file->getPathname(), strlen($projectRoot) + 1)),
                    'type' => self::CONTENT_TYPES[$extension],
                ];
            }
        }

        if ($files === []) {
            $this->warn('No project image assets found.');

            return self::SUCCESS;
        }

        if ($this->option('dry-run')) {
            foreach ($files as $file) {
                $this->line($file['path']);
            }
            $this->info(count($files) . ' image files ready.');

            return self::SUCCESS;
        }

        $baseUrl = rtrim((string) config('services.supabase.url'), '/');
        $key = (string) config('services.supabase.key');
        $bucket = (string) config('services.supabase.bucket');

        if ($baseUrl === '' || $key === '' || $bucket === '') {
            $this->error('Set SUPABASE_URL, SUPABASE_SERVICE_ROLE_KEY, and SUPABASE_STORAGE_BUCKET before uploading.');

            return self::FAILURE;
        }

        $headers = ['apikey' => $key, 'Authorization' => 'Bearer ' . $key];
        $bucketCheck = Http::withHeaders($headers)->get($baseUrl . '/storage/v1/bucket/' . rawurlencode($bucket));
        if (! $bucketCheck->successful()) {
            $this->error('Supabase bucket is not accessible. Check the URL, service role key, and bucket name.');

            return self::FAILURE;
        }

        if ($bucketCheck->json('public') !== true) {
            $this->error('The Supabase bucket must be public because the application uses public image URLs.');

            return self::FAILURE;
        }

        if ($this->option('check')) {
            $this->info('Supabase bucket is accessible and public.');

            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar(count($files));
        $bar->start();
        $uploaded = 0;

        try {
            foreach ($files as $file) {
                $path = 'project-assets/' . $file['path'];
                $response = Http::withHeaders($headers + [
                    'Content-Type' => $file['type'],
                    'x-upsert' => 'true',
                ])->withBody(file_get_contents($file['file']), $file['type'])
                    ->put($baseUrl . '/storage/v1/object/' . rawurlencode($bucket) . '/' . implode('/', array_map('rawurlencode', explode('/', $path))));

                if (! $response->successful()) {
                    $this->newLine(2);
                    $this->error('Upload failed for ' . $file['path'] . ' (HTTP ' . $response->status() . ').');
                    $bar->finish();

                    return self::FAILURE;
                }

                $uploaded++;
                $bar->advance();
            }
        } catch (\Throwable $exception) {
            $this->newLine(2);
            $this->error('Upload stopped after ' . $uploaded . ' files: ' . $exception->getMessage());
            $bar->finish();

            return self::FAILURE;
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('Uploaded ' . $uploaded . ' image files to Supabase bucket "' . $bucket . '" under project-assets/.');

        return self::SUCCESS;
    }
}
