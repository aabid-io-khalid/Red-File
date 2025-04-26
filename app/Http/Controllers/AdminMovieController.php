<?php

namespace App\Http\Controllers;

use App\Services\TmdbService;
use App\Services\CloudinaryService;
use App\Services\MovieService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AdminMovieController extends Controller
{
    private $tmdbService;
    private $cloudinaryService;
    private $movieService;

    public function __construct(TmdbService $tmdbService, CloudinaryService $cloudinaryService, MovieService $movieService)
    {
        $this->tmdbService = $tmdbService;
        $this->cloudinaryService = $cloudinaryService;
        $this->movieService = $movieService;
    }

    public function index(Request $request)
    {
        $hasDbError = false;
        $localMovies = collect();
        $apiMovies = [];
        $bannedApiMovieIds = [];
        $apiPage = $request->input('page', 1);
        $apiMoviesPaginator = null;

        try {
            $localMovies = $this->movieService->getLocalMovies();
            $bannedApiMovieIds = $this->movieService->getBannedApiMovieIds();
        } catch (\Exception $e) {
            $hasDbError = true;
        }

        try {
            $apiData = $this->tmdbService->getMedia('movie', [], $apiPage);
            $apiMovies = $apiData['media'];
            $totalPages = $apiData['total_pages'];
            $totalResults = $apiData['total_results'];

            $apiMoviesPaginator = new LengthAwarePaginator(
                $apiMovies,
                $totalResults,
                count($apiMovies) > 0 ? count($apiMovies) : 1,
                $apiPage,
                [
                    'path' => route('admin.movies.index'),
                    'query' => ['tab' => 'api']
                ]
            );
        } catch (\Exception $e) {
            Log::error('Failed to fetch TMDB movies', ['error' => $e->getMessage()]);
        }

        Log::info('Rendering admin.movies view', [
            'hasDbError' => $hasDbError,
            'localMoviesCount' => $localMovies->count(),
            'apiMoviesCount' => count($apiMovies),
            'apiMoviesPaginatorExists' => !is_null($apiMoviesPaginator)
        ]);

        return view('admin.movies', compact(
            'localMovies',
            'apiMovies',
            'apiMoviesPaginator',
            'bannedApiMovieIds',
            'hasDbError',
            'apiPage'
        ));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'year' => 'nullable|integer|min:1900|max:' . date('Y'),
                'rating' => 'nullable|numeric|min:0|max:10',
                'poster' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'categories' => 'required|string',
            ]);

            $posterUrl = null;
            if ($request->hasFile('poster')) {
                $posterUrl = $this->cloudinaryService->uploadPoster($request->file('poster'), 'movie');
            }

            $movie = $this->movieService->createMovie($validated, $posterUrl);

            return response()->json([
                'success' => true,
                'message' => 'Movie created successfully.',
                'movie' => $movie->load('categories')
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'year' => 'nullable|integer|min:1900|max:' . date('Y'),
                'rating' => 'nullable|numeric|min:0|max:10',
                'poster' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'categories' => 'required|string',
            ]);

            $movie = $this->movieService->getMovieForEdit($id);
            $posterUrl = $movie->poster;

            if ($request->hasFile('poster')) {
                $this->cloudinaryService->deletePoster($movie->poster);
                $posterUrl = $this->cloudinaryService->uploadPoster($request->file('poster'), "movie_{$id}");
            }

            $movie = $this->movieService->updateMovie($movie, $validated, $posterUrl);

            return response()->json([
                'success' => true,
                'message' => 'Movie updated successfully.',
                'movie' => $movie->load('categories')
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $movie = $this->movieService->getMovieForEdit($id);
            return response()->json([
                'success' => true,
                'movie' => $movie,
                'categories' => $movie->categories->pluck('name')->implode(', ')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch movie: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $movie = $this->movieService->getMovieForEdit($id);
            $this->cloudinaryService->deletePoster($movie->poster);
            $this->movieService->deleteMovie($id);

            return response()->json([
                'success' => true,
                'message' => 'Movie deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete movie: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleBan($id, Request $request)
    {
        try {
            $isTmdb = $request->input('is_tmdb', false);
            $isBanned = $request->input('is_banned', false);
            $reason = $request->input('reason', 'Violation of guidelines');

            $message = $this->movieService->toggleBan($id, $isTmdb, $isBanned, $reason);

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update ban status: ' . $e->getMessage()
            ], 500);
        }
    }
}