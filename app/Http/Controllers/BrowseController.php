<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Movie;
use App\Models\TvShow;
use App\Models\Category;
use App\Models\BannedMovie;
use Illuminate\Support\Facades\Log;

class BrowseController extends Controller
{
    protected $baseUrl = 'https://api.themoviedb.org/3';
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('TMDB_API_KEY');
    }

    public function browse(Request $request)
    {
        // Query parameters
        $type = $request->query('type', '');
        $search = $request->query('search', '');
        $genres = $request->query('genres', []);
        $genres = is_array($genres) ? array_filter($genres) : ($genres ? explode(',', $genres) : []);
        $year = $request->query('year', '');
        $rating = $request->query('rating', '');
        $sort = $request->query('sort', 'popularity');
        $order = $request->query('order', 'desc');
        $page = max(1, (int)$request->query('page', 1));
        $perPage = 30;

        // Fetch TMDB genres
        $movieGenresResponse = Http::get("{$this->baseUrl}/genre/movie/list?api_key={$this->apiKey}");
        $tvGenresResponse = Http::get("{$this->baseUrl}/genre/tv/list?api_key={$this->apiKey}");
        $movieGenres = $movieGenresResponse->successful() ? $movieGenresResponse->json()['genres'] : [];
        $tvGenres = $tvGenresResponse->successful() ? $tvGenresResponse->json()['genres'] : [];
        $genresList = $type === 'tv' ? $tvGenres : $movieGenres;
        $genreMap = collect($genresList)->pluck('name', 'id')->toArray();

        // Local categories
        $localCategories = Category::all()->pluck('name', 'id')->toArray();

        // Genre to category mapping
        $genreCategoryMap = [];
        foreach ($genresList as $tmdbGenre) {
            $tmdbName = strtolower(trim(str_replace('& Adventure', '', $tmdbGenre['name'])));
            foreach ($localCategories as $categoryId => $categoryName) {
                $categoryNameLower = strtolower(trim($categoryName));
                if ($tmdbName === $categoryNameLower || strpos($categoryNameLower, $tmdbName) !== false) {
                    $genreCategoryMap[$tmdbGenre['id']] = $categoryId;
                }
            }
        }

        // Map TMDB sort to local columns
        $localSortColumn = $sort;
        if ($sort === 'release_date') {
            $localSortColumn = 'year';
        } elseif ($sort === 'vote_average') {
            $localSortColumn = 'rating';
        } elseif ($sort === 'popularity' || $sort === 'trending') {
            $localSortColumn = 'id';
        }

        // Initialize
        $localMovies = [];
        $localShows = [];
        $tmdbMovies = [];
        $tmdbShows = [];
        $localMoviesCount = 0;
        $localShowsCount = 0;
        $tmdbMoviesTotalPages = 1;
        $tmdbShowsTotalPages = 1;

        // Fetch local movies
        if (!$type || $type === 'movie') {
            $moviesQuery = Movie::query()
                ->leftJoin('categoryables', function ($join) {
                    $join->on('movies.id', '=', 'categoryables.categoryable_id')
                         ->where('categoryables.categoryable_type', '=', 'App\\Models\\Movie');
                });

            if ($search) {
                $moviesQuery->where('title', 'LIKE', "%{$search}%");
            }
            if ($year) {
                $moviesQuery->where('year', $year);
            }
            if ($rating) {
                $moviesQuery->where('rating', '>=', $rating);
            }
            if ($genres) {
                $categoryIds = array_filter(array_map(fn($id) => $genreCategoryMap[$id] ?? null, $genres));
                if ($categoryIds) {
                    $moviesQuery->whereIn('categoryables.category_id', $categoryIds);
                } else {
                    $moviesQuery->whereNull('categoryables.category_id');
                }
            }

            try {
                $moviesQuery->orderBy('movies.' . $localSortColumn, $order)
                            ->select('movies.*')
                            ->distinct();
                $localMovies = $moviesQuery->get()->map(function ($movie) use ($genreCategoryMap) {
                    $categoryIds = $movie->categories ? $movie->categories->pluck('id')->toArray() : [];
                    $genreIds = array_filter(array_map(fn($id) => array_search($id, $genreCategoryMap) ?: null, $categoryIds));
                    return [
                        'id' => (int)$movie->id, // Ensure integer ID
                        'title' => $movie->title ?? 'Untitled',
                        'year' => $movie->year,
                        'rating' => $movie->rating,
                        'genre_ids' => $genreIds,
                        'poster' => $movie->poster,
                        'content_type' => 'movies',
                        'is_local' => true
                    ];
                })->toArray();
                $localMoviesCount = count($localMovies);
            } catch (\Exception $e) {
                Log::error('Local movies query failed', ['error' => $e->getMessage()]);
            }
        }

        // Fetch local shows
        if (!$type || $type === 'tv') {
            $showsQuery = TvShow::query()
                ->leftJoin('categoryables', function ($join) {
                    $join->on('tv_shows.id', '=', 'categoryables.categoryable_id')
                         ->where('categoryables.categoryable_type', '=', 'App\\Models\\TvShow');
                });

            if ($search) {
                $showsQuery->where('title', 'LIKE', "%{$search}%");
            }
            if ($year) {
                $showsQuery->where('year', $year);
            }
            if ($rating) {
                $showsQuery->where('rating', '>=', $rating);
            }
            if ($genres) {
                $categoryIds = array_filter(array_map(fn($id) => $genreCategoryMap[$id] ?? null, $genres));
                if ($categoryIds) {
                    $showsQuery->whereIn('categoryables.category_id', $categoryIds);
                } else {
                    $showsQuery->whereNull('categoryables.category_id');
                }
            }

            try {
                $showsQuery->orderBy('tv_shows.' . $localSortColumn, $order)
                           ->select('tv_shows.*')
                           ->distinct();
                $localShows = $showsQuery->get()->map(function ($show) use ($genreCategoryMap) {
                    $categoryIds = $show->categories ? $show->categories->pluck('id')->toArray() : [];
                    $genreIds = array_filter(array_map(fn($id) => array_search($id, $genreCategoryMap) ?: null, $categoryIds));
                    return [
                        'id' => (int)$show->id, // Ensure integer ID
                        'title' => $show->title ?? 'Untitled',
                        'year' => $show->year,
                        'rating' => $show->rating,
                        'genre_ids' => $genreIds,
                        'poster' => $show->poster,
                        'content_type' => 'shows',
                        'is_local' => true
                    ];
                })->toArray();
                $localShowsCount = count($localShows);
            } catch (\Exception $e) {
                Log::error('Local shows query failed', ['error' => $e->getMessage()]);
            }
        }

        // Fetch TMDB movies
        $tmdbPage = $page;
        $remaining = $perPage;
        $offset = ($page - 1) * $perPage;
        $totalLocalCount = $localMoviesCount + $localShowsCount;
        $localRemaining = max(0, $totalLocalCount - $offset);
        $tmdbNeeded = max(0, $remaining - min($localRemaining, $perPage));

        if ($tmdbNeeded > 0 && (!$type || $type === 'movie')) {
            $apiUrl = "{$this->baseUrl}/discover/movie?api_key={$this->apiKey}&page={$tmdbPage}";
            if ($search) {
                $apiUrl = "{$this->baseUrl}/search/movie?api_key={$this->apiKey}&query=" . urlencode($search) . "&page={$tmdbPage}";
            } elseif ($sort === 'trending') {
                $apiUrl = "{$this->baseUrl}/trending/movie/week?api_key={$this->apiKey}&page={$tmdbPage}";
            } elseif ($request->query('filter') === 'upcoming') {
                $apiUrl = "{$this->baseUrl}/movie/upcoming?api_key={$this->apiKey}&page={$tmdbPage}";
            } else {
                if ($genres) {
                    $apiUrl .= "&with_genres=" . implode(',', array_map('intval', $genres));
                }
                if ($year) {
                    $apiUrl .= "&primary_release_year={$year}";
                }
                if ($rating) {
                    $apiUrl .= "&vote_average.gte={$rating}";
                }
                if ($sort && $sort !== 'trending') {
                    $apiUrl .= "&sort_by={$sort}.{$order}";
                }
            }

            $response = Http::get($apiUrl);
            if ($response->successful()) {
                $tmdbData = $response->json();
                $tmdbMovies = array_map(function ($movie) {
                    return array_merge($movie, [
                        'id' => (int)$movie['id'], // Ensure integer ID
                        'content_type' => 'movies',
                        'is_local' => false
                    ]);
                }, $tmdbData['results'] ?? []);
                $tmdbMoviesTotalPages = $tmdbData['total_pages'] ?? 1;
                Log::debug('TMDB movies', ['page' => $tmdbPage, 'count' => count($tmdbMovies)]);
            } else {
                Log::error('TMDB movies failed', ['url' => $apiUrl]);
            }
        }

        // Fetch TMDB shows
        if ($tmdbNeeded > 0 && (!$type || $type === 'tv')) {
            $apiUrl = "{$this->baseUrl}/discover/tv?api_key={$this->apiKey}&page={$tmdbPage}";
            if ($search) {
                $apiUrl = "{$this->baseUrl}/search/tv?api_key={$this->apiKey}&query=" . urlencode($search) . "&page={$tmdbPage}";
            } elseif ($sort === 'trending') {
                $apiUrl = "{$this->baseUrl}/trending/tv/week?api_key={$this->apiKey}&page={$tmdbPage}";
            } else {
                if ($genres) {
                    $apiUrl .= "&with_genres=" . implode(',', array_map('intval', $genres));
                }
                if ($year) {
                    $apiUrl .= "&first_air_date_year={$year}";
                }
                if ($rating) {
                    $apiUrl .= "&vote_average.gte={$rating}";
                }
                if ($sort && $sort !== 'trending') {
                    $apiUrl .= "&sort_by={$sort}.{$order}";
                }
            }

            $response = Http::get($apiUrl);
            if ($response->successful()) {
                $tmdbData = $response->json();
                $tmdbShows = array_map(function ($show) {
                    return array_merge($show, [
                        'id' => (int)$show['id'], // Ensure integer ID
                        'content_type' => 'shows',
                        'is_local' => false
                    ]);
                }, $tmdbData['results'] ?? []);
                $tmdbShowsTotalPages = $tmdbData['total_pages'] ?? 1;
                Log::debug('TMDB shows', ['page' => $tmdbPage, 'count' => count($tmdbShows)]);
            } else {
                Log::error('TMDB shows failed', ['url' => $apiUrl]);
            }
        }

        // Ban filtering
        $bannedMovies = BannedMovie::all();
        $bannedTmdbIds = $bannedMovies->pluck('tmdb_id')->filter()->toArray();
        $bannedLocalIds = $bannedMovies->pluck('movie_id')->filter()->toArray();

        $filteredLocalMovies = array_filter($localMovies, fn($movie) => !in_array($movie['id'] ?? null, $bannedLocalIds));
        $filteredLocalShows = array_filter($localShows, fn($show) => !in_array($show['id'] ?? null, $bannedLocalIds));
        $filteredTmdbMovies = array_filter($tmdbMovies, fn($movie) => !in_array($movie['id'] ?? null, $bannedTmdbIds));
        $filteredTmdbShows = array_filter($tmdbShows, fn($show) => !in_array($show['id'] ?? null, $bannedTmdbIds));

        // Combine local content
        $localContent = [];
        if (!$type || $type === 'movie') {
            $localContent = array_merge($localContent, $filteredLocalMovies);
        }
        if (!$type || $type === 'tv') {
            $localContent = array_merge($localContent, $filteredLocalShows);
        }

        // Paginate
        $localSlice = array_slice($localContent, $offset, $perPage);
        $tmdbSlice = [];
        if ($tmdbNeeded > 0) {
            $tmdbMoviesSlice = (!$type || $type === 'movie') ? array_slice($filteredTmdbMovies, 0, $tmdbNeeded) : [];
            $tmdbShowsSlice = (!$type || $type === 'tv') ? array_slice($filteredTmdbShows, 0, $tmdbNeeded - count($tmdbMoviesSlice)) : [];
            $tmdbSlice = array_merge($tmdbMoviesSlice, $tmdbShowsSlice);
        }
        $content = array_merge($localSlice, $tmdbSlice);

        // Total pages
        $totalItems = max($totalLocalCount, max($tmdbMoviesTotalPages, $tmdbShowsTotalPages) * $perPage);
        $totalPages = ceil($totalItems / $perPage);

        // Log content for debugging
        Log::debug('Browse Content', [
            'page' => $page,
            'count' => count($content),
            'local_movies' => array_map(fn($m) => ['id' => $m['id'], 'title' => $m['title']], $filteredLocalMovies),
            'local_shows' => array_map(fn($s) => ['id' => $s['id'], 'title' => $s['title']], $filteredLocalShows),
            'tmdb_movies' => array_map(fn($m) => ['id' => $m['id'], 'title' => $m['title'] ?? ''], $filteredTmdbMovies),
            'tmdb_shows' => array_map(fn($s) => ['id' => $s['id'], 'name' => $s['name'] ?? ''], $filteredTmdbShows)
        ]);

        Log::info('Browse fetched', [
            'total_pages' => $totalPages,
            'filters' => compact('type', 'search', 'genres', 'year', 'rating', 'sort', 'order')
        ]);

        return view('Front-office.browse', [
            'content' => $content,
            'genres' => $genresList,
            'genreMap' => $genreMap,
            'selectedGenres' => $genres,
            'search' => is_array($search) ? '' : $search,
            'year' => is_array($year) ? '' : $year,
            'rating' => is_array($rating) ? '' : $rating,
            'sort' => $sort,
            'order' => $order,
            'page' => $page,
            'totalPages' => $totalPages,
            'type' => $type
        ]);
    }
}