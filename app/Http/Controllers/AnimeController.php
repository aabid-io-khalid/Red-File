<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class AnimeController extends Controller
{
    private $apiKey;
    private $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('TMDB_API_KEY');
        $this->baseUrl = 'https://api.themoviedb.org/3';
    }

    public function filteredAnime(Request $request)
    {
        try {
            $genreResponse = Http::get("{$this->baseUrl}/genre/tv/list?api_key={$this->apiKey}");
            
            if (!$genreResponse->successful()) {
                Log::error('TMDB Genre API Error', [
                    'status' => $genreResponse->status(),
                    'response' => $genreResponse->body()
                ]);
                throw new \Exception('Failed to fetch genres');
            }

            $genres = $genreResponse->json('genres', []);

            $params = [
                'api_key' => $this->apiKey,
                'with_genres' => '16', 
                'page' => $request->input('page', 1),
                'sort_by' => $this->sanitizeSort($request->input('sort', 'popularity'), $request->input('order', 'desc')),
            ];

            if ($request->filled('search')) {
                $params['query'] = $request->input('search');
            }
            if ($request->filled('year')) {
                $params['first_air_date_year'] = $request->input('year');
            }
            if ($request->filled('rating')) {
                $params['vote_average.gte'] = $request->input('rating');
            }

            $url = $request->filled('search') ? "{$this->baseUrl}/search/tv" : "{$this->baseUrl}/discover/tv";

            $response = Http::get($url, array_filter($params));

            if (!$response->successful()) {
                Log::error('TMDB Anime API Error', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                throw new \Exception('Failed to fetch anime data from TMDB');
            }

            $data = $response->json();

            return view('front-office.anime', [
                'animes' => $data['results'] ?? [],
                'genres' => $genres,
                'totalPages' => $data['total_pages'] ?? 1,
                'currentPage' => $params['page'],
                'filters' => $request->all(),
                'error' => null
            ]);

        } catch (\Exception $e) {
            Log::error('AnimeController Error: ' . $e->getMessage());
            
            return view('front-office.anime', [
                'animes' => [],
                'genres' => [],
                'totalPages' => 1,
                'currentPage' => 1,
                'filters' => [],
                'error' => 'Failed to load anime data. Please try again later.'
            ]);
        }
    }

    public function show($id)
    {
        try {
            // Fetch show details from TMDB API
            $response = Http::get("{$this->baseUrl}/tv/{$id}", [
                'api_key' => $this->apiKey,
                'language' => 'en-US',
            ]);

            if (!$response->successful()) {
                Log::error('TMDB Show Details API Error', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                throw new \Exception('Failed to fetch show details from TMDB');
            }

            $showDetails = $response->json();

            if (isset($showDetails['seasons']) && count($showDetails['seasons']) > 0) {
                $seasonNumber = 1; 
                $episodeNumber = 1; 
                $downloadLink = $this->fetchEpisodeDownloadLink($showDetails['name'], $seasonNumber, $episodeNumber);
            } else {
                $downloadLink = null;
            }

            return view('front-office.informations', [
                'show' => $showDetails,
                'download_link' => $downloadLink,
                'error' => null
            ]);

        } catch (\Exception $e) {
            Log::error('AnimeController Show Error: ' . $e->getMessage());
            
            return view('front-office.informations', [
                'show' => null,
                'download_link' => null,
                'error' => 'Failed to load show details. Please try again later.'
            ]);
        }
    }

    private function sanitizeSort($sort, $order)
    {
        $validSorts = [
            'popularity' => 'popularity',
            'release_date' => 'first_air_date',
            'vote_average' => 'vote_average',
            'trending' => 'popularity'
        ];

        $sortKey = $validSorts[$sort] ?? 'popularity';
        $order = in_array($order, ['asc', 'desc']) ? $order : 'desc';

        return "$sortKey.$order";
    }



    public function fetchEpisodeDownloadLink($tvShowTitle, $seasonNumber, $episodeNumber)
{
    $seasonFormatted = str_pad($seasonNumber, 2, '0', STR_PAD_LEFT);
    $episodeFormatted = str_pad($episodeNumber, 2, '0', STR_PAD_LEFT);
    
    $searchTerm = "{$tvShowTitle} S{$seasonFormatted}E{$episodeFormatted}";
    $encodedSearch = urlencode($searchTerm);
    
    Log::info('Starting anime torrent search', [
        'show' => $tvShowTitle,
        'season' => $seasonNumber,
        'episode' => $episodeNumber,
        'search_term' => $searchTerm
    ]);
    
    $client = new Client([
        'timeout' => 15,
        'headers' => [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
        ]
    ]);

    // 1. Nyaa.si (Primary Anime Torrent Source)
    try {
        Log::info('Trying Nyaa.si');
        $apiUrl = "https://nyaa.si/?page=rss&q={$encodedSearch}&c=1_2&f=0"; // c=1_2 is for English-translated anime
        
        $response = $client->request('GET', $apiUrl);
        $xmlContent = $response->getBody()->getContents();
        
        $xml = simplexml_load_string($xmlContent);
        if ($xml && isset($xml->channel->item)) {
            $matchingTorrents = [];
            
foreach ($xml->channel->item as $item) {
    $title = (string)$item->title;
    $link = (string)$item->link;
    $seeders = (int)$item->children('http://xmlns.ezrss.it/0.1/')->torrent->seeds;
    
    Log::debug('Checking Nyaa.si torrent', ['title' => $title, 'seeders' => $seeders]);
    
    $seasonEpisodePatterns = [
        "S{$seasonFormatted}E{$episodeFormatted}",
        "s{$seasonFormatted}e{$episodeFormatted}",
        "- {$episodeNumber}",
        "[Ep {$episodeNumber}]",
        " - {$episodeFormatted}",
        "[{$episodeNumber}]",
        "Episode {$episodeNumber}"
    ];
    
    $hasShowName = stripos($title, $tvShowTitle) !== false || 
                   stripos($title, str_replace(' ', '.', $tvShowTitle)) !== false || 
                   stripos($title, str_replace(' ', '-', $tvShowTitle)) !== false;
    $hasSeasonEpisode = false;
    
    foreach ($seasonEpisodePatterns as $pattern) {
        if (stripos($title, $pattern) !== false) {
            $hasSeasonEpisode = true;
            break;
        }
    }
    
    if ($hasShowName && $hasSeasonEpisode) {
        $matchingTorrents[] = [
            'title' => $title,
            'link' => $link,
            'seeders' => $seeders
        ];
    }
}
            
            if (!empty($matchingTorrents)) {
                usort($matchingTorrents, function($a, $b) {
                    return $b['seeders'] - $a['seeders'];
                });
                
                $bestTorrent = reset($matchingTorrents);
                Log::info('Found matching torrent on Nyaa.si', ['torrent' => $bestTorrent['title']]);
                return $bestTorrent['link'];
            }
        }
    } catch (\Exception $e) {
        Log::error('Error with Nyaa.si API:', ['error' => $e->getMessage()]);
    }

    // 2. AnimeTosho (Fallback Anime Torrent Source)
    try {
        Log::info('Trying AnimeTosho');
        $apiUrl = "https://feed.animetosho.org/json?q={$encodedSearch}";
        
        $response = $client->request('GET', $apiUrl);
        $data = json_decode($response->getBody(), true);
        
        if (is_array($data) && !empty($data)) {
            $matchingTorrents = [];
            
            foreach ($data as $torrent) {
                $title = $torrent['title'] ?? '';
                $magnet = $torrent['magnet_uri'] ?? '';
                $seeders = $torrent['seeders'] ?? 0;
                
                $seasonEpisodePatterns = [
                    "S{$seasonFormatted}E{$episodeFormatted}",
                    "s{$seasonFormatted}e{$episodeFormatted}",
                    "- {$episodeNumber}",
                    "[{$episodeNumber}]"
                ];
                
                $hasShowName = stripos($title, $tvShowTitle) !== false;
                $hasSeasonEpisode = false;
                
                foreach ($seasonEpisodePatterns as $pattern) {
                    if (stripos($title, $pattern) !== false) {
                        $hasSeasonEpisode = true;
                        break;
                    }
                }
                
                if ($hasShowName && $hasSeasonEpisode) {
                    $matchingTorrents[] = [
                        'title' => $title,
                        'link' => $magnet,
                        'seeders' => $seeders
                    ];
                }
            }
            
            if (!empty($matchingTorrents)) {
                usort($matchingTorrents, function($a, $b) {
                    return $b['seeders'] - $a['seeders'];
                });
                
                $bestTorrent = reset($matchingTorrents);
                Log::info('Found matching torrent on AnimeTosho', ['torrent' => $bestTorrent['title']]);
                return $bestTorrent['link'];
            }
        }
    } catch (\Exception $e) {
        Log::error('Error with AnimeTosho API:', ['error' => $e->getMessage()]);
    }

    // Fallback: Broader search without episode number
    try {
        Log::info('Trying fallback search on Nyaa.si');
        $fallbackSearch = urlencode($tvShowTitle . " S{$seasonFormatted}");
        $apiUrl = "https://nyaa.si/?page=rss&q={$fallbackSearch}&c=1_2&f=0";
        
        $response = $client->request('GET', $apiUrl);
        $xmlContent = $response->getBody()->getContents();
        
        $xml = simplexml_load_string($xmlContent);
        if ($xml && isset($xml->channel->item)) {
            foreach ($xml->channel->item as $item) {
                $title = (string)$item->title;
                if (stripos($title, $tvShowTitle) !== false) {
                    Log::info('Found fallback torrent on Nyaa.si', ['torrent' => $title]);
                    return (string)$item->link;
                }
            }
        }
    } catch (\Exception $e) {
        Log::error('Error with Nyaa.si fallback:', ['error' => $e->getMessage()]);
    }

    if (empty($matchingTorrents)) {
        foreach ($xml->channel->item as $item) {
            $title = (string)$item->title;
            $link = (string)$item->link;
            $seeders = (int)$item->children('http://xmlns.ezrss.it/0.1/')->torrent->seeds;
            
            if (stripos($title, $tvShowTitle) !== false && 
                (stripos($title, "[Complete]") !== false || stripos($title, "Batch") !== false || stripos($title, "S{$seasonFormatted}") !== false)) {
                Log::info('Found batch torrent on Nyaa.si', ['torrent' => $title]);
                return $link;
            }
        }
    }
    
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