<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CommunityController extends Controller
{
    public function redirectToChat(Request $request)
    {

        if (Gate::denies('access-community-chat')) {
            return redirect()->route('home')->with('error', 'You must be a premium user to access the community chat.');
        }

        
        $user = Auth::user();

        // Generate a simple token (Base64 for now; Sanctum/JWT recommended later)
        $token = base64_encode(json_encode([
            'id' => $user->id,
            'name' => $user->name, // Adjust to fullName if that's your column
            'email' => $user->email,
            'timestamp' => now()->timestamp, // For expiration
        ]));

        // Redirect to React chat app
        $chatUrl = "http://localhost:5173/chat?token=" . urlencode($token);
        return redirect($chatUrl);
    }
}