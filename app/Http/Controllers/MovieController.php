<?php

namespace App\Http\Controllers;

use App\Services\TmdbService;
use App\Services\LocalMediaService;
use App\Services\MovieTorrentService;
use App\Models\Movie;
use App\Models\BannedMovie;
use App\Models\UserMovieList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class MovieController extends Controller
{
    private $tmdbService;
    private $localService;
    private $torrentService;

    public function __construct(TmdbService $tmdbService, LocalMediaService $localService, MovieTorrentService $torrentService)
    {
        $this->tmdbService = $tmdbService;
        $this->localService = $localService;
        $this->torrentService = $torrentService;
    }

    public function movies(Request $request)
    {
        $filters = [
            'search' => $request->query('search', ''),
            'genres' => $this->normalizeGenres($request->query('genres', [])),
            'year' => $request->query('year', ''),
            'rating' => $request->query('rating', ''),
            'sort' => $request->query('sort', 'popularity'),
            'order' => $request->query('order', 'desc')
        ];
        $page = max(1, (int)$request->query('page', 1));
        $perPage = 30;

        $genresList = $this->tmdbService->getGenres('movie');
        $genreMap = collect($genresList)->pluck('name', 'id')->toArray();
        $genreCategoryMap = $this->localService->getGenreCategoryMap($genresList);

        $localMovies = $this->localService->getLocalMedia('movie', $filters, $genreCategoryMap);

        $tmdbNeeded = $this->calculateTmdbNeeded(count($localMovies), $page, $perPage);
        $tmdbData = $tmdbNeeded > 0 ? $this->tmdbService->getMedia('movie', $filters, $page) : ['media' => [], 'total_pages' => 1];
        $tmdbMovies = $tmdbData['media'];
        $tmdbTotalPages = $tmdbData['total_pages'];

        $bannedMovies = BannedMovie::all();
        $bannedTmdbIds = $bannedMovies->pluck('tmdb_id')->filter()->toArray();
        $bannedLocalIds = $bannedMovies->pluck('movie_id')->filter()->toArray();

        $filteredLocalMovies = array_filter($localMovies, fn($movie) => !in_array($movie['id'] ?? null, $bannedLocalIds));
        $filteredTmdbMovies = array_filter($tmdbMovies, fn($movie) => !in_array($movie['id'] ?? null, $bannedTmdbIds));

        $offset = ($page - 1) * $perPage;
        $localSlice = array_slice($filteredLocalMovies, $offset, $perPage);
        $tmdbSlice = $tmdbNeeded > 0 ? array_slice($filteredTmdbMovies, 0, $tmdbNeeded) : [];
        $movies = array_merge($localSlice, $tmdbSlice);

        $totalItems = max(count($localMovies), $tmdbTotalPages * $perPage);
        $totalPages = ceil($totalItems / $perPage);

        Log::info('Movies fetched', [
            'total_pages' => $totalPages,
            'filters' => $filters
        ]);

        return view('Front-office.movies', [
            'content' => $movies,
            'genres' => $genresList,
            'genreMap' => $genreMap,
            'selectedGenres' => $filters['genres'],
            'search' => is_array($filters['search']) ? '' : $filters['search'],
            'year' => is_array($filters['year']) ? '' : $filters['year'],
            'rating' => is_array($filters['rating']) ? '' : $filters['rating'],
            'sort' => $filters['sort'],
            'order' => $filters['order'],
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
        $movieData = $this->tmdbService->getMediaDetails('movie', $id);
        if ($movieData) {
            $isInList = Auth::check() && UserMovieList::where('user_id', Auth::id())
                ->where('tmdb_id', $id)
                ->exists();
            $downloadLink = $this->torrentService->fetchDownloadLink($movieData['title']);
            return view('Front-office.details', compact('movieData', 'downloadLink', 'isInList'));
        }
        return redirect()->route('movies')->with('error', 'Movie not found.');
    }

    public function fetchDownloadLink(Request $request)
    {
        if (Gate::denies('download-content')) {
            return response()->json(['error' => 'You need a premium subscription to download content.'], 403);
        }

        $movieTitle = $request->input('title');
        $downloadLink = $this->torrentService->fetchDownloadLink($movieTitle);

        return response()->json(['download_link' => $downloadLink]);
    }

    public function localMovieDetails($id)
    {
        try {
            $data = $this->localService->getLocalMediaDetails('movie', $id, $this->tmdbService);
            $data['isInList'] = Auth::check() && UserMovieList::where('user_id', Auth::id())
                ->where('tmdb_id', $id)
                ->exists();
            return view('Front-office.local', $data);
        } catch (\Exception $e) {
            return view('errors.500');
        }
    }

    private function normalizeGenres($genres): array
    {
        return is_array($genres) ? array_filter($genres) : ($genres ? explode(',', $genres) : []);
    }

    private function calculateTmdbNeeded(int $localCount, int $page, int $perPage): int
    {
        $offset = ($page - 1) * $perPage;
        $localRemaining = max(0, $localCount - $offset);
        return max(0, $perPage - min($localRemaining, $perPage));
    }
}