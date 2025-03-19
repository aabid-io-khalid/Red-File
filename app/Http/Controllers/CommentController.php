<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Movie;
use App\Models\TvShow;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index($type, $id)
    {
        $model = $this->getModel($type, $id);
        return response()->json($model->comments()->with('user')->get());
    }

    public function store(Request $request, $type, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'content' => 'required|string|max:1000',
        ]);

        $model = $this->getModel($type, $id);

        $comment = $model->comments()->create([
            'user_id' => $request->user_id,
            'content' => $request->content,
        ]);

        return response()->json($comment, 201);
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();
        return response()->json(null, 204);
    }

    private function getModel($type, $id)
    {
        $models = [
            'movies' => Movie::class,
            'tv-shows' => TvShow::class,
        ];

        if (!isset($models[$type])) {
            abort(404, "Invalid commentable type");
        }

        return $models[$type]::findOrFail($id);
    }
}
