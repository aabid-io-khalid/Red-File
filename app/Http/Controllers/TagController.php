<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {
        return response()->json(Tag::all());
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:tags']);
        $tag = Tag::create($request->all());
        return response()->json($tag, 201);
    }

    public function show(Tag $tag)
    {
        return response()->json($tag->load('movies'));
    }

    public function update(Request $request, Tag $tag)
    {
        $tag->update($request->all());
        return response()->json($tag);
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();
        return response()->json(null, 204);
    }
}
