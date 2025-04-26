<?php

namespace App\Services;

use App\Models\Movie;
use App\Models\TvShow;
use App\Models\Category;
use App\Models\Categoryable;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class LocalMediaService
{
    public function getGenreCategoryMap(array $tmdbGenres): array
    {
        $localCategories = Category::all()->pluck('name', 'id')->toArray();
        $genreCategoryMap = [];

        foreach ($tmdbGenres as $tmdbGenre) {
            $tmdbName = strtolower(trim(str_replace('& Adventure', '', $tmdbGenre['name'])));
            foreach ($localCategories as $categoryId => $categoryName) {
                $categoryNameLower = strtolower(trim($categoryName));
                if ($tmdbName === $categoryNameLower || strpos($categoryNameLower, $tmdbName) !== false) {
                    $genreCategoryMap[$tmdbGenre['id']] = $categoryId;
                }
            }
        }

        return $genreCategoryMap;
    }

    public function getLocalMedia(string $type, array $filters, array $genreCategoryMap): array
    {
        $model = $this->getModel($type);
        $search = $filters['search'] ?? '';
        $genres = $filters['genres'] ?? [];
        $year = $filters['year'] ?? '';
        $rating = $filters['rating'] ?? '';
        $sort = $filters['sort'] ?? 'popularity';
        $order = $filters['order'] ?? 'desc';

        $localSortColumn = $this->mapSortColumn($sort);

        $query = $model::query()
            ->leftJoin('categoryables', function ($join) use ($type) {
                $join->on("{$type}s.id", '=', 'categoryables.categoryable_id')
                     ->where('categoryables.categoryable_type', '=', "App\\Models\\" . ucfirst($type));
            });

        if ($search) {
            $query->where('title', 'LIKE', "%{$search}%");
        }
        if ($year) {
            $query->where('year', $year);
        }
        if ($rating) {
            $query->where('rating', '>=', $rating);
        }
        if ($genres) {
            $categoryIds = array_filter(array_map(fn($id) => $genreCategoryMap[$id] ?? null, $genres));
            if ($categoryIds) {
                $query->whereIn('categoryables.category_id', $categoryIds);
            } else {
                $query->whereNull('categoryables.category_id');
            }
        }

        try {
            $media = $query->orderBy("{$type}s.{$localSortColumn}", $order)
                           ->select("{$type}s.*")
                           ->distinct()
                           ->get()
                           ->map(function ($item) use ($genreCategoryMap, $type) {
                               $categoryIds = $item->categories ? $item->categories->pluck('id')->toArray() : [];
                               $genreIds = array_filter(array_map(fn($id) => array_search($id, $genreCategoryMap) ?: null, $categoryIds));
                               return [
                                   'id' => (int)$item->id,
                                   'title' => $item->title ?? 'Untitled',
                                   'year' => $item->year,
                                   'rating' => $item->rating,
                                   'genre_ids' => $genreIds,
                                   'poster' => $item->poster,
                                   'content_type' => $type === 'movie' ? 'movies' : 'shows',
                                   'is_local' => true
                               ];
                           })->toArray();
            return $media;
        } catch (\Exception $e) {
            Log::error("Local {$type} query failed", ['error' => $e->getMessage()]);
            return [];
        }
    }

    public function getLocalMediaDetails(string $type, int $id, TmdbService $tmdbService): array
    {
        try {
            $model = $this->getModel($type);
            $item = $model::find($id);
            $media = [
                'id' => $id,
                'title' => 'Untitled',
                'year' => null,
                'rating' => null,
                'poster' => null,
                'description' => null,
                'type' => $type,
                'number_of_seasons' => $type === 'tv' ? 0 : null
            ];
            $noInfoAvailable = true;

            if ($item) {
                $media = [
                    'id' => $item->id,
                    'title' => $item->title ?? 'Untitled',
                    'year' => $item->year,
                    'rating' => $item->rating,
                    'poster' => $item->poster,
                    'description' => $item->description ?? null,
                    'type' => $type,
                    'number_of_seasons' => $type === 'tv' ? 0 : null
                ];
                $noInfoAvailable = false;

                if ($item->tmdb_id) {
                    $tmdbData = $tmdbService->getMediaDetails($type, $item->tmdb_id);
                    if ($tmdbData) {
                        $media = array_merge($media, [
                            'title' => $tmdbData['title'] ?? $tmdbData['name'] ?? $media['title'],
                            'year' => isset($tmdbData['release_date']) ? date('Y', strtotime($tmdbData['release_date'])) :
                                      (isset($tmdbData['first_air_date']) ? date('Y', strtotime($tmdbData['first_air_date'])) : $media['year']),
                            'rating' => $tmdbData['vote_average'] ?? $media['rating'],
                            'poster' => isset($tmdbData['poster_path']) ? 'https://image.tmdb.org/t/p/original' . $tmdbData['poster_path'] : $media['poster'],
                            'description' => $tmdbData['overview'] ?? $media['description'],
                            'number_of_seasons' => $tmdbData['number_of_seasons'] ?? $media['number_of_seasons']
                        ]);
                        $noInfoAvailable = false;
                    }
                }
            } else {
                Log::warning("Local {$type} not found", ['id' => $id]);
            }

            return [
                'media' => $media,
                'noInfoAvailable' => $noInfoAvailable
            ];
        } catch (\Exception $e) {
            Log::error("Local {$type} details error", ['id' => $id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    private function getModel(string $type)
    {
        if ($type === 'movie') {
            return Movie::class;
        } elseif ($type === 'tv') {
            return TvShow::class;
        }
        throw new InvalidArgumentException("Invalid media type: {$type}");
    }

    private function mapSortColumn(string $sort): string
    {
        if ($sort === 'release_date') {
            return 'year';
        } elseif ($sort === 'vote_average') {
            return 'rating';
        } elseif ($sort === 'popularity' || $sort === 'trending') {
            return 'id';
        }
        return $sort;
    }
}