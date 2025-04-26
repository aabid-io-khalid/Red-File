<?php

namespace App\Http\Controllers;

use App\Services\TmdbService;
use App\Services\CloudinaryService;
use App\Services\TvShowService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AdminTvShowController extends Controller
{
    private $tmdbService;
    private $cloudinaryService;
    private $tvShowService;

    public function __construct(TmdbService $tmdbService, CloudinaryService $cloudinaryService, TvShowService $tvShowService)
    {
        $this->tmdbService = $tmdbService;
        $this->cloudinaryService = $cloudinaryService;
        $this->tvShowService = $tvShowService;
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
            $localSeries = $this->tvShowService->getLocalSeries();
            $bannedApiSeriesIds = $this->tvShowService->getBannedApiSeriesIds();
        } catch (\Exception $e) {
            $hasDbError = true;
        }

        try {
            $apiData = $this->tmdbService->getMedia('tv', [], $apiPage);
            $apiSeries = $apiData['media'];
            $totalPages = $apiData['total_pages'];
            $totalResults = $apiData['total_results'];

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
        } catch (\Exception $e) {
            Log::error('Failed to fetch TMDB series', ['error' => $e->getMessage()]);
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
                $posterUrl = $this->cloudinaryService->uploadPoster($request->file('poster'));
            }

            $this->tvShowService->createTvShow($validated, $posterUrl);

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
                'seasons' => 'nullable|integer|min:1',
                'episodes_per_season' => 'nullable|integer|min:1',
            ]);

            $tvShow = $this->tvShowService->getTvShowForEdit($id);
            $posterUrl = $tvShow->poster;

            if ($request->hasFile('poster')) {
                $this->cloudinaryService->deletePoster($tvShow->poster);
                $posterUrl = $this->cloudinaryService->uploadPoster($request->file('poster'), "series_{$id}");
            }

            $this->tvShowService->updateTvShow($tvShow, $validated, $posterUrl);

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
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $tvShow = $this->tvShowService->getTvShowForEdit($id);
            return response()->json([
                'success' => true,
                'tvShow' => $tvShow,
                'categories' => $tvShow->categories->pluck('name')->implode(', ')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch TV Show: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $tvShow = $this->tvShowService->getTvShowForEdit($id);
            $this->cloudinaryService->deletePoster($tvShow->poster);
            $this->tvShowService->deleteTvShow($id);

            return response()->json([
                'success' => true,
                'message' => 'TV Show deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete TV Show: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleBan($id, Request $request)
    {
        try {
            $isTmdb = $request->input('is_tmdb', false);
            $isBanned = $request->input('is_banned', false);
            $reason = $request->input('reason', 'Violation of guidelines');

            $message = $this->tvShowService->toggleBan($id, $isTmdb, $isBanned, $reason);

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