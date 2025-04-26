<?php

namespace App\Http\Controllers;

use App\Services\TmdbService;
use App\Services\LocalMediaService;
use App\Services\TorrentService;
use App\Models\BannedTvShow;
use App\Models\UserTvShowList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class TvShowController extends Controller
{
    private $tmdbService;
    private $localService;
    private $torrentService;

    public function __construct(TmdbService $tmdbService, LocalMediaService $localService, TorrentService $torrentService)
    {
        $this->tmdbService = $tmdbService;
        $this->localService = $localService;
        $this->torrentService = $torrentService;
    }

    public function index()
    {
        return view('Front-office.home');
    }

    public function shows(Request $request)
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

        $genresList = $this->tmdbService->getGenres('tv');
        $genreMap = collect($genresList)->pluck('name', 'id')->toArray();
        $genreCategoryMap = $this->localService->getGenreCategoryMap($genresList);

        $localTvShows = $this->localService->getLocalMedia('tv', $filters, $genreCategoryMap);

        $tmdbNeeded = $this->calculateTmdbNeeded(count($localTvShows), $page, $perPage);
        $tmdbData = $tmdbNeeded > 0 ? $this->tmdbService->getMedia('tv', $filters, $page) : ['media' => [], 'total_pages' => 1];
        $tmdbTvShows = $tmdbData['media'];
        $tmdbTotalPages = $tmdbData['total_pages'];

        $bannedTvShows = BannedTvShow::all();
        $bannedTmdbIds = $bannedTvShows->pluck('tmdb_id')->filter()->toArray();
        $bannedLocalIds = $bannedTvShows->pluck('tv_show_id')->filter()->toArray();

        $filteredLocalTvShows = array_filter($localTvShows, fn($tvShow) => !in_array($tvShow['id'] ?? null, $bannedLocalIds));
        $filteredTmdbTvShows = array_filter($tmdbTvShows, fn($tvShow) => !in_array($tvShow['id'] ?? null, $bannedTmdbIds));

        $offset = ($page - 1) * $perPage;
        $localSlice = array_slice($filteredLocalTvShows, $offset, $perPage);
        $tmdbSlice = $tmdbNeeded > 0 ? array_slice($filteredTmdbTvShows, 0, $tmdbNeeded) : [];
        $tvShows = array_merge($localSlice, $tmdbSlice);

        $totalItems = max(count($localTvShows), $tmdbTotalPages * $perPage);
        $totalPages = ceil($totalItems / $perPage);

        Log::info('TV Shows fetched', [
            'total_pages' => $totalPages,
            'filters' => $filters
        ]);

        return view('Front-office.shows', [
            'content' => $tvShows,
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

    public function showTvShowDetails($id)
    {
        $show = $this->tmdbService->getMediaDetails('tv', $id);
        if ($show) {
            $isInList = Auth::check() && UserTvShowList::where('user_id', Auth::id())
                ->where('tmdb_id', $id)
                ->exists();
            return view('Front-office.informations', [
                'show' => $show,
                'isInList' => $isInList,
                'noInfoAvailable' => false
            ]);
        }
        return redirect()->route('tv-shows.shows')->with('error', 'TV Show not found.');
    }

    public function localShowDetails($id)
    {
        try {
            $data = $this->localService->getLocalMediaDetails('tv', $id, $this->tmdbService);
            $data['isInList'] = Auth::check() && UserTvShowList::where('user_id', Auth::id())
                ->where('tmdb_id', $id)
                ->exists();
            return view('Front-office.local', $data);
        } catch (\Exception $e) {
            return view('errors.500');
        }
    }

    public function apiEpisodeDownload(Request $request)
    {
        $title = $request->query('title');
        $seasonNumber = $request->query('season');
        $episodeNumber = $request->query('episode');

        if (!$title || !$seasonNumber || !$episodeNumber) {
            return response()->json(['error' => 'Missing required parameters'], 400);
        }

        if (Gate::denies('download-content')) {
            return response()->json(['error' => 'You must be a premium user to download content.'], 403);
        }

        $downloadLink = $this->torrentService->fetchEpisodeDownloadLink($title, $seasonNumber, $episodeNumber);

        return response()->json([
            'success' => $downloadLink ? true : false,
            'download_link' => $downloadLink,
        ]);
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