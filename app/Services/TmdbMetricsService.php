<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TmdbMetricsService
{
    protected $client;
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('TMDB_API_KEY');
        $this->baseUrl = 'https://api.themoviedb.org/3';
    }

    public function getMetrics()
    {
        // Cache TMDB metrics for 1 hour
        return Cache::remember('tmdb_metrics', 3600, function () {
            try {
                // Fetch popular movies
                $movieResponse = $this->client->get("{$this->baseUrl}/discover/movie?api_key={$this->apiKey}&language=en-US&page=1&sort_by=popularity.desc");
                if ($movieResponse->getStatusCode() !== 200) {
                    Log::error('TMDB API Error: Failed to fetch popular movies', [
                        'status' => $movieResponse->getStatusCode(),
                    ]);
                    throw new \Exception('TMDB movie request failed with status ' . $movieResponse->getStatusCode());
                }
                $movies = json_decode($movieResponse->getBody()->getContents(), true)['results'] ?? [];
                Log::info('Fetched popular movies from TMDB', ['count' => count($movies)]);

                // Fetch popular series
                $seriesResponse = $this->client->get("{$this->baseUrl}/discover/tv?api_key={$this->apiKey}&language=en-US&page=1&sort_by=popularity.desc");
                if ($seriesResponse->getStatusCode() !== 200) {
                    Log::error('TMDB API Error: Failed to fetch popular series', [
                        'status' => $seriesResponse->getStatusCode(),
                    ]);
                    throw new \Exception('TMDB series request failed with status ' . $seriesResponse->getStatusCode());
                }
                $series = json_decode($seriesResponse->getBody()->getContents(), true)['results'] ?? [];
                Log::info('Fetched popular series from TMDB', ['count' => count($series)]);

                $mostWatchedMovies = [];
                $genreTracker = [];

                foreach ($movies as $movie) {
                    $movieDetails = $this->fetchTmdbMovieDetails($movie['id']);
                    $genres = $movieDetails['genres'] ?? [];

                    $mostWatchedMovies[] = [
                        'title' => $movie['title'] ?? 'Unknown',
                        'poster_url' => $movie['poster_path'] ? 'https://image.tmdb.org/t/p/w92' . $movie['poster_path'] : 'https://via.placeholder.com/92x138',
                        'popularity' => $movie['popularity'] ?? 0,
                        'vote_average' => $movie['vote_average'] ?? 0,
                    ];

                    foreach ($genres as $genre) {
                        $genreTracker[$genre['name']] = ($genreTracker[$genre['name']] ?? 0) + 1;
                    }
                }

                foreach ($series as $show) {
                    $showDetails = $this->fetchTmdbSeriesDetails($show['id']);
                    $genres = $showDetails['genres'] ?? [];
                    foreach ($genres as $genre) {
                        $genreTracker[$genre['name']] = ($genreTracker[$genre['name']] ?? 0) + 1;
                    }
                }

                $genreDistribution = array_map(function ($name, $count) {
                    return ['name' => $name, 'count' => $count];
                }, array_keys($genreTracker), array_values($genreTracker));

                return [
                    'total_movies' => count($movies),
                    'total_series' => count($series),
                    'content_distribution' => [
                        'movies' => count($movies),
                        'series' => count($series)
                    ],
                    'most_watched_movies' => collect($mostWatchedMovies)->sortByDesc('popularity')->take(5)->values()->all(),
                    'genre_distribution' => $genreDistribution,
                ];
            } catch (\Exception $e) {
                Log::error('TMDB Metrics Fetch Error', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                return [
                    'total_movies' => 0,
                    'total_series' => 0,
                    'content_distribution' => ['movies' => 0, 'series' => 0],
                    'most_watched_movies' => [],
                    'genre_distribution' => [],
                ];
            }
        });
    }

    protected function fetchTmdbMovieDetails($movieId)
    {
        return Cache::remember("tmdb_movie_{$movieId}", 86400, function () use ($movieId) {
            try {
                $response = $this->client->get("{$this->baseUrl}/movie/{$movieId}?api_key={$this->apiKey}&language=en-US");
                $movieDetails = json_decode($response->getBody()->getContents(), true);
                Log::info('Fetched movie details from TMDB', ['movie_id' => $movieId, 'title' => $movieDetails['title'] ?? 'Unknown']);
                return $movieDetails;
            } catch (\Exception $e) {
                Log::error('TMDB Movie Details Fetch Error', ['movie_id' => $movieId, 'message' => $e->getMessage()]);
                return [];
            }
        });
    }

    protected function fetchTmdbSeriesDetails($seriesId)
    {
        return Cache::remember("tmdb_series_{$seriesId}", 86400, function () use ($seriesId) {
            try {
                $response = $this->client->get("{$this->baseUrl}/tv/{$seriesId}?api_key={$this->apiKey}&language=en-US");
                $seriesDetails = json_decode($response->getBody()->getContents(), true);
                Log::info('Fetched series details from TMDB', ['series_id' => $seriesId, 'name' => $seriesDetails['name'] ?? 'Unknown']);
                return $seriesDetails;
            } catch (\Exception $e) {
                Log::error('TMDB Series Details Fetch Error', ['series_id' => $seriesId, 'message' => $e->getMessage()]);
                return [];
            }
        });
    }
}