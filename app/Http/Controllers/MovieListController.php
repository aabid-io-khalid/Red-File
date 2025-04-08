<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserMovieList;
use App\Models\UserTvShowList;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class MovieListController extends Controller
{
    public function toggle($id)
    {

        
        try {
    
            $user = auth()->user()->load('roles');
        \Log::info('User roles in toggleTvShow:', $user->roles->pluck('name')->toArray());
        \Log::info("User {$user->id} hasRole('premium') in toggleTvShow: " . json_encode($user->hasRole('premium')));
        \Log::info("Gate::forUser(\$user)->allows('add-to-my-list') in toggleTvShow: " . json_encode(Gate::forUser($user)->allows('add-to-my-list')));
        \Log::info("User {$user->id} is trying to toggle TV show ID {$id}");

        if (!Gate::allows('add-to-my-list', $user)) {
            return response()->json(['error' => 'Unauthorized: Premium membership required.'], 403);
        }
    
    
            $movieEntry = UserMovieList::where('user_id', $user->id)
                ->where('tmdb_id', $id)
                ->first();
    
            if ($movieEntry) {
                $movieEntry->delete();
                return response()->json(['message' => "Movie ID {$id} removed from your list."]);
            } else {
                UserMovieList::create([
                    'user_id' => $user->id,
                    'tmdb_id' => $id
                ]);
                return response()->json(['message' => "Movie ID {$id} added to your list."]);
            }
        } catch (\Exception $e) {
            \Log::error("Error in toggle method: " . $e->getMessage());
            return response()->json(['error' => 'Something went wrong.'], 500);
        }
    }


    public function toggleTvShow($id)
    {
        try {
            $user = auth()->user();

            if (!Gate::allows('add-to-my-list', $user)) {
                return response()->json(['error' => 'Unauthorized: Premium membership required.'], 403);
            }
            
            \Log::info("User {$user->id} is trying to toggle TV show ID {$id}");
    
            $tvShowEntry = UserTvShowList::where('user_id', $user->id)
                ->where('tmdb_id', $id)
                ->first();
    
            if ($tvShowEntry) {
                $tvShowEntry->delete();
                return response()->json([
                    'inList' => false, 
                    'message' => "TV Show ID {$id} removed from your list."
                ]);
            } else {
                UserTvShowList::create([
                    'user_id' => $user->id,
                    'tmdb_id' => $id
                ]);
                return response()->json([
                    'inList' => true, 
                    'message' => "TV Show ID {$id} added to your list."
                ]);
            }
        } catch (\Exception $e) {
            \Log::error("Error in toggleTvShow method: " . $e->getMessage());
            return response()->json(['error' => 'Something went wrong.'], 500);
        }
    }

}
