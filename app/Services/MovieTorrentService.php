<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class MovieTorrentService
{
    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 15,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            ]
        ]);
    }

    public function fetchDownloadLink(string $movieTitle): ?string
    {
        try {
            $apiUrl = "https://yts.mx/api/v2/list_movies.json?query_term=" . urlencode($movieTitle) . "&limit=1";
            $response = $this->client->request('GET', $apiUrl);
            $data = json_decode($response->getBody(), true);

            if (isset($data['data']['movies']) && count($data['data']['movies']) > 0) {
                $movie = $data['data']['movies'][0];
                if (isset($movie['torrents'][0]['url'])) {
                    Log::info('Found YTS torrent', ['title' => $movieTitle, 'url' => $movie['torrents'][0]['url']]);
                    return $movie['torrents'][0]['url'];
                }
            }
            Log::warning('No YTS torrent found', ['title' => $movieTitle]);
            return null;
        } catch (\Exception $e) {
            Log::error('Error fetching YTS torrent', ['title' => $movieTitle, 'error' => $e->getMessage()]);
            return null;
        }
    }
}