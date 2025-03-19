<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class TvShowController extends Controller
{
    private $apiKey;
    private $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('TMDB_API_KEY'); 
        $this->baseUrl = 'https://api.themoviedb.org/3';
    }


    // public function index()
    // {
    //     return view('Front-office.home');
    // }

    public function shows(Request $request)
    {
        $search = $request->query('search', '');
        $genre = $request->query('genre', '');
        $year = $request->query('year', '2025');
        $rating = $request->query('rating', '');
        $sort = $request->query('sort', 'popularity');
        $order = $request->query('order', 'desc');
        $page = $request->query('page', 1);

        $apiUrl = "{$this->baseUrl}/discover/tv?api_key={$this->apiKey}&page={$page}";

        if ($search) {
            $apiUrl = "{$this->baseUrl}/search/tv?api_key={$this->apiKey}&query=" . urlencode($search);
        }
        if ($genre) {
            $apiUrl .= "&with_genres={$genre}";
        }
        if ($year) {
            $apiUrl .= "&first_air_date_year={$year}";
        }
        if ($rating) {
            $apiUrl .= "&vote_average.gte={$rating}";
        }
        if ($sort) {
            $apiUrl .= "&sort_by={$sort}.{$order}";
        }

        $response = Http::get($apiUrl);
        
        if ($response->failed()) {
            Log::error('TMDB API request failed', ['url' => $apiUrl, 'response' => $response->body()]);
            return view('Front-office.shows', [
                'tvShows' => [],
                'genres' => [],
                'search' => $search,
                'genre' => $genre,
                'year' => $year,
                'rating' => $rating,
                'sort' => $sort,
                'order' => $order,
                'page' => $page,
                'totalPages' => 0,
            ]);
        }

        $tvShows = $response->json();

        $genresResponse = Http::get("{$this->baseUrl}/genre/tv/list?api_key={$this->apiKey}");
        $genres = $genresResponse->json()['genres'];

        return view('Front-office.shows', [
            'tvShows' => $tvShows['results'] ?? [],
            'genres' => $genres,
            'search' => $search,
            'genre' => $genre,
            'year' => $year,
            'rating' => $rating,
            'sort' => $sort,
            'order' => $order,
            'page' => $page,
            'totalPages' => $tvShows['total_pages'] ?? 1,
        ]);
    }

    public function showTvShowDetails($id)
    {
        $url = "{$this->baseUrl}/tv/{$id}?api_key={$this->apiKey}&append_to_response=credits,videos,reviews,similar,keywords";
        $response = Http::get($url);
    
        if ($response->successful()) {
            $show = $response->json(); 
            $similarShows = $show['similar']['results'] ?? []; 
    
            return view('Front-office.informations', [
                'show' => $show,
                'similarShows' => $similarShows 
            ]);
        }
    
        return redirect()->route('tv-shows.shows')->with('error', 'TV Show not found.');
    }






    public function fetchEpisodeDownloadLink($tvShowTitle, $seasonNumber, $episodeNumber)
    {
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
        
        // EZTV API howa lowal for tv shows
        try {
            Log::info('Trying EZTV API');
            $apiUrl = "https://eztv.re/api/get-torrents?limit=100&imdb_id=&page=1&query=" . $encodedSearch;
            
            $response = $client->request('GET', $apiUrl);
            $data = json_decode($response->getBody(), true);
            
            Log::info('EZTV response received', ['status' => isset($data['torrents']) ? 'has torrents' : 'no torrents']);
            
            if (isset($data['torrents']) && count($data['torrents']) > 0) {
                $matchingTorrents = [];
                
                foreach ($data['torrents'] as $torrent) {
                    $title = $torrent['title'] ?? '';
                    
                    $showNameVariations = [
                        $tvShowTitle,
                        str_replace(' ', '.', $tvShowTitle),
                        str_replace(' ', '-', $tvShowTitle)
                    ];
                    
                    $hasShowName = false;
                    foreach ($showNameVariations as $variation) {
                        if (stripos($title, $variation) !== false) {
                            $hasShowName = true;
                            break;
                        }
                    }
                    
                    $seasonEpisodePatterns = [
                        "S{$seasonFormatted}E{$episodeFormatted}",
                        "s{$seasonFormatted}e{$episodeFormatted}",
                        "{$seasonFormatted}x{$episodeFormatted}"
                    ];
                    
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
                
                usort($matchingTorrents, function($a, $b) {
                    return ($b['seeds'] ?? 0) - ($a['seeds'] ?? 0);
                });
                
                if (!empty($matchingTorrents)) {
                    $bestTorrent = reset($matchingTorrents);
                    Log::info('Found matching torrent on EZTV', ['torrent' => $bestTorrent['title']]);
                    return $bestTorrent['magnet_url'] ?? null;
                }
            }
        } catch (\Exception $e) {
            Log::error('Error with EZTV API:', ['error' => $e->getMessage()]);
        }
        
        // ThePirateBay API (via proxy)
        try {
            Log::info('Trying ThePirateBay API');
            $apiUrl = "https://apibay.org/q.php?q={$encodedSearch}&cat=205"; //jrab 205 is for TV shows yts 5dam ha lmovies
            
            $response = $client->request('GET', $apiUrl);
            $data = json_decode($response->getBody(), true);
            
            if (is_array($data) && count($data) > 0 && isset($data[0]['id']) && $data[0]['id'] != 0) {
                $matchingTorrents = [];
                
                foreach ($data as $torrent) {
                    $title = $torrent['name'] ?? '';
                    
                    $showNameVariations = [
                        $tvShowTitle,
                        str_replace(' ', '.', $tvShowTitle),
                        str_replace(' ', '-', $tvShowTitle)
                    ];
                    
                    $hasShowName = false;
                    foreach ($showNameVariations as $variation) {
                        if (stripos($title, $variation) !== false) {
                            $hasShowName = true;
                            break;
                        }
                    }
                    
                    $seasonEpisodePatterns = [
                        "S{$seasonFormatted}E{$episodeFormatted}",
                        "s{$seasonFormatted}e{$episodeFormatted}",
                        "{$seasonFormatted}x{$episodeFormatted}"
                    ];
                    
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
                
                usort($matchingTorrents, function($a, $b) {
                    return ($b['seeders'] ?? 0) - ($a['seeders'] ?? 0);
                });
                
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
        
        // hada Nyaa API for anime bach nfetch anime 
        if (stripos($tvShowTitle, 'anime') !== false || 
            stripos($tvShowTitle, 'dragon') !== false || 
            stripos($tvShowTitle, 'naruto') !== false || 
            stripos($tvShowTitle, 'one piece') !== false) {
            
            try {
                Log::info('Trying Nyaa API for possible anime');
                $apiUrl = "https://nyaa.si/?page=rss&q={$encodedSearch}&c=1_2&f=0";
                
                $response = $client->request('GET', $apiUrl);
                $xmlContent = $response->getBody()->getContents();
                
                $xml = simplexml_load_string($xmlContent);
                if ($xml && isset($xml->channel->item)) {
                    foreach ($xml->channel->item as $item) {
                        $title = (string)$item->title;
                        
                        if ((stripos($title, $tvShowTitle) !== false || 
                             stripos($title, str_replace(' ', '_', $tvShowTitle)) !== false) && 
                            (stripos($title, "S{$seasonFormatted}E{$episodeFormatted}") !== false || 
                             stripos($title, "s{$seasonFormatted}e{$episodeFormatted}") !== false ||
                             stripos($title, "- {$episodeNumber}") !== false)) {
                                
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
}