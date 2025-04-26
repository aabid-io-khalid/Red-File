<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\Log;

class GenreMappingService
{
    private $tmdbService;

    public function __construct(TmdbService $tmdbService)
    {
        $this->tmdbService = $tmdbService;
    }

    public function getGenres(string $type): array
    {
        try {
            $genres = $this->tmdbService->getGenres($type);
            Log::info("Fetched TMDB {$type} genres", ['count' => count($genres)]);
            return $genres;
        } catch (\Exception $e) {
            Log::error("Failed to fetch TMDB {$type} genres", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    public function getGenreMap(string $type): array
    {
        $genres = $this->getGenres($type);
        return collect($genres)->pluck('name', 'id')->toArray();
    }

    public function getGenreCategoryMap(string $type): array
    {
        try {
            $genres = $this->getGenres($type);
            $localCategories = Category::all()->pluck('name', 'id')->toArray();

            $genreCategoryMap = [];
            foreach ($genres as $tmdbGenre) {
                $tmdbName = strtolower(trim(str_replace('& Adventure', '', $tmdbGenre['name'])));
                foreach ($localCategories as $categoryId => $categoryName) {
                    $categoryNameLower = strtolower(trim($categoryName));
                    if ($tmdbName === $categoryNameLower || strpos($categoryNameLower, $tmdbName) !== false) {
                        $genreCategoryMap[$tmdbGenre['id']] = $categoryId;
                    }
                }
            }

            Log::info("Generated genre-category map for {$type}", ['map_count' => count($genreCategoryMap)]);
            return $genreCategoryMap;
        } catch (\Exception $e) {
            Log::error("Failed to generate genre-category map for {$type}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }
}