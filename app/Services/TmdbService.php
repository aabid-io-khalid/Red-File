<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TmdbService
{
    private $apiKey;
    private $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('TMDB_API_KEY');
        $this->baseUrl = 'https://api.themoviedb.org/3';
    }

    public function getMedia(string $type, array $filters = [], int $page = 1): array
    {
        try {
            $queryParams = [
                'api_key' => $this->apiKey,
                'language' => 'en-US',
                'page' => $page,
            ];

            if (!empty($filters['search'])) {
                $queryParams['query'] = $filters['search'];
                $endpoint = "{$this->baseUrl}/search/{$type}";
            } else {
                $endpoint = "{$this->baseUrl}/{$type}/popular";
            }

            if (!empty($filters['genres'])) {
                $queryParams['with_genres'] = implode(',', $filters['genres']);
            }

            Log::info("Fetching TMDB {$type} data", ['endpoint' => $endpoint, 'params' => $queryParams]);

            $response = Http::get($endpoint, $queryParams);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'media' => $data['results'] ?? [],
                    'total_pages' => $data['total_pages'] ?? 1,
                    'total_results' => $data['total_results'] ?? count($data['results'] ?? []),
                ];
            }

            Log::error("Failed to fetch TMDB {$type} data", [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
            return ['media' => [], 'total_pages' => 1, 'total_results' => 0];
        } catch (\Exception $e) {
            Log::error("TMDB API error for {$type}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return ['media' => [], 'total_pages' => 1, 'total_results' => 0];
        }
    }

    public function getMediaDetails(string $type, int $id): ?array
    {
        try {
            $response = Http::get("{$this->baseUrl}/{$type}/{$id}", [
                'api_key' => $this->apiKey,
                'language' => 'en-US',
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error("Failed to fetch TMDB {$type} details", [
                'id' => $id,
                'status' => $response->status(),
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error("TMDB API error for {$type} details", [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    public function getGenres(string $type): array
    {
        try {
            $response = Http::get("{$this->baseUrl}/genre/{$type}/list", [
                'api_key' => $this->apiKey,
                'language' => 'en-US',
            ]);

            if ($response->successful()) {
                return $response->json()['genres'] ?? [];
            }

            Log::error("Failed to fetch TMDB {$type} genres", ['status' => $response->status()]);
            return [];
        } catch (\Exception $e) {
            Log::error("TMDB API error for {$type} genres", ['error' => $e->getMessage()]);
            return [];
        }
    }
}