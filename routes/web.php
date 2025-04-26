<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AnimeController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\BrowseController;
use App\Http\Controllers\MyListController;
use App\Http\Controllers\TvShowController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\MovieListController;
use App\Http\Controllers\AdminMovieController;
use App\Http\Controllers\AdminTvShowController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\AdminAnalyticsController;
use Laravel\Socialite\Facades\Socialite;

Route::get('/', function () {
    return view('welcome');
})->name('home');

/* ----- Authentication ----- */
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('reset-password', [AuthController::class, 'updatePassword'])->name('password.update');

/* ----- Public Routes ----- */
Route::get('/tv-shows', [TvShowController::class, 'index'])->name('tv.shows');
Route::get('/browse', [BrowseController::class, 'browse'])->name('browse');
Route::get('/movies', [MovieController::class, 'movies'])->name('movies.index');
Route::get('/movie/{id}', [MovieController::class, 'showMovieDetails'])->name('movies.show');
Route::get('/movies/local/{id}', [MovieController::class, 'localMovieDetails'])->name('movies.local');
Route::get('/fetch-download-link', [MovieController::class, 'fetchDownloadLink']);
Route::get('/anime', [AnimeController::class, 'filteredAnime'])->name('anime');
Route::get('/shows', [TvShowController::class, 'shows'])->name('tv-shows.shows');
Route::get('/shows/{id}', [TvShowController::class, 'showTvShowDetails'])->name('tv-shows.details');
Route::get('/shows/local/{id}', [TvShowController::class, 'localShowDetails'])->name('tv-shows.local');
Route::get('/api/episode-download', [TvShowController::class, 'apiEpisodeDownload']);
Route::get('/anime/{id}', [AnimeController::class, 'show'])->name('anime.show');
Route::get('/home', [TvShowController::class, 'index'])->name('home');

/* ----- Admin Routes ----- */
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    // Analytics Dashboard
    Route::get('/', [AdminAnalyticsController::class, 'index'])->name('index');
    Route::get('/analytics', [AdminAnalyticsController::class, 'index'])->name('analytics');
    Route::get('/analytics/update', [AdminAnalyticsController::class, 'updateAnalytics'])->name('analytics.update');
    Route::get('/analytics/export', [AdminAnalyticsController::class, 'exportReport'])->name('analytics.export'); 

    // Series Routes (AdminTvShowController)
    Route::get('/series', [AdminTvShowController::class, 'index'])->name('series');
    Route::post('/series', [AdminTvShowController::class, 'store'])->name('series.store');
    Route::get('/series/{id}/edit', [AdminTvShowController::class, 'edit'])->name('series.edit');
    Route::put('/series/{id}', [AdminTvShowController::class, 'update'])->name('series.update');
    Route::delete('/series/{id}', [AdminTvShowController::class, 'destroy'])->name('series.destroy');
    Route::post('/series/toggle-ban/{id}', [AdminTvShowController::class, 'toggleBan'])->name('series.toggle-ban');
    Route::post('/series/toggle-ban-api/{id}', [AdminTvShowController::class, 'toggleBan'])->name('series.toggle-ban-api');

    // Movies Routes (AdminMovieController)
    Route::get('/movies', [AdminMovieController::class, 'index'])->name('movies.index');
    Route::post('/movies', [AdminMovieController::class, 'store'])->name('movies.store');
    Route::get('/movies/{id}/edit', [AdminMovieController::class, 'edit'])->name('movies.edit');
    Route::put('/movies/{id}', [AdminMovieController::class, 'update'])->name('movies.update');
    Route::post('/movies/toggle-ban/{id}', [AdminMovieController::class, 'toggleBan'])->name('movies.toggle-ban');
    Route::delete('/movies/{id}', [AdminMovieController::class, 'destroy'])->name('movies.destroy');

    // Users Routes (AdminUserController)
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/details', [AdminUserController::class, 'show'])->name('users.show');
    Route::patch('/users/{id}/toggle-ban', [AdminUserController::class, 'toggleBan'])->name('users.toggle-ban');
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])->name('users.destroy');
});

/* ----- User Routes (Auth Required) ----- */
Route::post('/movies/{movie}/toggle-list', [MovieListController::class, 'toggle'])->middleware('auth');
Route::post('/tv-shows/{tvShow}/toggle-list', [MovieListController::class, 'toggleTvShow'])->middleware('auth');
Route::get('/mylist', [MyListController::class, 'index'])->middleware('auth');
Route::delete('/removefrom-list/{tmdb_id}', [MyListController::class, 'destroy'])->middleware('auth');
Route::get('/community', [CommunityController::class, 'redirectToChat'])->middleware('auth');

/* ----- Comments Routes ----- */
Route::get('/tv-shows/{tvShowId}/comments', [CommentController::class, 'indexTvShowComments']);
Route::post('/tv-shows/{tvShowId}/comments', [CommentController::class, 'storeTvShowComment']);
Route::get('/movies/{movieId}/comments', [CommentController::class, 'indexMovieComments']);
Route::post('/movies/{movieId}/comments', [CommentController::class, 'storeMovieComment']);
Route::get('/movies/{id}/tmdb-reviews', [CommentController::class, 'fetchTmdbMovieReviews']);
Route::get('/tv-shows/{id}/tmdb-reviews', [CommentController::class, 'fetchTmdbTvShowReviews']);
Route::delete('/movie-comments/{id}', [CommentController::class, 'destroyMovieComment']);
Route::delete('/tv-show-comments/{id}', [CommentController::class, 'destroyTvShowComment']);
Route::delete('/comments/{comment}', [CommentController::class, 'destroyComment']);

/* ----- Subscription Routes ----- */
Route::get('/subscription', [SubscriptionController::class, 'showSubscriptionPage'])->name('subscription');
Route::post('/subscription/process', [SubscriptionController::class, 'processSubscription'])->name('subscription.process');
Route::get('/subscription/success', [SubscriptionController::class, 'success'])->name('subscription.success');
Route::get('/subscription/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');
Route::get('/subscription/manage', [SubscriptionController::class, 'manage'])->name('subscription.manage');
Route::post('/subscription/cancel-subscription', [SubscriptionController::class, 'cancelSubscription'])->name('subscription.cancel-subscription');
Route::post('/subscription/create', [SubscriptionController::class, 'createSubscription'])->name('subscription.create-api');




Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');