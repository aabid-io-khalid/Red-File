<?php

namespace App\Http\Controllers;

use App\Services\TmdbService;
use App\Services\GenreMappingService;
use App\Services\BrowseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BrowseController extends Controller
{
    private $tmdbService;
    private $genreMappingService;
    private $browseService;

    public function __construct(
        TmdbService $tmdbService,
        GenreMappingService $genreMappingService,
        BrowseService $browseService
    ) {
        $this->tmdbService = $tmdbService;
        $this->genreMappingService = $genreMappingService;
        $this->browseService = $browseService;
    }

    public function browse(Request $request)
    {
        $type = $request->query('type', '');
        $search = $request->query('search', '');
        $genres = $request->query('genres', []);
        $genres = is_array($genres) ? array_filter($genres) : ($genres ? explode(',', $genres) : []);
        $year = $request->query('year', '');
        $rating = $request->query('rating', '');
        $sort = $request->query('sort', 'popularity');
        $order = $request->query('order', 'desc');
        $page = max(1, (int)$request->query('page', 1));
        $perPage = 30;

        $genresList = $this->genreMappingService->getGenres($type === 'tv' ? 'tv' : 'movie');
        $genreMap = $this->genreMappingService->getGenreMap($type === 'tv' ? 'tv' : 'movie');
        $genreCategoryMap = $this->genreMappingService->getGenreCategoryMap($type === 'tv' ? 'tv' : 'movie');

        $filters = [
            'search' => is_array($search) ? '' : $search,
            'genres' => $genres,
            'year' => is_array($year) ? '' : $year,
            'rating' => is_array($rating) ? '' : $rating,
            'sort' => $sort,
            'order' => $order,
            'filter' => $request->query('filter', '')
        ];

        $localContent = $this->browseService->getLocalContent($type, $filters, $genreCategoryMap, $sort, $order);
        $bannedIds = $this->browseService->getBannedIds();

        $tmdbMovies = ['media' => [], 'total_pages' => 1];
        $tmdbShows = ['media' => [], 'total_pages' => 1];
        $tmdbNeeded = $this->calculateTmdbNeeded(
            $localContent['movies_count'] + $localContent['shows_count'],
            $page,
            $perPage
        );

        if ($tmdbNeeded > 0 && (!$type || $type === 'movie')) {
            $tmdbMovies = $this->tmdbService->getMedia('movie', $filters, $page);
        }
        if ($tmdbNeeded > 0 && (!$type || $type === 'tv')) {
            $tmdbShows = $this->tmdbService->getMedia('tv', $filters, $page);
        }

        $combinedContent = $this->browseService->combineContent(
            $type,
            $localContent['movies'],
            $localContent['shows'],
            $tmdbMovies['media'],
            $tmdbShows['media'],
            $bannedIds,
            $page,
            $perPage
        );

        Log::info('Browse fetched', [
            'total_pages' => $combinedContent['total_pages'],
            'filters' => $filters
        ]);

        return view('Front-office.browse', [
            'content' => $combinedContent['content'],
            'genres' => $genresList,
            'genreMap' => $genreMap,
            'selectedGenres' => $genres,
            'search' => $filters['search'],
            'year' => $filters['year'],
            'rating' => $filters['rating'],
            'sort' => $sort,
            'order' => $order,
            'page' => $page,
            'totalPages' => $combinedContent['total_pages'],
            'type' => $type
        ]);
    }

    private function calculateTmdbNeeded(int $totalLocalCount, int $page, int $perPage): int
    {
        $offset = ($page - 1) * $perPage;
        $localRemaining = max(0, $totalLocalCount - $offset);
        return max(0, $perPage - min($localRemaining, $perPage));
    }
}