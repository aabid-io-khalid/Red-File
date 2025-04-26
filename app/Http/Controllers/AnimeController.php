<?php

namespace App\Http\Controllers;

use App\Services\TmdbService;
use App\Services\TorrentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\UserTvShowList;

class AnimeController extends Controller
{
    private $tmdbService;
    private $torrentService;

    public function __construct(TmdbService $tmdbService, TorrentService $torrentService)
    {
        $this->tmdbService = $tmdbService;
        $this->torrentService = $torrentService;
    }

    public function filteredAnime(Request $request)
    {
        try {
            $genres = $this->tmdbService->getGenres('tv');
            $filters = [
                'search' => $request->input('search', ''),
                'genres' => ['16'], // Anime genre ID
                'year' => $request->input('year', ''),
                'rating' => $request->input('rating', ''),
                'sort' => $request->input('sort', 'popularity'),
                'order' => $request->input('order', 'desc')
            ];
            $page = max(1, (int)$request->input('page', 1));

            $data = $this->tmdbService->getMedia('tv', $filters, $page);

            return view('front-office.anime', [
                'animes' => $data['media'] ?? [],
                'genres' => $genres,
                'totalPages' => $data['total_pages'] ?? 1,
                'currentPage' => $page,
                'filters' => $request->all(),
                'error' => null
            ]);
        } catch (\Exception $e) {
            Log::error('AnimeController Error', ['error' => $e->getMessage()]);
            return view('front-office.anime', [
                'animes' => [],
                'genres' => [],
                'totalPages' => 1,
                'currentPage' => 1,
                'filters' => [],
                'error' => 'Failed to load anime data. Please try again later.'
            ]);
        }
    }

    public function show($id)
    {
        try {
            $showDetails = $this->tmdbService->getMediaDetails('tv', $id);
            if (!$showDetails) {
                throw new \Exception('Failed to fetch show details from TMDB');
            }

            $isInList = Auth::check() && UserTvShowList::where('user_id', Auth::id())
                ->where('tmdb_id', $id)
                ->exists();

            $downloadLink = null;
            if (isset($showDetails['seasons']) && count($showDetails['seasons']) > 0) {
                $seasonNumber = 1;
                $episodeNumber = 1;
                $downloadLink = $this->torrentService->fetchEpisodeDownloadLink($showDetails['name'], $seasonNumber, $episodeNumber, true);
            }

            return view('front-office.informations', [
                'show' => $showDetails,
                'download_link' => $downloadLink,
                'isInList' => $isInList,
                'error' => null
            ]);
        } catch (\Exception $e) {
            Log::error('AnimeController Show Error', ['error' => $e->getMessage()]);
            return view('front-office.informations', [
                'show' => null,
                'download_link' => null,
                'isInList' => false,
                'error' => 'Failed to load show details. Please try again later.'
            ]);
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

        $downloadLink = $this->torrentService->fetchEpisodeDownloadLink($title, $seasonNumber, $episodeNumber, true);

        return response()->json([
            'success' => $downloadLink ? true : false,
            'download_link' => $downloadLink,
        ]);
    }
}