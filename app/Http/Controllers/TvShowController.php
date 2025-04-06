<?php

namespace App\Http\Controllers;

use App\Models\TvShow;
use GuzzleHttp\Client;
use App\Models\Category;
use App\Models\BannedTvShow;
use App\Models\Categoryable;
use Illuminate\Http\Request;
use App\Models\UserTvShowList;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use App\Models\UserMovieList;

class TvShowController extends Controller
{
    private $apiKey;
    private $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('TMDB_API_KEY');
        $this->baseUrl = 'https://api.themoviedb.org/3';
    }

    public function index()
    {
        return view('Front-office.home');
    }

    public function shows(Request $request)
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
        $genresResponse = Http::get("{$this->baseUrl}/genre/tv/list?api_key={$this->apiKey}");
        $genresList = $genresResponse->successful() ? $genresResponse->json()['genres'] : [];
        $genreMap = collect($genresList)->pluck('name', 'id')->toArray();

        // Fetch local categories
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

        // Fetch local shows
        $localTvShowsQuery = TvShow::query()
            ->leftJoin('categoryables', function ($join) {
                $join->on('tv_shows.id', '=', 'categoryables.categoryable_id')
                     ->where('categoryables.categoryable_type', '=', 'App\\Models\\TvShow');
            });

        if ($search) {
            $localTvShowsQuery->where('title', 'LIKE', "%{$search}%");
        }
        if ($year) {
            $localTvShowsQuery->where('year', $year);
        }
        if ($rating) {
            $localTvShowsQuery->where('rating', '>=', $rating);
        }
        if ($genres) {
            $categoryIds = array_filter(array_map(fn($id) => $genreCategoryMap[$id] ?? null, $genres));
            if ($categoryIds) {
                $localTvShowsQuery->whereIn('categoryables.category_id', $categoryIds);
            } else {
                $localTvShowsQuery->whereNull('categoryables.category_id');
            }
        }

        try {
            $localTvShowsQuery->orderBy('tv_shows.' . $localSortColumn, $order)
                              ->select('tv_shows.*')
                              ->distinct();
            $localTvShows = $localTvShowsQuery->get()->map(function ($show) use ($genreCategoryMap) {
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
        } catch (\Exception $e) {
            Log::error('Local TV shows query failed', ['error' => $e->getMessage()]);
            $localTvShows = [];
        }

        // Fetch TMDB shows
        $tmdbTvShows = [];
        $tmdbTotalPages = 1;
        $tmdbPage = $page;
        $remaining = $perPage;
        $localCount = count($localTvShows);
        $offset = ($page - 1) * $perPage;
        $localRemaining = max(0, $localCount - $offset);
        $tmdbNeeded = max(0, $remaining - min($localRemaining, $perPage));

        if ($tmdbNeeded > 0) {
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

            try {
                $response = Http::get($apiUrl);
                if ($response->successful()) {
                    $tmdbData = $response->json();
                    $tmdbTvShows = array_map(function ($show) {
                        return array_merge($show, [
                            'id' => (int)$show['id'],
                            'content_type' => 'shows',
                            'is_local' => false
                        ]);
                    }, $tmdbData['results'] ?? []);
                    $tmdbTotalPages = $tmdbData['total_pages'] ?? 1;
                } else {
                    Log::error('TMDB API request failed', ['url' => $apiUrl, 'status' => $response->status()]);
                }
            } catch (\Exception $e) {
                Log::error('TMDB API exception', ['url' => $apiUrl, 'error' => $e->getMessage()]);
            }
        }

        // Ban filtering
        $bannedTvShows = BannedTvShow::all();
        $bannedTmdbIds = $bannedTvShows->pluck('tmdb_id')->filter()->toArray();
        $bannedLocalIds = $bannedTvShows->pluck('tv_show_id')->filter()->toArray();

        $filteredLocalTvShows = array_filter($localTvShows, fn($tvShow) => !in_array($tvShow['id'] ?? null, $bannedLocalIds));
        $filteredTmdbTvShows = array_filter($tmdbTvShows, fn($tvShow) => !in_array($tvShow['id'] ?? null, $bannedTmdbIds));

        // Paginate
        $localSlice = array_slice($filteredLocalTvShows, $offset, $perPage);
        $tmdbSlice = $tmdbNeeded > 0 ? array_slice($filteredTmdbTvShows, 0, $tmdbNeeded) : [];
        $tvShows = array_merge($localSlice, $tmdbSlice);

        // Total pages
        $totalItems = max($localCount, $tmdbTotalPages * $perPage);
        $totalPages = ceil($totalItems / $perPage);

        Log::info('TV Shows fetched', [
            'total_pages' => $totalPages,
            'filters' => compact('search', 'genres', 'year', 'rating', 'sort', 'order')
        ]);

        return view('Front-office.shows', [
            'content' => $tvShows, // Changed to 'content' for consistency
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

    public function showTvShowDetails($id)
    {
        try {
            $url = "{$this->baseUrl}/tv/{$id}?api_key={$this->apiKey}&append_to_response=credits,videos,reviews,similar,keywords";
            $response = Http::get($url);

            if ($response->successful()) {
                $show = $response->json();
                $isInList = Auth::check() && UserTvShowList::where('user_id', Auth::id())
                    ->where('tmdb_id', $id)
                    ->exists();
                return view('Front-office.informations', [
                    'show' => $show,
                    'isInList' => $isInList,
                    'noInfoAvailable' => false
                ]);
            }
            return redirect()->route('tv-shows.shows')->with('error', 'TV Show not found.');
        } catch (\Exception $e) {
            Log::error('TMDB Show Details Error', ['id' => $id, 'error' => $e->getMessage()]);
            return view('errors.500');
        }
    }

    public function localShowDetails($id)
    {
        try {
            Log::debug('Local Show Details Called', ['id' => $id]);
            $noInfoAvailable = true;
            $media = [
                'id' => $id,
                'title' => 'Untitled',
                'year' => null,
                'rating' => null,
                'poster' => null,
                'description' => null,
                'number_of_seasons' => 0,
                'type' => 'show',
            ];
            $isInList = false;

            $localShow = TvShow::find($id);

            if ($localShow) {
                $media = [
                    'id' => $localShow->id,
                    'title' => $localShow->title ?? 'Untitled',
                    'year' => $localShow->year,
                    'rating' => $localShow->rating,
                    'poster' => $localShow->poster,
                    'description' => $localShow->description ?? null,
                    'number_of_seasons' => 0,
                    'type' => 'show',
                ];
                $noInfoAvailable = false;
            } else {
                Log::warning('Local show not found', ['id' => $id]);
            }

            Log::debug('Local Show Details Data', [
                'id' => $id,
                'media' => $media,
                'noInfoAvailable' => $noInfoAvailable,
                'isInList' => $isInList
            ]);

            return view('Front-office.local', compact('media', 'noInfoAvailable', 'isInList'));
        } catch (\Exception $e) {
            Log::error('Local Show Details Error', ['id' => $id, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return view('errors.500');
        }
    }

    public function fetchEpisodeDownloadLink($tvShowTitle, $seasonNumber, $episodeNumber)
    {
        if (Gate::denies('download-content')) {
            return response()->json(['error' => 'You must be a premium user to download content.'], 403);
        }

        $seasonFormatted = str_pad($seasonNumber, 2, '0', STR_PAD_LEFT);
        $episodeFormatted = str_pad($episodeNumber, 2, '0', STR_PAD_LEFT);
        $searchTerm = "{$tvShowTitle} S{$seasonFormatted}E{$episodeFormatted}";
        $encodedSearch = urlencode($searchTerm);

        Log::info('Starting torrent search', [
            'show' => $tvShowTitle,
            'season' => $seasonNumber,
            'episode' => $episodeNumber,
            'search_term' => $searchTerm
        ]);

        $requestOptions = [
            'timeout' => 15,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            ]
        ];

        $client = new Client($requestOptions);

        // EZTV API
        try {
            Log::info('Trying EZTV API');
            $apiUrl = "https://eztv.re/api/get-torrents?limit=100&imdb_id=&page=1&query=" . $encodedSearch;
            $response = $client->request('GET', $apiUrl);
            $data = json_decode($response->getBody(), true);

            if (isset($data['torrents']) && count($data['torrents']) > 0) {
                $matchingTorrents = [];
                foreach ($data['torrents'] as $torrent) {
                    $title = $torrent['title'] ?? '';
                    $showNameVariations = [$tvShowTitle, str_replace(' ', '.', $tvShowTitle), str_replace(' ', '-', $tvShowTitle)];
                    $hasShowName = false;
                    foreach ($showNameVariations as $variation) {
                        if (stripos($title, $variation) !== false) {
                            $hasShowName = true;
                            break;
                        }
                    }
                    $seasonEpisodePatterns = ["S{$seasonFormatted}E{$episodeFormatted}", "s{$seasonFormatted}e{$episodeFormatted}", "{$seasonFormatted}x{$episodeFormatted}"];
                    $hasSeasonEpisode = false;
                    foreach ($seasonEpisodePatterns as $pattern) {
                        if (stripos($title, $pattern) !== false) {
                            $hasSeasonEpisode = true;
                            break;
                        }
                    }
                    if ($hasShowName && $hasSeasonEpisode) {
                        $matchingTorrents[] = $torrent;
                    }
                }
                usort($matchingTorrents, fn($a, $b) => ($b['seeds'] ?? 0) - ($a['seeds'] ?? 0));
                if (!empty($matchingTorrents)) {
                    $bestTorrent = reset($matchingTorrents);
                    Log::info('Found matching torrent on EZTV', ['torrent' => $bestTorrent['title']]);
                    return $bestTorrent['magnet_url'] ?? null;
                }
            }
        } catch (\Exception $e) {
            Log::error('Error with EZTV API:', ['error' => $e->getMessage()]);
        }

        // ThePirateBay API
        try {
            Log::info('Trying ThePirateBay API');
            $apiUrl = "https://apibay.org/q.php?q={$encodedSearch}&cat=205";
            $response = $client->request('GET', $apiUrl);
            $data = json_decode($response->getBody(), true);

            if (is_array($data) && count($data) > 0 && isset($data[0]['id']) && $data[0]['id'] != 0) {
                $matchingTorrents = [];
                foreach ($data as $torrent) {
                    $title = $torrent['name'] ?? '';
                    $showNameVariations = [$tvShowTitle, str_replace(' ', '.', $tvShowTitle), str_replace(' ', '-', $tvShowTitle)];
                    $hasShowName = false;
                    foreach ($showNameVariations as $variation) {
                        if (stripos($title, $variation) !== false) {
                            $hasShowName = true;
                            break;
                        }
                    }
                    $seasonEpisodePatterns = ["S{$seasonFormatted}E{$episodeFormatted}", "s{$seasonFormatted}e{$episodeFormatted}", "{$seasonFormatted}x{$episodeFormatted}"];
                    $hasSeasonEpisode = false;
                    foreach ($seasonEpisodePatterns as $pattern) {
                        if (stripos($title, $pattern) !== false) {
                            $hasSeasonEpisode = true;
                            break;
                        }
                    }
                    if ($hasShowName && $hasSeasonEpisode) {
                        $matchingTorrents[] = $torrent;
                    }
                }
                usort($matchingTorrents, fn($a, $b) => ($b['seeders'] ?? 0) - ($a['seeders'] ?? 0));
                if (!empty($matchingTorrents)) {
                    $bestTorrent = reset($matchingTorrents);
                    Log::info('Found matching torrent on ThePirateBay', ['torrent' => $bestTorrent['name']]);
                    $infoHash = $bestTorrent['info_hash'];
                    $name = urlencode($bestTorrent['name']);
                    $magnetLink = "magnet:?xt=urn:btih:{$infoHash}&dn={$name}&tr=udp%3A%2F%2Ftracker.coppersurfer.tk%3A6969%2Fannounce&tr=udp%3A%2F%2F9.rarbg.to%3A2920%2Fannounce&tr=udp%3A%2F%2Ftracker.opentrackr.org%3A1337&tr=udp%3A%2F%2Ftracker.internetwarriors.net%3A1337%2Fannounce";
                    return $magnetLink;
                }
            }
        } catch (\Exception $e) {
            Log::error('Error with ThePirateBay API:', ['error' => $e->getMessage()]);
        }

        // Nyaa API for anime
        if (stripos($tvShowTitle, 'anime') !== false || stripos($tvShowTitle, 'dragon') !== false || stripos($tvShowTitle, 'naruto') !== false || stripos($tvShowTitle, 'one piece') !== false) {
            try {
                Log::info('Trying Nyaa API for possible anime');
                $apiUrl = "https://nyaa.si/?page=rss&q={$encodedSearch}&c=1_2&f=0";
                $response = $client->request('GET', $apiUrl);
                $xmlContent = $response->getBody()->getContents();
                $xml = simplexml_load_string($xmlContent);
                if ($xml && isset($xml->channel->item)) {
                    foreach ($xml->channel->item as $item) {
                        $title = (string)$item->title;
                        if ((stripos($title, $tvShowTitle) !== false || stripos($title, str_replace(' ', '_', $tvShowTitle)) !== false) &&
                            (stripos($title, "S{$seasonFormatted}E{$episodeFormatted}") !== false || stripos($title, "s{$seasonFormatted}e{$episodeFormatted}") !== false || stripos($title, "- {$episodeNumber}") !== false)) {
                            Log::info('Found matching anime torrent on Nyaa', ['torrent' => $title]);
                            return (string)$item->link;
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error with Nyaa API:', ['error' => $e->getMessage()]);
            }
        }

        Log::warning('No torrent found across all APIs', [
            'show' => $tvShowTitle,
            'season' => $seasonNumber,
            'episode' => $episodeNumber
        ]);

        return null;
    }

    public function apiEpisodeDownload(Request $request)
    {
        $title = $request->query('title');
        $seasonNumber = $request->query('season');
        $episodeNumber = $request->query('episode');

        if (!$title || !$seasonNumber || !$episodeNumber) {
            return response()->json(['error' => 'Missing required parameters'], 400);
        }

        $downloadLink = $this->fetchEpisodeDownloadLink($title, $seasonNumber, $episodeNumber);

        return response()->json([
            'success' => $downloadLink ? true : false,
            'download_link' => $downloadLink,
        ]);
    }

    // public function localShowDetails($id)
    // {
    //     try {
    //         Log::debug('Local Show Details Called', ['id' => $id]);
    //         $noInfoAvailable = true;
    //         $media = [
    //             'id' => $id,
    //             'title' => 'Untitled',
    //             'year' => null,
    //             'rating' => null,
    //             'poster' => null,
    //             'description' => null,
    //             'number_of_seasons' => 0,
    //             'type' => 'show',
    //         ];
    //         $isInList = false;

    //         $localShow = TvShow::find($id);

    //         if ($localShow) {
    //             $media = [
    //                 'id' => $localShow->id,
    //                 'title' => $localShow->title ?? 'Untitled',
    //                 'year' => $localShow->year,
    //                 'rating' => $localShow->rating,
    //                 'poster' => $localShow->poster,
    //                 'description' => $localShow->description ?? null,
    //                 'number_of_seasons' => 0,
    //                 'type' => 'show',
    //             ];
    //             $noInfoAvailable = false;
    //         } else {
    //             Log::warning('Local show not found', ['id' => $id]);
    //         }

    //         Log::debug('Local Show Details Data', [
    //             'id' => $id,
    //             'media' => $media,
    //             'noInfoAvailable' => $noInfoAvailable,
    //             'isInList' => $isInList
    //         ]);

    //         return view('Front-office.local', compact('media', 'noInfoAvailable', 'isInList'));
    //     } catch (\Exception $e) {
    //         Log::error('Local Show Details Error', ['id' => $id, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
    //         return view('errors.500');
    //     }
    // }
}