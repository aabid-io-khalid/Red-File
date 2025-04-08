<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Movie;
use App\Models\Category;
use App\Models\BannedMovie;
use App\Models\UserMovieList;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class MovieController extends Controller
{
    protected $baseUrl = 'https://api.themoviedb.org/3';
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('TMDB_API_KEY');
    }

    public function movies(Request $request)
    {
        // Retrieve query parameters
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
        $genresResponse = Http::get("{$this->baseUrl}/genre/movie/list?api_key={$this->apiKey}");
        $genresList = $genresResponse->successful() ? $genresResponse->json()['genres'] : [];
        $genreMap = collect($genresList)->pluck('name', 'id')->toArray();

        // Fetch local categories
        $localCategories = Category::all()->pluck('name', 'id')->toArray();

        // Dynamic genre to category mapping
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
        Log::debug('Genre to category mapping', ['map' => $genreCategoryMap]);

        // Map TMDB sort to local columns
        $localSortColumn = $sort;
        if ($sort === 'release_date') {
            $localSortColumn = 'year';
        } elseif ($sort === 'vote_average') {
            $localSortColumn = 'rating';
        } elseif ($sort === 'popularity' || $sort === 'trending') {
            $localSortColumn = 'id';
        }

        // Fetch local movies with categories
        $localMoviesQuery = Movie::query()
            ->leftJoin('categoryables', function ($join) {
                $join->on('movies.id', '=', 'categoryables.categoryable_id')
                     ->where('categoryables.categoryable_type', '=', 'App\\Models\\Movie');
            });

        if ($search) {
            $localMoviesQuery->where('title', 'LIKE', "%{$search}%");
        }
        if ($year) {
            $localMoviesQuery->where('year', $year);
        }
        if ($rating) {
            $localMoviesQuery->where('rating', '>=', $rating);
        }
        if ($genres) {
            $categoryIds = array_filter(array_map(function ($genreId) use ($genreCategoryMap) {
                return $genreCategoryMap[$genreId] ?? null;
            }, $genres));
            if ($categoryIds) {
                $localMoviesQuery->whereIn('categoryables.category_id', $categoryIds);
            } else {
                $localMoviesQuery->whereNull('categoryables.category_id');
            }
        }

        try {
            $localMoviesQuery->orderBy('movies.' . $localSortColumn, $order)
                             ->select('movies.*')
                             ->distinct();
            $localMovies = $localMoviesQuery->get()->map(function ($movie) use ($genreCategoryMap) {
                $categoryIds = $movie->categories ? $movie->categories->pluck('id')->toArray() : [];
                $genreIds = array_filter(array_map(function ($categoryId) use ($genreCategoryMap) {
                    return array_search($categoryId, $genreCategoryMap) ?: null;
                }, $categoryIds));
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
        } catch (\Exception $e) {
            Log::error('Local movies query failed', ['error' => $e->getMessage()]);
            $localMovies = [];
        }

        $localCount = count($localMovies);
        Log::debug('Local movies', ['count' => $localCount, 'sample' => array_slice($localMovies, 0, 2)]);

        // Fetch TMDB movies
        $tmdbMovies = [];
        $tmdbTotalPages = 1;
        $tmdbPage = $page;
        $remaining = $perPage;
        $offset = ($page - 1) * $perPage;
        $localRemaining = max(0, $localCount - $offset);
        $tmdbNeeded = max(0, $remaining - min($localRemaining, $perPage));

        if ($tmdbNeeded > 0) {
            $apiUrl = "{$this->baseUrl}/discover/movie?api_key={$this->apiKey}&page={$tmdbPage}";
            if ($search) {
                $apiUrl = "{$this->baseUrl}/search/movie?api_key={$this->apiKey}&query=" . urlencode($search) . "&page={$tmdbPage}";
            } elseif ($sort === 'trending') {
                $apiUrl = "{$this->baseUrl}/trending/movie/week?api_key={$this->apiKey}&page={$tmdbPage}";
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

            try {
                $response = Http::get($apiUrl);
                if ($response->successful()) {
                    $tmdbData = $response->json();
                    $tmdbMovies = array_map(function ($movie) {
                        return array_merge($movie, [
                            'id' => (int)$movie['id'],
                            'content_type' => 'movies',
                            'is_local' => false
                        ]);
                    }, $tmdbData['results'] ?? []);
                    $tmdbTotalPages = $tmdbData['total_pages'] ?? 1;
                    Log::debug('TMDB movies', ['page' => $tmdbPage, 'count' => count($tmdbMovies), 'sample' => array_slice($tmdbMovies, 0, 2)]);
                } else {
                    Log::error('TMDB API request failed', ['url' => $apiUrl, 'status' => $response->status()]);
                }
            } catch (\Exception $e) {
                Log::error('TMDB API exception', ['url' => $apiUrl, 'error' => $e->getMessage()]);
            }
        }

        // Ban filtering
        $bannedMovies = BannedMovie::all();
        $bannedTmdbIds = $bannedMovies->pluck('tmdb_id')->filter()->toArray();
        $bannedLocalIds = $bannedMovies->pluck('movie_id')->filter()->toArray();

        $filteredTmdbMovies = array_filter($tmdbMovies, fn($movie) => !in_array($movie['id'] ?? null, $bannedTmdbIds));
        $filteredLocalMovies = array_filter($localMovies, fn($movie) => !in_array($movie['id'] ?? null, $bannedLocalIds));

        // Paginate combined movies
        $localSlice = array_slice($filteredLocalMovies, $offset, $perPage);
        $tmdbSlice = $tmdbNeeded > 0 ? array_slice($filteredTmdbMovies, 0, $tmdbNeeded) : [];
        $movies = array_merge($localSlice, $tmdbSlice);

        // Calculate total pages
        $totalItems = max($localCount, $tmdbTotalPages * $perPage);
        $totalPages = ceil($totalItems / $perPage);

        // Log combined movies
        Log::debug('Combined movies', [
            'page' => $page,
            'count' => count($movies),
            'local_count' => count($localSlice),
            'tmdb_count' => count($tmdbSlice),
            'sample' => array_slice($movies, 0, 2)
        ]);

        Log::info('Movies fetched', [
            'tmdb_count' => count($filteredTmdbMovies),
            'local_count' => count($filteredLocalMovies),
            'banned_tmdb' => $bannedTmdbIds,
            'banned_local' => $bannedLocalIds,
            'page' => $page,
            'total_pages' => $totalPages,
            'filters' => compact('search', 'genres', 'year', 'rating', 'sort', 'order')
        ]);

        return view('Front-office.movies', [
            'content' => $movies,
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
        ]);
    }

    public function createMovie(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'year' => 'nullable|integer',
            'rating' => 'nullable|numeric',
            'poster' => 'nullable|string',
        ]);

        Movie::create($request->all());

        return redirect()->back()->with('success', 'Movie created successfully.');
    }

    public function banMovie(Request $request)
    {
        $request->validate([
            'tmdb_id' => 'required|string|unique:banned_movies,tmdb_id',
        ]);

        BannedMovie::create(['tmdb_id' => $request->tmdb_id]);

        return redirect()->back()->with('success', 'Movie banned successfully.');
    }

    public function showMovieDetails($id)
    {
        try {
            $url = "{$this->baseUrl}/movie/{$id}?api_key={$this->apiKey}&append_to_response=credits,videos,reviews,similar,keywords";
            $response = Http::get($url);

            if ($response->successful()) {
                $movieData = $response->json();
                $user = auth()->user();
                $isInList = false;

                if ($user) {
                    $isInList = UserMovieList::where('user_id', $user->id)
                        ->where('tmdb_id', $id)
                        ->exists();
                }

                $downloadLink = $this->fetchYTSDownloadLink($movieData['title']);

                return view('Front-office.details', compact('movieData', 'downloadLink', 'isInList'));
            }

            Log::warning('TMDB Movie not found', ['id' => $id]);
            return redirect()->route('movies')->with('error', 'Movie not found.');
        } catch (\Exception $e) {
            Log::error('TMDB Movie Details Error', ['id' => $id, 'error' => $e->getMessage()]);
            return view('errors.500');
        }
    }

    private function fetchYTSDownloadLink($movieTitle)
    {
        try {
            $client = new Client();
            $apiUrl = "https://yts.mx/api/v2/list_movies.json?query_term=" . urlencode($movieTitle) . "&limit=1";
            $response = $client->request('GET', $apiUrl);
            $data = json_decode($response->getBody(), true);

            if (isset($data['data']['movies']) && count($data['data']['movies']) > 0) {
                $movie = $data['data']['movies'][0];
                if (isset($movie['torrents'][0]['url'])) {
                    return $movie['torrents'][0]['url'];
                }
            }
        } catch (\Exception $e) {
            Log::error('Error fetching download link:', ['error' => $e->getMessage()]);
        }

        return null;
    }

    public function fetchDownloadLink(Request $request)
    {
        if (Gate::denies('download-content')) {
            return response()->json(['error' => 'You need a premium subscription to download content.'], 403);
        }

        $movieTitle = $request->input('title');
        $downloadLink = $this->fetchYTSDownloadLink($movieTitle);

        return response()->json(['download_link' => $downloadLink]);
    }

    private function sanitizeTitle($title)
    {
        return preg_replace('/[^a-zA-Z0-9\s]/', '', $title);
    }

    public function localMovieDetails($id)
    {
        try {
            Log::debug('Local Movie Details Called', ['id' => $id]);
            $noInfoAvailable = true;
            $media = [
                'id' => $id,
                'title' => 'Untitled',
                'year' => null,
                'rating' => null,
                'poster' => null,
                'description' => null,
                'type' => 'movie',
            ];
            $isInList = false;

            $localMovie = Movie::find($id);

            if ($localMovie) {
                $media = [
                    'id' => $localMovie->id,
                    'title' => $localMovie->title ?? 'Untitled',
                    'year' => $localMovie->year,
                    'rating' => $localMovie->rating,
                    'poster' => $localMovie->poster,
                    'description' => $localMovie->description ?? null,
                    'type' => 'movie',
                ];
                $noInfoAvailable = false;
                if ($localMovie->tmdb_id) {
                    $response = Http::get("{$this->baseUrl}/movie/{$localMovie->tmdb_id}?api_key={$this->apiKey}&append_to_response=credits,videos,reviews,similar");
                    Log::debug('TMDB API Response', [
                        'tmdb_id' => $localMovie->tmdb_id,
                        'status' => $response->status(),
                        'data' => $response->successful() ? $response->json() : null,
                    ]);
                    if ($response->successful()) {
                        $tmdbData = $response->json();
                        $media = array_merge($media, [
                            'title' => $tmdbData['title'] ?? $media['title'],
                            'year' => isset($tmdbData['release_date']) ? date('Y', strtotime($tmdbData['release_date'])) : $media['year'],
                            'rating' => $tmdbData['vote_average'] ?? $media['rating'],
                            'poster' => isset($tmdbData['poster_path']) ? 'https://image.tmdb.org/t/p/original' . $tmdbData['poster_path'] : $media['poster'],
                            'description' => $tmdbData['overview'] ?? $media['description'],
                        ]);
                        $noInfoAvailable = false;
                    }
                }
            } else {
                Log::warning('Local movie not found', ['id' => $id]);
            }

            Log::debug('Local Movie Details Data', [
                'id' => $id,
                'media' => $media,
                'noInfoAvailable' => $noInfoAvailable,
                'isInList' => $isInList
            ]);

            return view('Front-office.local', compact('media', 'noInfoAvailable', 'isInList'));
        } catch (\Exception $e) {
            Log::error('Local Movie Details Error', ['id' => $id, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return view('errors.500');
        }
    }
}