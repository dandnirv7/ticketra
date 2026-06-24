<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Http\File as HttpFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PosterDownloader
{
    private const DISK = 'public';

    private const DIRECTORY = 'posters';

    private const TIMEOUT = 15;

    public function __construct(
        private readonly Client $http,
    ) {}

    
    public function download(string $url, ?string $preferredName = null): ?string
    {
        if (empty($url)) {
            return null;
        }

        try {
            $response = $this->http->get($url, [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0 Safari/537.36',
                    'Accept' => 'image/*',
                ],
                'timeout' => self::TIMEOUT,
                'http_errors' => false,
                
                'sink' => $tempPath = tempnam(sys_get_temp_dir(), 'poster_'),
            ]);

            $status = $response->getStatusCode();
            if ($status !== 200) {
                Log::warning('PosterDownloader: non-200 response', [
                    'url' => $url,
                    'status' => $status,
                ]);
                @unlink($tempPath);
                return null;
            }

            
            $size = filesize($tempPath);
            if ($size === false || $size < 1024) {
                Log::warning('PosterDownloader: file too small', ['url' => $url, 'size' => $size]);
                @unlink($tempPath);
                return null;
            }

            
            $mime = $this->detectMime($tempPath);
            if (! str_starts_with($mime, 'image/')) {
                Log::warning('PosterDownloader: not an image', ['url' => $url, 'mime' => $mime]);
                @unlink($tempPath);
                return null;
            }

            
            $extension = $this->extensionFromMime($mime);
            $filename = $this->buildFilename($preferredName, $extension);

            
            $stored = Storage::disk(self::DISK)->putFileAs(
                self::DIRECTORY,
                new HttpFile($tempPath),
                $filename,
            );

            @unlink($tempPath);

            if ($stored === false) {
                Log::error('PosterDownloader: storage put failed', ['url' => $url]);
                return null;
            }

            Log::info('PosterDownloader: saved', ['url' => $url, 'stored' => $stored]);

            return $stored;
        } catch (\Throwable $e) {
            Log::error('PosterDownloader: error', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    private function detectMime(string $path): string
    {
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo !== false) {
                $mime = finfo_file($finfo, $path);
                
                if (PHP_VERSION_ID < 80500) {
                    finfo_close($finfo);
                }
                if (is_string($mime)) {
                    return $mime;
                }
            }
        }

        
        $mime = @mime_content_type($path);
        return is_string($mime) ? $mime : 'application/octet-stream';
    }

    private function extensionFromMime(string $mime): string
    {
        return match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            default => 'jpg',
        };
    }

    private function buildFilename(?string $preferredName, string $extension): string
    {
        if ($preferredName !== null && ! empty(trim($preferredName))) {
            
            $preferredName = preg_replace('/\.(jpg|jpeg|png|webp|gif)$/i', '', $preferredName);

            
            $safe = preg_replace('/[^a-z0-9\-]+/', '-', mb_strtolower(trim($preferredName)));
            $safe = trim($safe, '-');
            if (! empty($safe)) {
                return $safe . '.' . $extension;
            }
        }

        return uniqid('poster_', true) . '.' . $extension;
    }
}
