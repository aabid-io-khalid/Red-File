<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Category;
use App\Models\BannedMovie;
use Cloudinary\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AdminMovieController extends Controller
{
    private $apiKey;
    private $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('TMDB_API_KEY');
        $this->baseUrl = 'https://api.themoviedb.org/3';
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
            Log::info('Attempting to fetch local movies');
            $localMovies = Movie::with('categories')->orderBy('created_at', 'desc')->get();
            Log::info('Successfully fetched local movies', ['count' => $localMovies->count()]);
            
            Log::info('Attempting to fetch banned API movie IDs');
            $bannedApiMovieIds = BannedMovie::where('is_tmdb', true)
                ->pluck('tmdb_id')
                ->toArray();
            Log::info('Successfully fetched banned API movie IDs', ['count' => count($bannedApiMovieIds)]);
        } catch (\Exception $e) {
            Log::error('Database error when fetching local movies', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $hasDbError = true;
        }

        try {
            Log::info('Attempting to fetch movies from TMDB API', ['page' => $apiPage]);
            $response = Http::get("{$this->baseUrl}/movie/popular", [
                'api_key' => $this->apiKey,
                'language' => 'en-US',
                'page' => $apiPage,
            ]);

            if ($response->successful()) {
                $apiData = $response->json();
                $apiMovies = $apiData['results'];
                $totalPages = $apiData['total_pages'] ?? 1;
                $totalResults = $apiData['total_results'] ?? count($apiMovies);
                Log::info('Successfully fetched TMDB movies', [
                    'count' => count($apiMovies),
                    'totalResults' => $totalResults
                ]);

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
            } else {
                Log::error('Failed to fetch movies from TMDB', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('API error when fetching TMDB movies', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
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
                Log::info('File received', [
                    'path' => $request->file('poster')->getRealPath(),
                    'size' => $request->file('poster')->getSize(),
                    'mime' => $request->file('poster')->getMimeType(),
                    'name' => $request->file('poster')->getClientOriginalName()
                ]);

                try {
                    $cloudinary = new Cloudinary([
                        'cloud' => [
                            'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                            'api_key' => env('CLOUDINARY_API_KEY'),
                            'api_secret' => env('CLOUDINARY_API_SECRET'),
                        ],
                        'url' => [
                            'secure' => true,
                        ]
                    ]);

                    $uploadResult = $cloudinary->uploadApi()->upload(
                        $request->file('poster')->getRealPath(),
                        [
                            'folder' => 'movies',
                            'public_id' => 'movie_' . time(),
                            'resource_type' => 'image'
                        ]
                    );

                    Log::info('Cloudinary raw response', ['response' => $uploadResult]);

                    if (!isset($uploadResult['secure_url'])) {
                        throw new \Exception('Cloudinary returned an invalid or empty response');
                    }

                    $posterUrl = $uploadResult['secure_url'];
                } catch (\Exception $e) {
                    Log::error('Cloudinary upload failed', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'file' => $request->file('poster')->getClientOriginalName(),
                        'size' => $request->file('poster')->getSize()
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Image upload failed: ' . $e->getMessage()
                    ], 500);
                }
            }

            $movie = Movie::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? '',
                'year' => $validated['year'] ?? null,
                'rating' => $validated['rating'] ?? null,
                'poster' => $posterUrl,
                'is_banned' => false,
            ]);

            $categoryNames = array_filter(
                array_map('trim', explode(',', $validated['categories'])),
                function($name) { return !empty($name); }
            );

            $categoryIds = [];
            foreach ($categoryNames as $name) {
                $category = Category::firstOrCreate(['name' => $name]);
                $categoryIds[] = $category->id;
            }

            $movie->categories()->sync($categoryIds);

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
            Log::error('Movie creation error: ' . $e->getMessage());
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

            $movie = Movie::findOrFail($id);
            $posterUrl = $movie->poster;

            if ($request->hasFile('poster')) {
                Log::info('File received for update', [
                    'path' => $request->file('poster')->getRealPath(),
                    'size' => $request->file('poster')->getSize(),
                    'mime' => $request->file('poster')->getMimeType(),
                    'name' => $request->file('poster')->getClientOriginalName()
                ]);

                try {
                    $cloudinary = new Cloudinary([
                        'cloud' => [
                            'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                            'api_key' => env('CLOUDINARY_API_KEY'),
                            'api_secret' => env('CLOUDINARY_API_SECRET'),
                        ],
                        'url' => [
                            'secure' => true,
                        ]
                    ]);

                    // Delete old image if exists
                    if ($movie->poster) {
                        $publicId = pathinfo($movie->poster, PATHINFO_FILENAME);
                        $cloudinary->uploadApi()->destroy($publicId, ['resource_type' => 'image']);
                        Log::info('Old poster deleted from Cloudinary', ['public_id' => $publicId]);
                    }

                    $uploadResult = $cloudinary->uploadApi()->upload(
                        $request->file('poster')->getRealPath(),
                        [
                            'folder' => 'movies',
                            'public_id' => 'movie_' . $id . '_' . time(),
                            'resource_type' => 'image'
                        ]
                    );

                    Log::info('Cloudinary raw response for update', ['response' => $uploadResult]);

                    if (!isset($uploadResult['secure_url'])) {
                        throw new \Exception('Cloudinary returned an invalid or empty response');
                    }

                    $posterUrl = $uploadResult['secure_url'];
                } catch (\Exception $e) {
                    Log::error('Cloudinary update failed', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'movie_id' => $id,
                        'file' => $request->file('poster')->getClientOriginalName(),
                        'size' => $request->file('poster')->getSize()
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Image update failed: ' . $e->getMessage()
                    ], 500);
                }
            }

            $movie->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? '',
                'year' => $validated['year'] ?? null,
                'rating' => $validated['rating'] ?? null,
                'poster' => $posterUrl,
            ]);

            $categoryNames = array_filter(
                array_map('trim', explode(',', $validated['categories'])),
                function($name) { return !empty($name); }
            );

            $categoryIds = [];
            foreach ($categoryNames as $name) {
                $category = Category::firstOrCreate(['name' => $name]);
                $categoryIds[] = $category->id;
            }

            $movie->categories()->sync($categoryIds);

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
            Log::error('Movie update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $movie = Movie::with('categories')->findOrFail($id);
            return response()->json([
                'success' => true,
                'movie' => $movie,
                'categories' => $movie->categories->pluck('name')->implode(', ')
            ]);
        } catch (\Exception $e) {
            Log::error('Database error in edit method', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch movie: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function destroy($id)
    {
        try {
            Log::info('Attempting to delete movie', ['id' => $id]);
            $movie = Movie::findOrFail($id);

            // Delete poster from Cloudinary if exists
            if ($movie->poster) {
                try {
                    $cloudinary = new Cloudinary([
                        'cloud' => [
                            'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                            'api_key' => env('CLOUDINARY_API_KEY'),
                            'api_secret' => env('CLOUDINARY_API_SECRET'),
                        ],
                        'url' => [
                            'secure' => true,
                        ]
                    ]);

                    $publicId = pathinfo($movie->poster, PATHINFO_FILENAME);
                    $cloudinary->uploadApi()->destroy($publicId, ['resource_type' => 'image']);
                    Log::info('Poster deleted from Cloudinary', ['public_id' => $publicId]);
                } catch (\Exception $e) {
                    Log::error('Failed to delete poster from Cloudinary', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'movie_id' => $id
                    ]);
                    // Continue with deletion even if Cloudinary fails
                }
            }

            $movie->delete();

            Log::info('Movie deleted successfully', ['id' => $id]);
            return response()->json(['success' => true, 'message' => 'Movie deleted successfully.']);
        } catch (\Exception $e) {
            Log::error('Database error in destroy method', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'id' => $id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete movie: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleBan($id, Request $request)
    {
        $isTmdb = $request->input('is_tmdb', false);
        $isBanned = $request->input('is_banned', false);

        Log::info('Toggle ban request received', [
            'id' => $id,
            'is_tmdb' => $isTmdb,
            'is_banned' => $isBanned,
            'request_data' => $request->all()
        ]);

        try {
            if ($isTmdb) {
                $existingBan = BannedMovie::where('tmdb_id', $id)->first();
                if ($existingBan && !$isBanned) {
                    $existingBan->delete();
                    $message = 'TMDB movie unbanned successfully.';
                    Log::info('Removed TMDB ban', ['tmdb_id' => $id]);
                } elseif (!$existingBan && $isBanned) {
                    BannedMovie::create([
                        'tmdb_id' => $id,
                        'is_tmdb' => true,
                        'reason' => $request->input('reason', 'Violation of guidelines')
                    ]);
                    $message = 'TMDB movie banned successfully.';
                    Log::info('Banned TMDB movie', ['tmdb_id' => $id]);
                } else {
                    $message = 'No change in TMDB ban status.';
                }
            } else {
                $existingBan = BannedMovie::where('movie_id', $id)->first();
                if ($existingBan && !$isBanned) {
                    $existingBan->delete();
                    $message = 'Local movie unbanned successfully.';
                    Log::info('Removed local ban', ['movie_id' => $id]);
                } elseif (!$existingBan && $isBanned) {
                    BannedMovie::create([
                        'movie_id' => $id,
                        'is_tmdb' => false,
                        'reason' => $request->input('reason', 'Violation of guidelines')
                    ]);
                    $message = 'Local movie banned successfully.';
                    Log::info('Banned local movie', ['movie_id' => $id]);
                } else {
                    $message = 'No change in local ban status.';
                }
            }

            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            Log::error('Error in toggleBan', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update ban status: ' . $e->getMessage()
            ], 500);
        }
    }
}