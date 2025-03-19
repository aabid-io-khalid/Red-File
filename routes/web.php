<?php

use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AnimeController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\TvShowController;
use App\Http\Controllers\AdminTvShowController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});


Route::get('/admin/', function () {
    return view('admin.index'); 
})->name('admin.index');

Route::get('/admin/analytics', function () {
    return view('admin.static-analytics'); 
})->name('admin.analytics');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/series', [AdminTvShowController::class, 'index'])->name('series.index');
    Route::post('/series', [AdminTvShowController::class, 'store'])->name('series.store');
    Route::get('/series/create', [AdminTvShowController::class, 'create'])->name('admin.series.create');


    Route::get('/series/{id}', [AdminTvShowController::class, 'show'])->name('series.view');
    Route::get('/series/{id}/edit', [AdminTvShowController::class, 'edit'])->name('series.edit');
    Route::delete('/series/{id}', [AdminTvShowController::class, 'destroy'])->name('series.delete');
});

Route::patch('/admin/series/{id}/toggle-ban', [AdminTvShowController::class, 'toggleBan'])
    ->name('admin.series.toggle-ban');


Route::get('/admin/user', function () {
    return view('admin.user'); 
})->name('admin.user');


Route::get('/movies', function () {
    return view('front-office.movie-dashboard');
});

// Route::get('/admin/notification', function () {
//     return view('Back-office.notification');
// });
// Route::get('/admin/form', function () {
//     return view('Back-office.form-elements');
// });
// Route::get('/admin/tables', function () {
//     return view('Back-office.tables');
// });

Route::get('/tv-shows', [TvShowController::class, 'index'])->name('tv.shows');


// Route::get('/shows', [TvShowController::class, 'shows'])->name('tv-shows.shows');

Route::get('/browse', [MovieController::class, 'filteredMovies']);

Route::get('/movies', [MovieController::class, 'filteredMovies'])->name('movies.index');

Route::get('/movie/{id}', [MovieController::class, 'showMovieDetails'])->name('movies.show');

Route::get('/fetch-download-link', [MovieController::class, 'fetchDownloadLink']);

Route::get('/anime', [AnimeController::class, 'filteredAnime'])->name('anime');

Route::get('/shows', [TvShowController::class, 'shows'])->name('tv-shows.shows');

Route::get('/shows/{id}', [TvShowController::class, 'showTvShowDetails'])->name('tv-shows.details');


Route::get('/anime/{id}', [AnimeController::class, 'show'])->name('anime.show');


Route::get('/', [TvShowController::class, 'index']);
Route::get('/home', [TvShowController::class, 'index']);


/* ----- Authentication ----- */

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


require __DIR__.'/auth.php';
