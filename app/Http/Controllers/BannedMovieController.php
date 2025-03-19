<?php

namespace App\Http\Controllers;

use App\Models\BannedMovie;
use Illuminate\Http\Request;

class BannedMovieController extends Controller
{
    public function index()
    {
        return response()->json(BannedMovie::all());
    }

    public function store(Request $request)
    {
        $request->validate(['tmdb_id' => 'required|string|unique:banned_movies']);
        $bannedMovie = BannedMovie::create($request->all());
        return response()->json($bannedMovie, 201);
    }

    public function show(BannedMovie $bannedMovie)
    {
        return response()->json($bannedMovie);
    }

    public function destroy(BannedMovie $bannedMovie)
    {
        $bannedMovie->delete();
        return response()->json(null, 204);
    }
}
