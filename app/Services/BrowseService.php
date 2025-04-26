<?php

namespace App\Services;

use App\Models\Movie;
use App\Models\TvShow;
use App\Models\BannedMovie;
use App\Models\BannedTvShow;
use Illuminate\Support\Facades\Log;
use Exception;

class BrowseService
{
    public function getLocalContent(
        string $type,
        array $filters,
        array $genreCategoryMap,
        string $sort,
        string $order
    ): array {
        try {
            $result = ['movies' => [], 'shows' => [], 'movies_count' => 0, 'shows_count' => 0];
            $localSortColumn = $this->mapSortColumn($sort);

            if (!$type || $type === 'movie') {
                $moviesQuery = $this->buildLocalQuery(Movie::query(), $filters, $genreCategoryMap);
                $moviesQuery->orderBy('movies.' . $localSortColumn, $order)
                            ->select('movies.*')
                            ->distinct();
                $result['movies'] = $moviesQuery->get()->map(function ($movie) use ($genreCategoryMap) {
                    $categoryIds = $movie->categories ? $movie->categories->pluck('id')->toArray() : [];
                    $genreIds = array_filter(array_map(fn($id) => array_search($id, $genreCategoryMap) ?: null, $categoryIds));
                    return [
                        'id' => (int)$movie->id,
                        'title' => $movie->title ?? 'Untitled',
                        'year' => $movie->year,
                        'rating' => $movie->rating,
                        'genre_ids' => $genreIds,
                        'poster' => $movie->poster,
                        'content_type' => 'movies',
                        'is_local' => true
                    ];
                })->toArray();
                $result['movies_count'] = count($result['movies']);
                Log::info('Fetched local movies', ['count' => $result['movies_count']]);
            }

            if (!$type || $type === 'tv') {
                $showsQuery = $this->buildLocalQuery(TvShow::query(), $filters, $genreCategoryMap);
                $showsQuery->orderBy('tv_shows.' . $localSortColumn, $order)
                           ->select('tv_shows.*')
                           ->distinct();
                $result['shows'] = $showsQuery->get()->map(function ($show) use ($genreCategoryMap) {
                    $categoryIds = $show->categories ? $show->categories->pluck('id')->toArray() : [];
                    $genreIds = array_filter(array_map(fn($id) => array_search($id, $genreCategoryMap) ?: null, $categoryIds));
                    return [
                        'id' => (int)$show->id,
                        'title' => $show->title ?? 'Untitled',
                        'year' => $show->year,
                        'rating' => $show->rating,
                        'genre_ids' => $genreIds,
                        'poster' => $show->poster,
                        'content_type' => 'shows',
                        'is_local' => true
                    ];
                })->toArray();
                $result['shows_count'] = count($result['shows']);
                Log::info('Fetched local shows', ['count' => $result['shows_count']]);
            }

            return $result;
        } catch (Exception $e) {
            Log::error("Local {$type} query failed", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function getBannedIds(): array
    {
        try {
            $bannedMovies = BannedMovie::all();
            $bannedShows = BannedTvShow::all();
            $bannedTmdbMovieIds = $bannedMovies->pluck('tmdb_id')->filter()->toArray();
            $bannedLocalMovieIds = $bannedMovies->pluck('movie_id')->filter()->toArray();
            $bannedTmdbShowIds = $bannedShows->pluck('tmdb_id')->filter()->toArray();
            $bannedLocalShowIds = $bannedShows->pluck('tv_show_id')->filter()->toArray();

            Log::info('Fetched banned IDs', [
                'tmdb_movies' => count($bannedTmdbMovieIds),
                'local_movies' => count($bannedLocalMovieIds),
                'tmdb_shows' => count($bannedTmdbShowIds),
                'local_shows' => count($bannedLocalShowIds)
            ]);

            return [
                'tmdb_movies' => $bannedTmdbMovieIds,
                'local_movies' => $bannedLocalMovieIds,
                'tmdb_shows' => $bannedTmdbShowIds,
                'local_shows' => $bannedLocalShowIds
            ];
        } catch (Exception $e) {
            Log::error('Failed to fetch banned IDs', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function combineContent(
        string $type,
        array $localMovies,
        array $localShows,
        array $tmdbMovies,
        array $tmdbShows,
        array $bannedIds,
        int $page,
        int $perPage
    ): array {
        try {
            $filteredLocalMovies = array_filter($localMovies, fn($movie) => !in_array($movie['id'] ?? null, $bannedIds['local_movies']));
            $filteredLocalShows = array_filter($localShows, fn($show) => !in_array($show['id'] ?? null, $bannedIds['local_shows']));
            $filteredTmdbMovies = array_filter($tmdbMovies, fn($movie) => !in_array($movie['id'] ?? null, $bannedIds['tmdb_movies']));
            $filteredTmdbShows = array_filter($tmdbShows, fn($show) => !in_array($show['id'] ?? null, $bannedIds['tmdb_shows']));

            $localContent = [];
            if (!$type || $type === 'movie') {
                $localContent = array_merge($localContent, $filteredLocalMovies);
            }
            if (!$type || $type === 'tv') {
                $localContent = array_merge($localContent, $filteredLocalShows);
            }

            $offset = ($page - 1) * $perPage;
            $totalLocalCount = count($filteredLocalMovies) + count($filteredLocalShows);
            $localRemaining = max(0, $totalLocalCount - $offset);
            $tmdbNeeded = max(0, $perPage - min($localRemaining, $perPage));

            $localSlice = array_slice($localContent, $offset, $perPage);
            $tmdbSlice = [];
            if ($tmdbNeeded > 0) {
                $tmdbMoviesSlice = (!$type || $type === 'movie') ? array_slice($filteredTmdbMovies, 0, $tmdbNeeded) : [];
                $tmdbShowsSlice = (!$type || $type === 'tv') ? array_slice($filteredTmdbShows, 0, $tmdbNeeded - count($tmdbMoviesSlice)) : [];
                $tmdbSlice = array_merge($tmdbMoviesSlice, $tmdbShowsSlice);
            }

            $content = array_merge($localSlice, $tmdbSlice);
            $totalItems = max($totalLocalCount, max($tmdbMovies['total_pages'] ?? 1, $tmdbShows['total_pages'] ?? 1) * $perPage);
            $totalPages = ceil($totalItems / $perPage);

            Log::info('Combined browse content', [
                'page' => $page,
                'content_count' => count($content),
                'total_pages' => $totalPages,
                'local_movies_count' => count($filteredLocalMovies),
                'local_shows_count' => count($filteredLocalShows),
                'tmdb_movies_count' => count($filteredTmdbMovies),
                'tmdb_shows_count' => count($filteredTmdbShows)
            ]);

            return [
                'content' => $content,
                'total_pages' => $totalPages
            ];
        } catch (Exception $e) {
            Log::error('Failed to combine browse content', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function buildLocalQuery($query, array $filters, array $genreCategoryMap)
    {
        $query->leftJoin('categoryables', function ($join) use ($query) {
            $table = $query->getModel()->getTable();
            $model = get_class($query->getModel());
            $join->on("{$table}.id", '=', 'categoryables.categoryable_id')
                 ->where('categoryables.categoryable_type', '=', $model);
        });

        if (!empty($filters['search'])) {
            $query->where('title', 'LIKE', "%{$filters['search']}%");
        }
        if (!empty($filters['year'])) {
            $query->where('year', $filters['year']);
        }
        if (!empty($filters['rating'])) {
            $query->where('rating', '>=', $filters['rating']);
        }
        if (!empty($filters['genres'])) {
            $categoryIds = array_filter(array_map(fn($id) => $genreCategoryMap[$id] ?? null, $filters['genres']));
            if ($categoryIds) {
                $query->whereIn('categoryables.category_id', $categoryIds);
            } else {
                $query->whereNull('categoryables.category_id');
            }
        }

        return $query;
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