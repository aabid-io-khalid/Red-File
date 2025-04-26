<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Support\Facades\Log;
use Exception;

class CloudinaryService
{
    private $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key' => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
            'url' => [
                'secure' => true,
            ]
        ]);
    }

    public function uploadPoster($file, string $publicIdPrefix = 'series'): string
    {
        try {
            Log::info('Uploading file to Cloudinary', [
                'path' => $file->getRealPath(),
                'size' => $file->getSize(),
                'mime' => $file->getMimeType(),
                'name' => $file->getClientOriginalName()
            ]);

            $uploadResult = $this->cloudinary->uploadApi()->upload(
                $file->getRealPath(),
                [
                    'folder' => 'series',
                    'public_id' => "{$publicIdPrefix}_" . time(),
                    'resource_type' => 'image'
                ]
            );

            Log::info('Cloudinary upload response', ['response' => $uploadResult]);

            if (!isset($uploadResult['secure_url'])) {
                throw new Exception('Cloudinary returned an invalid or empty response');
            }

            return $uploadResult['secure_url'];
        } catch (Exception $e) {
            Log::error('Cloudinary upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $file->getClientOriginalName(),
                'size' => $file->getSize()
            ]);
            throw $e;
        }
    }

    public function deletePoster(?string $posterUrl): void
    {
        if (!$posterUrl) {
            return;
        }

        try {
            $publicId = pathinfo($posterUrl, PATHINFO_FILENAME);
            $this->cloudinary->uploadApi()->destroy($publicId, ['resource_type' => 'image']);
            Log::info('Poster deleted from Cloudinary', ['public_id' => $publicId]);
        } catch (Exception $e) {
            Log::error('Failed to delete poster from Cloudinary', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'poster_url' => $posterUrl
            ]);
        }
    }
}