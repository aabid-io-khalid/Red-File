<?php

namespace App\Http\Controllers;

use App\Services\TmdbService;
use App\Services\BrowseService;
use App\Services\GenreMappingService;
use App\Services\LocalMediaService;
use App\Services\TorrentService;
use App\Models\UserTvShowList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class TvShowController extends Controller
{
    private $tmdbService;
    private $browseService;
    private $genreMappingService;
    private $localMediaService;
    private $torrentService;

    public function __construct(
        TmdbService $tmdbService,
        BrowseService $browseService,
        GenreMappingService $genreMappingService,
        LocalMediaService $localMediaService,
        TorrentService $torrentService
    ) {
        $this->tmdbService = $tmdbService;
        $this->browseService = $browseService;
        $this->genreMappingService = $genreMappingService;
        $this->localMediaService = $localMediaService;
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

        // Fetch genres and mappings
        $genresList = $this->genreMappingService->getGenres('tv');
        $genreMap = $this->genreMappingService->getGenreMap('tv');
        $genreCategoryMap = $this->genreMappingService->getGenreCategoryMap('tv');

        // Fetch local content
        $localContent = $this->browseService->getLocalContent('tv', $filters, $genreCategoryMap, $filters['sort'], $filters['order']);
        $localTvShows = $localContent['shows'];

        // Calculate TMDB needs
        $tmdbNeeded = $this->calculateTmdbNeeded(count($localTvShows), $page, $perPage);
        $tmdbData = $tmdbNeeded > 0 ? $this->tmdbService->getMedia('tv', $filters, $page) : ['media' => [], 'total_pages' => 1];
        $tmdbTvShows = $tmdbData['media'];
        $tmdbTotalPages = $tmdbData['total_pages'];

        // Get banned IDs
        $bannedIds = $this->browseService->getBannedIds();

        // Combine content
        $combined = $this->browseService->combineContent(
            'tv',
            [], // No local movies
            $localTvShows,
            [], // No TMDB movies
            $tmdbTvShows,
            $bannedIds,
            $page,
            $perPage
        );

        Log::info('TV Shows fetched', [
            'total_pages' => $combined['total_pages'],
            'filters' => $filters,
            'local_count' => count($localTvShows),
            'tmdb_count' => count($tmdbTvShows),
            'merged_count' => count($combined['content'])
        ]);

        return view('Front-office.shows', [
            'content' => $combined['content'],
            'genres' => $genresList,
            'genreMap' => $genreMap,
            'selectedGenres' => $filters['genres'],
            'search' => is_array($filters['search']) ? '' : $filters['search'],
            'year' => is_array($filters['year']) ? '' : $filters['year'],
            'rating' => is_array($filters['rating']) ? '' : $filters['rating'],
            'sort' => $filters['sort'],
            'order' => $filters['order'],
            'page' => $page,
            'totalPages' => $combined['total_pages'],
        ]);
    }

    public function showTvShowDetails($id)
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('TMDB Show Details Error', ['id' => $id, 'error' => $e->getMessage()]);
            return view('errors.500');
        }
    }

    public function localShowDetails($id)
    {
        try {
            $data = $this->localMediaService->getLocalMediaDetails('tv', $id, $this->tmdbService);
            $data['isInList'] = Auth::check() && UserTvShowList::where('user_id', Auth::id())
                ->where('tmdb_id', $data['media']['tmdb_id'] ?? $id)
                ->exists();
            Log::debug('Local Show Details Data', [
                'id' => $id,
                'media' => $data['media'],
                'noInfoAvailable' => $data['noInfoAvailable'],
                'isInList' => $data['isInList']
            ]);
            return view('Front-office.local', $data);
        } catch (\Exception $e) {
            Log::error('Local Show Details Error', ['id' => $id, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return view('errors.500');
        }
    }

    public function apiEpisodeDownload(Request $request)
    {
        $title = $request->query('title');
        $seasonNumber = $request->query('season');
        $episodeNumber = $request->query('episode');
        $isAnime = $request->query('is_anime', false);

        if (!$title || !$seasonNumber || !$episodeNumber) {
            return response()->json(['error' => 'Missing required parameters'], 400);
        }

        if (Gate::denies('download-content')) {
            return response()->json(['error' => 'You must be a premium user to download content.'], 403);
        }

        $downloadLink = $this->torrentService->fetchEpisodeDownloadLink($title, (int)$seasonNumber, (int)$episodeNumber, $isAnime);

        return response()->json([
            'success' => $downloadLink ? true : false,
            'download_link' => $downloadLink,
        ]);
    }

    private function normalizeGenres($genres): array
    {
        return is_array($genres) ? array_filter($genres, 'is_numeric') : ($genres ? array_filter(explode(',', $genres), 'is_numeric') : []);
    }

    private function calculateTmdbNeeded(int $localCount, int $page, int $perPage): int
    {
        $offset = ($page - 1) * $perPage;
        $localRemaining = max(0, $localCount - $offset);
        return max(0, $perPage - min($localRemaining, $perPage));
    }
}