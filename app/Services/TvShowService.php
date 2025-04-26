<?php

namespace App\Services;

use App\Models\TvShow;
use App\Models\Category;
use App\Models\BannedTvShow;
use Illuminate\Support\Facades\Log;
use Exception;

class TvShowService
{
    public function getLocalSeries()
    {
        try {
            Log::info('Fetching local series');
            $series = TvShow::with('categories')->orderBy('created_at', 'desc')->get();
            Log::info('Successfully fetched local series', ['count' => $series->count()]);
            return $series;
        } catch (Exception $e) {
            Log::error('Database error fetching local series', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function getBannedApiSeriesIds(): array
    {
        try {
            Log::info('Fetching banned API series IDs');
            $ids = BannedTvShow::where('is_tmdb', true)
                ->pluck('tmdb_id')
                ->toArray();
            Log::info('Successfully fetched banned API series IDs', ['count' => count($ids)]);
            return $ids;
        } catch (Exception $e) {
            Log::error('Database error fetching banned API series IDs', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function createTvShow(array $data, ?string $posterUrl): TvShow
    {
        try {
            $tvShow = TvShow::create([
                'title' => $data['title'],
                'description' => $data['description'] ?? '',
                'year' => $data['year'] ?? null,
                'rating' => $data['rating'] ?? null,
                'poster' => $posterUrl,
                'is_banned' => false,
                'seasons' => $data['seasons'] ?? null,
                'episodes_per_season' => $data['episodes_per_season'] ?? null,
            ]);

            $categoryIds = $this->syncCategories($data['categories']);
            $tvShow->categories()->sync($categoryIds);

            Log::info('TV Show created successfully', ['id' => $tvShow->id, 'title' => $tvShow->title]);
            return $tvShow;
        } catch (Exception $e) {
            Log::error('TV Show creation error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    public function updateTvShow(TvShow $tvShow, array $data, ?string $posterUrl): TvShow
    {
        try {
            $tvShow->update([
                'title' => $data['title'],
                'description' => $data['description'] ?? '',
                'year' => $data['year'] ?? null,
                'rating' => $data['rating'] ?? null,
                'poster' => $posterUrl ?? $tvShow->poster,
                'seasons' => $data['seasons'] ?? $tvShow->seasons,
                'episodes_per_season' => $data['episodes_per_season'] ?? $tvShow->episodes_per_season,
            ]);

            $categoryIds = $this->syncCategories($data['categories']);
            $tvShow->categories()->sync($categoryIds);

            Log::info('TV Show updated successfully', ['id' => $tvShow->id, 'title' => $tvShow->title]);
            return $tvShow;
        } catch (Exception $e) {
            Log::error('TV Show update error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    public function getTvShowForEdit(int $id): TvShow
    {
        try {
            $tvShow = TvShow::with('categories')->findOrFail($id);
            Log::info('Fetched TV Show for edit', ['id' => $id, 'title' => $tvShow->title]);
            return $tvShow;
        } catch (Exception $e) {
            Log::error('Database error fetching TV Show for edit', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'id' => $id
            ]);
            throw $e;
        }
    }

    public function deleteTvShow(int $id): void
    {
        try {
            $tvShow = TvShow::findOrFail($id);
            $tvShow->delete();
            Log::info('TV Show deleted successfully', ['id' => $id]);
        } catch (Exception $e) {
            Log::error('Database error deleting TV Show', [
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
            Log::info('Toggling ban for TV Show', [
                'id' => $id,
                'is_tmdb' => $isTmdb,
                'is_banned' => $isBanned,
                'reason' => $reason
            ]);

            if ($isTmdb) {
                $existingBan = BannedTvShow::where('tmdb_id', $id)->first();
                if ($existingBan && !$isBanned) {
                    $existingBan->delete();
                    Log::info('Removed TMDB TV Show ban', ['tmdb_id' => $id]);
                    return 'TMDB TV Show unbanned successfully.';
                } elseif (!$existingBan && $isBanned) {
                    BannedTvShow::create([
                        'tmdb_id' => $id,
                        'is_tmdb' => true,
                        'reason' => $reason
                    ]);
                    Log::info('Banned TMDB TV Show', ['tmdb_id' => $id]);
                    return 'TMDB TV Show banned successfully.';
                }
                return 'No change in TMDB TV Show ban status.';
            }

            $existingBan = BannedTvShow::where('tv_show_id', $id)->first();
            if ($existingBan && !$isBanned) {
                $existingBan->delete();
                Log::info('Removed local TV Show ban', ['tv_show_id' => $id]);
                return 'Local TV Show unbanned successfully.';
            } elseif (!$existingBan && $isBanned) {
                BannedTvShow::create([
                    'tv_show_id' => $id,
                    'is_tmdb' => false,
                    'reason' => $reason
                ]);
                Log::info('Banned local TV Show', ['tv_show_id' => $id]);
                return 'Local TV Show banned successfully.';
            }
            return 'No change in local TV Show ban status.';
        } catch (Exception $e) {
            Log::error('Error toggling ban for TV Show', [
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