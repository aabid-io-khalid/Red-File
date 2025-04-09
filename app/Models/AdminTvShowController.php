<?php

namespace App\Http\Controllers;

use App\Models\TvShow;
use App\Models\Category;
use App\Models\BannedTvShow;
use Cloudinary\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AdminTvShowController extends Controller
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
        $localSeries = collect();
        $apiSeries = [];
        $bannedApiSeriesIds = [];
        $apiPage = $request->input('page', 1);
        $apiSeriesPaginator = null;

        try {
            Log::info('Attempting to fetch local series');
            $localSeries = TvShow::with('categories')->orderBy('created_at', 'desc')->get();
            Log::info('Successfully fetched local series', ['count' => $localSeries->count()]);
            
            Log::info('Attempting to fetch banned API series IDs');
            $bannedApiSeriesIds = BannedTvShow::where('is_tmdb', true)
                ->pluck('tmdb_id')
                ->toArray();
            Log::info('Successfully fetched banned API series IDs', ['count' => count($bannedApiSeriesIds)]);
        } catch (\Exception $e) {
            Log::error('Database error when fetching local series', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $hasDbError = true;
        }

        try {
            Log::info('Attempting to fetch series from TMDB API', ['page' => $apiPage]);
            $response = Http::get("{$this->baseUrl}/tv/popular", [
                'api_key' => $this->apiKey,
                'language' => 'en-US',
                'page' => $apiPage,
            ]);

            if ($response->successful()) {
                $apiData = $response->json();
                $apiSeries = $apiData['results'];
                $totalPages = $apiData['total_pages'] ?? 1;
                $totalResults = $apiData['total_results'] ?? count($apiSeries);
                Log::info('Successfully fetched TMDB series', [
                    'count' => count($apiSeries),
                    'totalResults' => $totalResults
                ]);

                $apiSeriesPaginator = new LengthAwarePaginator(
                    $apiSeries,
                    $totalResults,
                    count($apiSeries) > 0 ? count($apiSeries) : 1,
                    $apiPage,
                    [
                        'path' => route('admin.series'),
                        'query' => ['tab' => 'api']
                    ]
                );
            } else {
                Log::error('Failed to fetch series from TMDB', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('API error when fetching TMDB series', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        Log::info('Rendering admin.series view', [
            'hasDbError' => $hasDbError,
            'localSeriesCount' => $localSeries->count(),
            'apiSeriesCount' => count($apiSeries),
            'apiSeriesPaginatorExists' => !is_null($apiSeriesPaginator)
        ]);

        return view('admin.series', compact(
            'localSeries',
            'apiSeries',
            'apiSeriesPaginator',
            'bannedApiSeriesIds',
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
                'seasons' => 'nullable|integer|min:1',
                'episodes_per_season' => 'nullable|integer|min:1',
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
                            'folder' => 'series',
                            'public_id' => 'series_' . time(),
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
    
            $tvShow = TvShow::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? '',
                'year' => $validated['year'] ?? null,
                'rating' => $validated['rating'] ?? null,
                'poster' => $posterUrl,
                'is_banned' => false,
                'seasons' => $validated['seasons'] ?? null,
                'episodes_per_season' => $validated['episodes_per_season'] ?? null,
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
    
            $tvShow->categories()->sync($categoryIds);
    
            return response()->json([
                'success' => true,
                'message' => 'TV Show created successfully'
            ], 201);
    
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('TV Show creation error: ' . $e->getMessage());
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
            'categories' => 'required|string', // Comma-separated string is fine
            'seasons' => 'nullable|integer|min:1',
            'episodes_per_season' => 'nullable|integer|min:1',
        ]);

        $tvShow = TvShow::findOrFail($id);
        $posterUrl = $tvShow->poster;

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

                if ($tvShow->poster) {
                    $publicId = pathinfo($tvShow->poster, PATHINFO_FILENAME);
                    $cloudinary->uploadApi()->destroy($publicId, ['resource_type' => 'image']);
                    Log::info('Old poster deleted from Cloudinary', ['public_id' => $publicId]);
                }

                $uploadResult = $cloudinary->uploadApi()->upload(
                    $request->file('poster')->getRealPath(),
                    [
                        'folder' => 'series',
                        'public_id' => 'series_' . $id . '_' . time(),
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
                    'tv_show_id' => $id,
                    'file' => $request->file('poster')->getClientOriginalName(),
                    'size' => $request->file('poster')->getSize()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Image update failed: ' . $e->getMessage()
                ], 500);
            }
        }

        $tvShow->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? '',
            'year' => $validated['year'] ?? null,
            'rating' => $validated['rating'] ?? null,
            'poster' => $posterUrl,
            'seasons' => $validated['seasons'] ?? $tvShow->seasons,
            'episodes_per_season' => $validated['episodes_per_season'] ?? $tvShow->episodes_per_season,
        ]);

        $categoryNames = array_filter(
            array_map('trim', explode(',', $validated['categories'])),
            fn($name) => !empty($name)
        );

        if (empty($categoryNames)) {
            return response()->json([
                'success' => false,
                'message' => 'At least one category is required',
                'errors' => ['categories' => ['The categories field must contain at least one valid category.']]
            ], 422);
        }

        $categoryIds = [];
        foreach ($categoryNames as $name) {
            $category = Category::firstOrCreate(['name' => $name]);
            $categoryIds[] = $category->id;
        }

        $tvShow->categories()->sync($categoryIds);

        return response()->json([
            'success' => true,
            'message' => 'TV Show updated successfully'
        ]);

    } catch (ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validation error',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        Log::error('TV Show update error: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'request' => $request->all()
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Server error: ' . $e->getMessage()
        ], 500);
    }
}

    public function edit($id)
    {
        try {
            $tvShow = TvShow::with('categories')->findOrFail($id);
            return response()->json([
                'success' => true,
                'tvShow' => $tvShow,
                'categories' => $tvShow->categories->pluck('name')->implode(', ')
            ]);
        } catch (\Exception $e) {
            Log::error('Database error in edit method', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch TV Show: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function destroy($id)
    {
        try {
            Log::info('Attempting to delete TV Show', ['id' => $id]);
            $tvShow = TvShow::findOrFail($id);

            // Delete poster from Cloudinary if exists
            if ($tvShow->poster) {
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

                    $publicId = pathinfo($tvShow->poster, PATHINFO_FILENAME);
                    $cloudinary->uploadApi()->destroy($publicId, ['resource_type' => 'image']);
                    Log::info('Poster deleted from Cloudinary', ['public_id' => $publicId]);
                } catch (\Exception $e) {
                    Log::error('Failed to delete poster from Cloudinary', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'tv_show_id' => $id
                    ]);
                    // Continue with deletion even if Cloudinary fails
                }
            }

            $tvShow->delete();

            Log::info('TV Show deleted successfully', ['id' => $id]);
            return response()->json(['success' => true, 'message' => 'TV Show deleted successfully.']);
        } catch (\Exception $e) {
            Log::error('Database error in destroy method', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'id' => $id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete TV Show: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleBan($id, Request $request)
    {
        $isTmdb = $request->input('is_tmdb', false);
        $isBanned = $request->input('is_banned', false);

        Log::info('Toggle ban request received for TV Show', [
            'id' => $id,
            'is_tmdb' => $isTmdb,
            'is_banned' => $isBanned,
            'request_data' => $request->all()
        ]);

        try {
            if ($isTmdb) {
                $existingBan = BannedTvShow::where('tmdb_id', $id)->first();
                if ($existingBan && !$isBanned) {
                    $existingBan->delete();
                    $message = 'TMDB TV Show unbanned successfully.';
                    Log::info('Removed TMDB TV Show ban', ['tmdb_id' => $id]);
                } elseif (!$existingBan && $isBanned) {
                    BannedTvShow::create([
                        'tmdb_id' => $id,
                        'is_tmdb' => true,
                        'reason' => $request->input('reason', 'Violation of guidelines')
                    ]);
                    $message = 'TMDB TV Show banned successfully.';
                    Log::info('Banned TMDB TV Show', ['tmdb_id' => $id]);
                } else {
                    $message = 'No change in TMDB TV Show ban status.';
                }
            } else {
                $existingBan = BannedTvShow::where('tv_show_id', $id)->first();
                if ($existingBan && !$isBanned) {
                    $existingBan->delete();
                    $message = 'Local TV Show unbanned successfully.';
                    Log::info('Removed local TV Show ban', ['tv_show_id' => $id]);
                } elseif (!$existingBan && $isBanned) {
                    BannedTvShow::create([
                        'tv_show_id' => $id,
                        'is_tmdb' => false,
                        'reason' => $request->input('reason', 'Violation of guidelines')
                    ]);
                    $message = 'Local TV Show banned successfully.';
                    Log::info('Banned local TV Show', ['tv_show_id' => $id]);
                } else {
                    $message = 'No change in local TV Show ban status.';
                }
            }

            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            Log::error('Error in toggleBan for TV Show', [
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