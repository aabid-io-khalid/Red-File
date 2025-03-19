<?php

namespace App\Http\Controllers;

use Log;
use App\Models\Movie;
use GuzzleHttp\Client;
use App\Models\BannedMovie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MovieController extends Controller
{
    // public function filteredMovies(Request $request)
    // {
    //     $apiKey = env('TMDB_API_KEY'); 
    //     $genre = $request->input('genre', 'all');
    //     $year = $request->input('year', 'all');
    //     $rating = $request->input('rating', 'all');
    //     $sort = $request->input('sort', 'popularity.desc');
    //     $page = $request->input('page', 1);

    //     $url = "https://api.themoviedb.org/3/discover/movie?api_key={$apiKey}&page={$page}";

    //     if ($genre && $genre !== 'all') {
    //         $url .= "&with_genres={$genre}";
    //     }
    //     if ($year && $year !== 'all') {
    //         $url .= "&primary_release_year={$year}";
    //     }
    //     if ($rating && $rating !== 'all') {
    //         $url .= "&vote_average.gte={$rating}";
    //     }
    //     if ($sort) {
    //         $url .= "&sort_by={$sort}";
    //     }

    //     $response = Http::get($url);
    //     $movies = $response->json()['results'];
    //     $totalPages = $response->json()['total_pages'];

    //     // Exclude banned movies
    //     $bannedMovieIds = BannedMovie::pluck('tmdb_id')->toArray();
    //     $movies = array_filter($movies, function ($movie) use ($bannedMovieIds) {
    //         return !in_array($movie['id'], $bannedMovieIds);
    //     });

    //     return view('Front-office.movies', compact('movies', 'totalPages', 'genre', 'year', 'rating', 'sort', 'page'));
    // }

    // public function createMovie(Request $request)
    // {
    //     $request->validate([
    //         'title' => 'required|string',
    //         'description' => 'nullable|string',
    //         'genre' => 'nullable|string',
    //         'year' => 'nullable|integer',
    //         'rating' => 'nullable|numeric',
    //         'poster' => 'nullable|string',
    //     ]);

    //     Movie::create($request->all());

    //     return redirect()->back()->with('success', 'Movie created successfully.');
    // }

    // public function banMovie(Request $request)
    // {
    //     $request->validate([
    //         'tmdb_id' => 'required|string|unique:banned_movies,tmdb_id',
    //     ]);

    //     BannedMovie::create(['tmdb_id' => $request->tmdb_id]);

    //     return redirect()->back()->with('success', 'Movie banned successfully.');
    // }

    // public function search(Request $request)
    // {
    //     $movieTitle = $request->input('movie_title');
    //     $client = new Client();
    //     $response = $client->request('GET', "https://1337x.to/search/{$movieTitle}/1/");
    //     $html = $response->getBody()->getContents();

    //     $crawler = new Crawler($html);
    //     $result = $crawler->filter('tr.listrow')->first();

    //     if ($result) {
    //         $downloadLink = $result->filter('a.download')->attr('href');
    //         return view('movie_details', ['movie_title' => $movieTitle, 'download_link' => $downloadLink]);
    //     } else {
    //         return view('movie_details', ['movie_title' => $movieTitle, 'download_link' => null]);
    //     }
    // }

//     public function showMovieDetails($id)
// {
//     $apiKey = env('TMDB_API_KEY');
//     $url = "https://api.themoviedb.org/3/movie/{$id}?api_key={$apiKey}&append_to_response=credits,videos,reviews,similar,keywords";

//     $response = Http::get($url);

//     // Debugging: Log the response
//     Log::info('Movie API Response:', $response->json());

//     if ($response->successful()) {
//         $movieData = $response->json();
//         return view('Front-office.details', compact('movieData'));
//     }

//     return redirect()->route('movies.index')->with('error', 'Movie not found.');
// }

// public function search(Request $request)
// {
//     $movieTitle = $request->input('movie_title');
//     $client = new Client();
//     $response = $client->request('GET', "https://1337x.to/search/{$movieTitle}/1/");
//     $html = $response->getBody()->getContents();

//     $crawler = new Crawler($html);
//     $result = $crawler->filter('tr.listrow')->first();

//     if ($result) {
//         $downloadLink = $result->filter('a.download')->attr('href');
//         // You may also want to fetch the movie ID here if available
//         $movieId = $result->filter('a')->attr('href'); // Adjust this based on the actual structure
//         return view('Front-office.details', [
//             'movie_title' => $movieTitle,
//             'download_link' => $downloadLink,
//             'movie_id' => $movieId, // Pass the movie ID
//         ]);
//     } else {
//         return redirect()->back()->with('error', 'No results found for your search.');
//     }
// }


public function filteredMovies(Request $request)
    {
        $apiKey = env('TMDB_API_KEY'); 
        $genre = $request->input('genre', 'all');
        $year = $request->input('year', 'all');
        $rating = $request->input('rating', 'all');
        $sort = $request->input('sort', 'popularity.desc');
        $page = $request->input('page', 1);

        $url = "https://api.themoviedb.org/3/discover/movie?api_key={$apiKey}&page={$page}";

        if ($genre && $genre !== 'all') {
            $url .= "&with_genres={$genre}";
        }
        if ($year && $year !== 'all') {
            $url .= "&primary_release_year={$year}";
        }
        if ($rating && $rating !== 'all') {
            $url .= "&vote_average.gte={$rating}";
        }
        if ($sort) {
            $url .= "&sort_by={$sort}";
        }

        $response = Http::get($url);
        $movies = $response->json()['results'];
        $totalPages = $response->json()['total_pages'];

        $bannedMovieIds = BannedMovie::pluck('tmdb_id')->toArray();
        $movies = array_filter($movies, function ($movie) use ($bannedMovieIds) {
            return !in_array($movie['id'], $bannedMovieIds);
        });

        return view('Front-office.movies', compact('movies', 'totalPages', 'genre', 'year', 'rating', 'sort', 'page'));
    }

    public function createMovie(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'genre' => 'nullable|string',
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
        $apiKey = env('TMDB_API_KEY');
        $url = "https://api.themoviedb.org/3/movie/{$id}?api_key={$apiKey}&append_to_response=credits,videos,reviews,similar,keywords";

        $response = Http::get($url);

        if ($response->successful()) {
            $movieData = $response->json();

            $downloadLink = $this->fetchYTSDownloadLink($movieData['title']); 

            return view('Front-office.details', compact('movieData', 'downloadLink'));
        }

        return redirect()->route('movies.index')->with('error', 'Movie not found.');
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
        $movieTitle = $request->input('title');
        $downloadLink = $this->fetchYTSDownloadLink($movieTitle); 

        return response()->json(['download_link' => $downloadLink]);
    }

    private function sanitizeTitle($title)
    {
        return preg_replace('/[^a-zA-Z0-9\s]/', '', $title);
    }

}