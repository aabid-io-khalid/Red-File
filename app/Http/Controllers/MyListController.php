<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class MyListController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $apiKey = env('TMDB_API_KEY');
        
        $movieIds = DB::table('user_movie_list')
            ->where('user_id', $userId)
            ->pluck('tmdb_id');
            
        $tvShowIds = DB::table('user_tv_show_list')
            ->where('user_id', $userId)
            ->pluck('tmdb_id');
            
        $movies = [];
        foreach ($movieIds as $id) {
            $response = Http::get("https://api.themoviedb.org/3/movie/{$id}?api_key={$apiKey}");
            if ($response->successful()) {
                $movies[] = $response->json();
            }
        }
        
        $tvShows = [];
        foreach ($tvShowIds as $id) {
            $response = Http::get("https://api.themoviedb.org/3/tv/{$id}?api_key={$apiKey}");
            if ($response->successful()) {
                $tvShows[] = $response->json();
            }
        }
        
        return view('Front-office.mylist', compact('movies', 'tvShows'));
    }
}
