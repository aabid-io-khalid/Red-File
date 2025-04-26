<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('authentication.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $user = User::where('email', $request->email)->first();
        if ($user && $user->is_banned) {
            Log::info('Login attempt by banned user', ['email' => $request->email]);
            return back()->withErrors(['email' => 'Your account has been banned.']);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $request->session()->regenerate();
            $isAdmin = Auth::user()->hasRole('admin'); 
            $redirectTo = $isAdmin ? 'admin.index' : 'home';
            Log::info('User logged in', [
                'user_id' => Auth::id(),
                'is_admin' => $isAdmin,
                'redirect_to' => $redirectTo,
            ]);
            return redirect()->intended(route($redirectTo));
        }

        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    public function showRegisterForm()
    {
        return view('authentication.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);
        $isAdmin = $user->hasRole('admin');
        $redirectTo = $isAdmin ? 'admin.index' : 'home';
        Log::info('User registered', [
            'user_id' => $user->id,
            'is_admin' => $isAdmin,
            'redirect_to' => $redirectTo,
        ]);
        return redirect()->intended(route($redirectTo));
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }

    public function showForgotPasswordForm()
    {
        return view('authentication.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $token = Str::random(60);
        
        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        Mail::send('emails.password-reset', ['token' => $token], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Password Reset Request');
        });

        return back()->with('message', 'Password reset link sent!');
    }

    public function showResetForm($token)
    {
        return view('authentication.reset-password', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6|confirmed',
            'token' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return back()->withErrors(['email' => 'No user found.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('login')->with('message', 'Password updated successfully.');
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $existingUser = User::where('email', $googleUser->email)->first();
            if ($existingUser && $existingUser->is_banned) {
                Log::info('Google login attempt by banned user', ['email' => $googleUser->email]);
                return redirect()->route('login')->withErrors(['email' => 'Your account has been banned.']);
            }

            $user = User::updateOrCreate(
                ['email' => $googleUser->email],
                [
                    'name' => $googleUser->name,
                    'google_id' => $googleUser->id,
                    'password' => Hash::make(Str::random(16)),
                ]
            );

            Auth::login($user, true);
            $isAdmin = $user->hasRole('admin'); 
            $redirectTo = $isAdmin ? 'admin.index' : 'home';
            Log::info('Google login', [
                'user_id' => $user->id,
                'is_admin' => $isAdmin,
                'redirect_to' => $redirectTo,
            ]);
            return redirect()->intended(route($redirectTo));
        } catch (\Exception $e) {
            Log::error('Google login failed', ['error' => $e->getMessage()]);
            return redirect()->route('login')->withErrors(['email' => 'Google login failed. Please try again.']);
        }
    }
}