<?php

namespace App\Http\Controllers;

use App\Models\BannedTvShow;
use Illuminate\Http\Request;

class BannedTvShowController extends Controller
{
    public function index()
    {
        return response()->json(BannedTvShow::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'tv_show_id' => 'required|exists:tv_shows,id',
            'reason' => 'nullable|string',
        ]);

        $bannedTvShow = BannedTvShow::create($request->all());
        return response()->json($bannedTvShow, 201);
    }

    public function show(BannedTvShow $bannedTvShow)
    {
        return response()->json($bannedTvShow->load('tvShow'));
    }

    public function destroy(BannedTvShow $bannedTvShow)
    {
        $bannedTvShow->delete();
        return response()->json(null, 204);
    }
}
