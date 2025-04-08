<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\TvShow;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;

class CommentController extends Controller
{
    private $apiKey;
    private $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('TMDB_API_KEY'); 
        $this->baseUrl = 'https://api.themoviedb.org/3';
    }

    public function indexTvShowComments($tvShowId)
    {
        try {
            \Log::info("Fetching reviews for TV Show ID: $tvShowId");
    
            $comments = Comment::where('commentable_id', $tvShowId)
                ->where('commentable_type', 'App\Models\TvShow')
                ->with('user') 
                ->latest()
                ->get();
    
            \Log::info("Fetched " . count($comments) . " comments.");
    
            return response()->json($comments, 200);
        } catch (\Exception $e) {
            \Log::error("Error fetching TV show comments: " . $e->getMessage());
            return response()->json(['error' => 'Failed to load TV show reviews.'], 500);
        }
    }
    

    public function storeTvShowComment(Request $request, $id) {

        if (Gate::denies('post-comment')) {
            return response()->json(['error' => 'You must be logged in to comment.'], 403);
        }
        
        try {
            $comment = new Comment();
            $comment->user_id = auth()->id();
            $comment->content = $request->input('content');
            $comment->rating = $request->rating;
            
            $comment->commentable_id = $id;  
            $comment->commentable_type = 'App\Models\TvShow';

            $comment->save();
            
            return response()->json(['message' => 'TV Show comment added successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroyTvShowComment($id)
    {
        return $this->destroyComment($id);
    }

    public function getTvShowReviews($id)
    {
        $localReviews = Comment::where('commentable_type', TvShow::class)
                               ->where('commentable_id', $id)
                               ->with('user')
                               ->get();

        return response()->json($localReviews);
    }

    public function fetchTmdbTvShowReviews($id)
    {
        return $this->fetchTmdbReviews($id, 'tv');
    }

    public function indexMovieComments($movieId)
    {
        try {
            \Log::info("Fetching reviews for Movie ID: $movieId");
    
            $comments = Comment::where('commentable_id', $movieId)
                ->where('commentable_type', 'App\Models\Movie')
                ->with('user') 
                ->latest()
                ->get();
    
            \Log::info("Fetched " . count($comments) . " comments.");
    
            return response()->json($comments, 200);
        } catch (\Exception $e) {
            \Log::error("Error fetching movie comments: " . $e->getMessage());
            return response()->json(['error' => 'Failed to load movie reviews.'], 500);
        }
    }
    

    public function storeMovieComment(Request $request, $id) {

        if (Gate::denies('post-comment')) {
            return response()->json(['error' => 'You must be logged in to comment.'], 403);
        }

        
        try {
            $comment = new Comment();
            $comment->user_id = auth()->id();
            $comment->content = $request->input('content');
            $comment->rating = $request->rating;
            
            $comment->commentable_id = $id;  
            $comment->commentable_type = 'App\Models\Movie';

            $comment->save();
            
            return response()->json(['message' => 'Movie comment added successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroyMovieComment($id)
    {
        return $this->destroyComment($id);
    }

    public function getMovieReviews($id)
    {
        $localReviews = Comment::where('commentable_type', Movie::class)
                               ->where('commentable_id', $id)
                               ->with('user')
                               ->get();

        return response()->json($localReviews);
    }

    public function fetchTmdbMovieReviews($id)
    {
        return $this->fetchTmdbReviews($id, 'movie');
    }

    public function destroyComment($id)
{
    try {
        $comment = Comment::findOrFail($id);
        
        if (auth()->user()->id !== $comment->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $comment->delete();
        return response()->json(['message' => 'Comment deleted successfully']);
    } catch (\Exception $e) {
        \Log::error('Comment deletion error: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to delete comment'], 500);
    }
}

    private function fetchTmdbReviews($id, $type)
    {
        try {
            $url = "{$this->baseUrl}/{$type}/{$id}/reviews?api_key={$this->apiKey}";
            $response = Http::get($url);

            if ($response->successful()) {
                $reviewsData = $response->json()['results'] ?? [];

                $transformedReviews = array_map(function($review) {
                    return [
                        'id' => $review['id'] ?? null,
                        'author' => $review['author'] ?? 'Anonymous',
                        'content' => $review['content'] ?? '',
                        'text' => $review['content'] ?? '',
                        'created_at' => $review['created_at'] ?? now(),
                        'updated_at' => $review['updated_at'] ?? now(),
                        'author_details' => [
                            'avatar_path' => $review['author_details']['avatar_path'] ?? null,
                            'rating' => $review['author_details']['rating'] ?? null
                        ],
                        'vote_average' => $review['author_details']['rating'] ?? 0
                    ];
                }, $reviewsData);

                return response()->json($transformedReviews);
            }

            Log::warning("No TMDB reviews found for {$type} ID: {$id}");
            return response()->json([], 200);
        } catch (\Exception $e) {
            Log::error("TMDB {$type} Reviews Fetch Error: " . $e->getMessage());
            return response()->json([], 200); 
        }
    }
}