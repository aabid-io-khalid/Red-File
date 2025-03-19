<?php

namespace App\Http\Controllers;

use App\Models\TvShow;
use App\Models\BannedTvShow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;

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
        $currentPage = $request->query('page', 1);
        $perPage = 10; 
    
        $response = Http::get("{$this->baseUrl}/tv/popular", [
            'api_key'  => $this->apiKey,
            'language' => 'en-US',
            'page'     => $currentPage,
        ]);
    
        $data = json_decode($response->body());
        $totalPages = $data->total_pages ?? 1;
        $allResults = $data->results ?? [];
    
        $seriesCollection = collect($allResults);
    
        $currentPageItems = $seriesCollection->slice(($currentPage - 1) * $perPage, $perPage)->all();
    
        $series = new LengthAwarePaginator(
            $currentPageItems,
            $seriesCollection->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );
    
        $showGenres = ['Action', 'Comedy', 'Drama', 'Horror', 'Sci-Fi', 'Thriller'];
    
        return view('admin.series', compact('series', 'showGenres'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'rating'      => 'required|numeric|min:0|max:10',
        ]);

        // TvShow::create($request->all());

        return redirect()->route('admin.series.index')->with('success', 'TV Show added successfully.');
    }

    public function create()
    {
        $showGenres = ['Action', 'Comedy', 'Drama', 'Horror', 'Sci-Fi', 'Thriller'];
        return view('admin.series', compact('showGenres'));
    }

    public function toggleBan($id)
    {
        $tvShow = TvShow::findOrFail($id);

        if ($tvShow->is_banned) {
            BannedTvShow::where('tv_show_id', $id)->delete();
            $tvShow->is_banned = false;
        } else {
            BannedTvShow::create(['tv_show_id' => $id, 'reason' => 'Violation of guidelines']);
            $tvShow->is_banned = true;
        }

        $tvShow->save();

        return redirect()->back()->with('success', 'TV Show ban status updated.');
    }
}
