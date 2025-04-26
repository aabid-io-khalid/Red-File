<?php

namespace App\Services;

use App\Models\Movie;
use App\Models\Category;
use App\Models\BannedMovie;
use Illuminate\Support\Facades\Log;
use Exception;

class MovieService
{
    public function getLocalMovies()
    {
        try {
            Log::info('Fetching local movies');
            $movies = Movie::with('categories')->orderBy('created_at', 'desc')->get();
            Log::info('Successfully fetched local movies', ['count' => $movies->count()]);
            return $movies;
        } catch (Exception $e) {
            Log::error('Database error fetching local movies', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function getBannedApiMovieIds(): array
    {
        try {
            Log::info('Fetching banned API movie IDs');
            $ids = BannedMovie::where('is_tmdb', true)
                ->pluck('tmdb_id')
                ->toArray();
            Log::info('Successfully fetched banned API movie IDs', ['count' => count($ids)]);
            return $ids;
        } catch (Exception $e) {
            Log::error('Database error fetching banned API movie IDs', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function createMovie(array $data, ?string $posterUrl): Movie
    {
        try {
            $movie = Movie::create([
                'title' => $data['title'],
                'description' => $data['description'] ?? '',
                'year' => $data['year'] ?? null,
                'rating' => $data['rating'] ?? null,
                'poster' => $posterUrl,
                'is_banned' => false,
            ]);

            $categoryIds = $this->syncCategories($data['categories']);
            $movie->categories()->sync($categoryIds);

            Log::info('Movie created successfully', ['id' => $movie->id, 'title' => $movie->title]);
            return $movie;
        } catch (Exception $e) {
            Log::error('Movie creation error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    public function updateMovie(Movie $movie, array $data, ?string $posterUrl): Movie
    {
        try {
            $movie->update([
                'title' => $data['title'],
                'description' => $data['description'] ?? '',
                'year' => $data['year'] ?? null,
                'rating' => $data['rating'] ?? null,
                'poster' => $posterUrl ?? $movie->poster,
            ]);

            $categoryIds = $this->syncCategories($data['categories']);
            $movie->categories()->sync($categoryIds);

            Log::info('Movie updated successfully', ['id' => $movie->id, 'title' => $movie->title]);
            return $movie;
        } catch (Exception $e) {
            Log::error('Movie update error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    public function getMovieForEdit(int $id): Movie
    {
        try {
            $movie = Movie::with('categories')->findOrFail($id);
            Log::info('Fetched movie for edit', ['id' => $id, 'title' => $movie->title]);
            return $movie;
        } catch (Exception $e) {
            Log::error('Database error fetching movie for edit', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'id' => $id
            ]);
            throw $e;
        }
    }

    public function deleteMovie(int $id): void
    {
        try {
            $movie = Movie::findOrFail($id);
            $movie->delete();
            Log::info('Movie deleted successfully', ['id' => $id]);
        } catch (Exception $e) {
            Log::error('Database error deleting movie', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'id' => $id
            ]);
            throw $e;
        }
    }

    public function toggleBan(int $id, bool $isTmdb, bool $isBanned, ?string $reason = 'Violation of guidelines'): string
    {
        try {
            Log::info('Toggling ban for movie', [
                'id' => $id,
                'is_tmdb' => $isTmdb,
                'is_banned' => $isBanned,
                'reason' => $reason
            ]);

            if ($isTmdb) {
                $existingBan = BannedMovie::where('tmdb_id', $id)->first();
                if ($existingBan && !$isBanned) {
                    $existingBan->delete();
                    Log::info('Removed TMDB movie ban', ['tmdb_id' => $id]);
                    return 'TMDB movie unbanned successfully.';
                } elseif (!$existingBan && $isBanned) {
                    BannedMovie::create([
                        'tmdb_id' => $id,
                        'is_tmdb' => true,
                        'reason' => $reason
                    ]);
                    Log::info('Banned TMDB movie', ['tmdb_id' => $id]);
                    return 'TMDB movie banned successfully.';
                }
                return 'No change in TMDB movie ban status.';
            }

            $existingBan = BannedMovie::where('movie_id', $id)->first();
            if ($existingBan && !$isBanned) {
                $existingBan->delete();
                Log::info('Removed local movie ban', ['movie_id' => $id]);
                return 'Local movie unbanned successfully.';
            } elseif (!$existingBan && $isBanned) {
                BannedMovie::create([
                    'movie_id' => $id,
                    'is_tmdb' => false,
                    'reason' => $reason
                ]);
                Log::info('Banned local movie', ['movie_id' => $id]);
                return 'Local movie banned successfully.';
            }
            return 'No change in local movie ban status.';
        } catch (Exception $e) {
            Log::error('Error toggling ban for movie', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'id' => $id
            ]);
            throw $e;
        }
    }

    private function syncCategories(string $categories): array
    {
        $categoryNames = array_filter(
            array_map('trim', explode(',', $categories)),
            fn($name) => !empty($name)
        );

        if (empty($categoryNames)) {
            throw new Exception('At least one category is required');
        }

        $categoryIds = [];
        foreach ($categoryNames as $name) {
            $category = Category::firstOrCreate(['name' => $name]);
            $categoryIds[] = $category->id;
        }

        return $categoryIds;
    }
}