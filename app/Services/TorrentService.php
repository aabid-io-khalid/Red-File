<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TorrentService
{
    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 15,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            ]
        ]);
    }

    public function fetchEpisodeDownloadLink(string $title, int $seasonNumber, int $episodeNumber, bool $isAnime = false): ?string
    {
        $seasonFormatted = str_pad($seasonNumber, 2, '0', STR_PAD_LEFT);
        $episodeFormatted = str_pad($episodeNumber, 2, '0', STR_PAD_LEFT);
        $searchTerm = "{$title} S{$seasonFormatted}E{$episodeFormatted}";
        $encodedSearch = urlencode($searchTerm);
        $cacheKey = "torrent_{$title}_S{$seasonFormatted}E{$episodeFormatted}_" . ($isAnime ? 'anime' : 'tv');

        Log::info('Starting torrent search', [
            'title' => $title,
            'season' => $seasonNumber,
            'episode' => $episodeNumber,
            'search_term' => $searchTerm,
            'is_anime' => $isAnime
        ]);

        return Cache::remember($cacheKey, now()->addMinutes(60), function () use ($title, $seasonNumber, $seasonFormatted, $episodeNumber, $episodeFormatted, $searchTerm, $encodedSearch, $isAnime) {
            $isAnime = $isAnime || $this->isAnime($title);

            // Anime-specific sources
            if ($isAnime) {
                // Nyaa.si
                $link = $this->searchNyaa($title, $encodedSearch, $seasonFormatted, $episodeFormatted, $episodeNumber);
                if ($link) {
                    return $link;
                }

                // AnimeTosho
                $link = $this->searchAnimeTosho($title, $encodedSearch, $seasonFormatted, $episodeFormatted, $episodeNumber);
                if ($link) {
                    return $link;
                }

                // Nyaa.si fallback (broader search)
                $link = $this->searchNyaaFallback($title, $seasonFormatted);
                if ($link) {
                    return $link;
                }
            } else {
                // Non-anime TV show sources
                // EZTV
                $link = $this->searchEztv($title, $searchTerm, $encodedSearch, $seasonFormatted, $episodeFormatted, $episodeNumber);
                if ($link) {
                    return $link;
                }

                // ThePirateBay
                $link = $this->searchThePirateBay($title, $encodedSearch, $seasonFormatted, $episodeFormatted, $episodeNumber);
                if ($link) {
                    return $link;
                }

                // 1337x
                $link = $this->search1337x($title, $encodedSearch, $seasonFormatted, $episodeFormatted, $episodeNumber);
                if ($link) {
                    return $link;
                }

                // Fallback: broader search for season
                $link = $this->searchTvShowFallback($title, $seasonFormatted, $seasonNumber);
                if ($link) {
                    return $link;
                }
            }

            Log::warning('No torrent found across all APIs', [
                'title' => $title,
                'season' => $seasonNumber,
                'episode' => $episodeNumber,
                'is_anime' => $isAnime
            ]);

            return null;
        });
    }

    private function searchEztv(string $title, string $searchTerm, string $encodedSearch, string $seasonFormatted, string $episodeFormatted, int $episodeNumber): ?string
    {
        try {
            Log::info('Trying EZTV API');
            $apiUrl = "https://eztv.re/api/get-torrents?limit=100&imdb_id=&page=1&query=" . $encodedSearch;
            $response = $this->client->request('GET', $apiUrl);
            $data = json_decode($response->getBody(), true);

            if (isset($data['torrents']) && count($data['torrents']) > 0) {
                $matchingTorrents = $this->filterTorrents($data['torrents'], $title, $seasonFormatted, $episodeFormatted, $episodeNumber);
                if (!empty($matchingTorrents)) {
                    usort($matchingTorrents, fn($a, $b) => ($b['seeds'] ?? 0) - ($a['seeds'] ?? 0));
                    $bestTorrent = reset($matchingTorrents);
                    Log::info('Found matching torrent on EZTV', ['torrent' => $bestTorrent['title']]);
                    return $bestTorrent['magnet_url'] ?? null;
                }
            }
        } catch (\Exception $e) {
            Log::error('Error with EZTV API', ['error' => $e->getMessage()]);
        }
        return null;
    }

    private function searchThePirateBay(string $title, string $encodedSearch, string $seasonFormatted, string $episodeFormatted, int $episodeNumber): ?string
    {
        try {
            Log::info('Trying ThePirateBay API');
            $apiUrl = "https://apibay.org/q.php?q={$encodedSearch}&cat=205";
            $response = $this->client->request('GET', $apiUrl);
            $data = json_decode($response->getBody(), true);

            if (is_array($data) && count($data) > 0 && isset($data[0]['id']) && $data[0]['id'] != 0) {
                $matchingTorrents = $this->filterTorrents($data, $title, $seasonFormatted, $episodeFormatted, $episodeNumber, 'name', 'seeders');
                if (!empty($matchingTorrents)) {
                    usort($matchingTorrents, fn($a, $b) => ($b['seeders'] ?? 0) - ($a['seeders'] ?? 0));
                    $bestTorrent = reset($matchingTorrents);
                    Log::info('Found matching torrent on ThePirateBay', ['torrent' => $bestTorrent['name']]);
                    $infoHash = $bestTorrent['info_hash'];
                    $name = urlencode($bestTorrent['name']);
                    return "magnet:?xt=urn:btih:{$infoHash}&dn={$name}&tr=udp%3A%2F%2Ftracker.coppersurfer.tk%3A6969%2Fannounce&tr=udp%3A%2F%2F9.rarbg.to%3A2920%2Fannounce&tr=udp%3A%2F%2Ftracker.opentrackr.org%3A1337&tr=udp%3A%2F%2Ftracker.internetwarriors.net%3A1337%2Fannounce";
                }
            }
        } catch (\Exception $e) {
            Log::error('Error with ThePirateBay API', ['error' => $e->getMessage()]);
        }
        return null;
    }

    private function search1337x(string $title, string $encodedSearch, string $seasonFormatted, string $episodeFormatted, int $episodeNumber): ?string
    {
        try {
            Log::info('Trying 1337x API');
            $apiUrl = "https://1337x.to/search/{$encodedSearch}/1/";
            $response = $this->client->request('GET', $apiUrl);
            $html = $response->getBody()->getContents();

            preg_match_all('/<a href="\/torrent\/(\d+)\/([^"]+)">\s*([^<]+)\s*<\/a>/', $html, $matches, PREG_SET_ORDER);
            $matchingTorrents = [];
            foreach ($matches as $match) {
                $torrentTitle = trim($match[3]);
                $torrentUrl = "https://1337x.to/torrent/{$match[1]}/{$match[2]}/";
                $seeders = 0; 
                if ($this->isMatchingTorrent($torrentTitle, $title, $seasonFormatted, $episodeFormatted, $episodeNumber)) {
                    $matchingTorrents[] = [
                        'title' => $torrentTitle,
                        'url' => $torrentUrl,
                        'seeders' => $seeders
                    ];
                }
            }
            if (!empty($matchingTorrents)) {
                usort($matchingTorrents, fn($a, $b) => $b['seeders'] - $a['seeders']);
                $bestTorrent = reset($matchingTorrents);
                $torrentPage = $this->client->request('GET', $bestTorrent['url']);
                $torrentHtml = $torrentPage->getBody()->getContents();
                preg_match('/href="(magnet:[^"]+)"/', $torrentHtml, $magnetMatch);
                if (!empty($magnetMatch[1])) {
                    Log::info('Found matching torrent on 1337x', ['torrent' => $bestTorrent['title']]);
                    return $magnetMatch[1];
                }
            }
        } catch (\Exception $e) {
            Log::error('Error with 1337x API', ['error' => $e->getMessage()]);
        }
        return null;
    }

    private function searchTvShowFallback(string $title, string $seasonFormatted, int $seasonNumber): ?string
    {
        try {
            Log::info('Trying TV show fallback search');
            $fallbackSearch = urlencode($title . " S{$seasonFormatted}");
            $apiUrl = "https://apibay.org/q.php?q={$fallbackSearch}&cat=205";
            $response = $this->client->request('GET', $apiUrl);
            $data = json_decode($response->getBody(), true);
            if (is_array($data) && count($data) > 0 && isset($data[0]['id']) && $data[0]['id'] != 0) {
                $matchingTorrents = [];
                foreach ($data as $torrent) {
                    $torrentTitle = $torrent['name'] ?? '';
                    $showNameVariations = [
                        $title,
                        str_replace(' ', '.', $title),
                        str_replace(' ', '-', $title),
                        str_replace('The ', '', $title),
                        str_replace('The ', '', str_replace(' ', '.', $title)),
                        str_replace('The ', '', str_replace(' ', '-', $title))
                    ];
                    $hasShowName = false;
                    foreach ($showNameVariations as $variation) {
                        if (stripos($torrentTitle, $variation) !== false) {
                            $hasShowName = true;
                            break;
                        }
                    }
                    if ($hasShowName && (stripos($torrentTitle, "S{$seasonFormatted}") !== false ||
                                         stripos($torrentTitle, "Season {$seasonNumber}") !== false ||
                                         stripos($torrentTitle, "Complete") !== false)) {
                        $matchingTorrents[] = $torrent;
                    }
                }
                if (!empty($matchingTorrents)) {
                    usort($matchingTorrents, fn($a, $b) => ($b['seeders'] ?? 0) - ($a['seeders'] ?? 0));
                    $bestTorrent = reset($matchingTorrents);
                    Log::info('Found batch torrent on ThePirateBay', ['torrent' => $bestTorrent['name']]);
                    $infoHash = $bestTorrent['info_hash'];
                    $name = urlencode($bestTorrent['name']);
                    return "magnet:?xt=urn:btih:{$infoHash}&dn={$name}&tr=udp%3A%2F%2Ftracker.coppersurfer.tk%3A6969%2Fannounce&tr=udp%3A%2F%2F9.rarbg.to%3A2920%2Fannounce&tr=udp%3A%2F%2Ftracker.opentrackr.org%3A1337&tr=udp%3A%2F%2Ftracker.internetwarriors.net%3A1337%2Fannounce";
                }
            }
        } catch (\Exception $e) {
            Log::error('Error with TV show fallback', ['error' => $e->getMessage()]);
        }
        return null;
    }

    private function searchNyaa(string $title, string $encodedSearch, string $seasonFormatted, string $episodeFormatted, int $episodeNumber): ?string
    {
        try {
            Log::info('Trying Nyaa API');
            $apiUrl = "https://nyaa.si/?page=rss&q={$encodedSearch}&c=1_2&f=0";
            $response = $this->client->request('GET', $apiUrl);
            $xmlContent = $response->getBody()->getContents();
            $xml = simplexml_load_string($xmlContent);
            if ($xml && isset($xml->channel->item)) {
                $matchingTorrents = [];
                foreach ($xml->channel->item as $item) {
                    $torrentTitle = (string)$item->title;
                    $link = (string)$item->link;
                    $seeders = (int)$item->children('http://xmlns.ezrss.it/0.1/')->torrent->seeds;
                    $seasonEpisodePatterns = [
                        "S{$seasonFormatted}E{$episodeFormatted}",
                        "s{$seasonFormatted}e{$episodeFormatted}",
                        "- {$episodeNumber}",
                        "[Ep {$episodeNumber}]",
                        " - {$episodeFormatted}",
                        "[{$episodeNumber}]",
                        "Episode {$episodeNumber}",
                        "E{$episodeFormatted}",
                        " {$episodeNumber} "
                    ];
                    $hasShowName = stripos($torrentTitle, $title) !== false ||
                                   stripos($torrentTitle, str_replace(' ', '.', $title)) !== false ||
                                   stripos($torrentTitle, str_replace(' ', '-', $title)) !== false ||
                                   stripos($torrentTitle, 'Shingeki no Kyojin') !== false;
                    $hasSeasonEpisode = false;
                    foreach ($seasonEpisodePatterns as $pattern) {
                        if (stripos($torrentTitle, $pattern) !== false) {
                            $hasSeasonEpisode = true;
                            break;
                        }
                    }
                    if ($hasShowName && $hasSeasonEpisode) {
                        $matchingTorrents[] = [
                            'title' => $torrentTitle,
                            'link' => $link,
                            'seeders' => $seeders
                        ];
                    }
                }
                if (!empty($matchingTorrents)) {
                    usort($matchingTorrents, fn($a, $b) => $b['seeders'] - $a['seeders']);
                    $bestTorrent = reset($matchingTorrents);
                    Log::info('Found matching torrent on Nyaa', ['torrent' => $bestTorrent['title']]);
                    return $bestTorrent['link'];
                }
            }
        } catch (\Exception $e) {
            Log::error('Error with Nyaa API', ['error' => $e->getMessage()]);
        }
        return null;
    }

    private function searchAnimeTosho(string $title, string $encodedSearch, string $seasonFormatted, string $episodeFormatted, int $episodeNumber): ?string
    {
        try {
            Log::info('Trying AnimeTosho API');
            $apiUrl = "https://feed.animetosho.org/json?q={$encodedSearch}";
            $response = $this->client->request('GET', $apiUrl);
            $data = json_decode($response->getBody(), true);
            if (is_array($data) && !empty($data)) {
                $matchingTorrents = [];
                foreach ($data as $torrent) {
                    $torrentTitle = $torrent['title'] ?? '';
                    $magnet = $torrent['magnet_uri'] ?? '';
                    $seeders = $torrent['seeders'] ?? 0;
                    $seasonEpisodePatterns = [
                        "S{$seasonFormatted}E{$episodeFormatted}",
                        "s{$seasonFormatted}e{$episodeFormatted}",
                        "- {$episodeNumber}",
                        "[{$episodeNumber}]",
                        "E{$episodeFormatted}",
                        " {$episodeNumber} "
                    ];
                    $hasShowName = stripos($torrentTitle, $title) !== false ||
                                   stripos($torrentTitle, 'Shingeki no Kyojin') !== false;
                    $hasSeasonEpisode = false;
                    foreach ($seasonEpisodePatterns as $pattern) {
                        if (stripos($torrentTitle, $pattern) !== false) {
                            $hasSeasonEpisode = true;
                            break;
                        }
                    }
                    if ($hasShowName && $hasSeasonEpisode) {
                        $matchingTorrents[] = [
                            'title' => $torrentTitle,
                            'link' => $magnet,
                            'seeders' => $seeders
                        ];
                    }
                }
                if (!empty($matchingTorrents)) {
                    usort($matchingTorrents, fn($a, $b) => $b['seeders'] - $a['seeders']);
                    $bestTorrent = reset($matchingTorrents);
                    Log::info('Found matching torrent on AnimeTosho', ['torrent' => $bestTorrent['title']]);
                    return $bestTorrent['link'];
                }
            }
        } catch (\Exception $e) {
            Log::error('Error with AnimeTosho API', ['error' => $e->getMessage()]);
        }
        return null;
    }

    private function searchNyaaFallback(string $title, string $seasonFormatted): ?string
    {
        try {
            Log::info('Trying Nyaa fallback search');
            $fallbackSearch = urlencode($title . " S{$seasonFormatted}");
            $apiUrl = "https://nyaa.si/?page=rss&q={$fallbackSearch}&c=1_2&f=0";
            $response = $this->client->request('GET', $apiUrl);
            $xmlContent = $response->getBody()->getContents();
            $xml = simplexml_load_string($xmlContent);
            if ($xml && isset($xml->channel->item)) {
                $matchingTorrents = [];
                foreach ($xml->channel->item as $item) {
                    $torrentTitle = (string)$item->title;
                    $link = (string)$item->link;
                    $seeders = (int)$item->children('http://xmlns.ezrss.it/0.1/')->torrent->seeds;
                    $hasShowName = stripos($torrentTitle, $title) !== false ||
                                   stripos($torrentTitle, str_replace(' ', '.', $title)) !== false ||
                                   stripos($torrentTitle, str_replace(' ', '-', $title)) !== false ||
                                   stripos($torrentTitle, 'Shingeki no Kyojin') !== false;
                    if ($hasShowName &&
                        (stripos($torrentTitle, "[Complete]") !== false ||
                         stripos($torrentTitle, "Batch") !== false ||
                         stripos($torrentTitle, "S{$seasonFormatted}") !== false)) {
                        $matchingTorrents[] = [
                            'title' => $torrentTitle,
                            'link' => $link,
                            'seeders' => $seeders
                        ];
                    }
                }
                if (!empty($matchingTorrents)) {
                    usort($matchingTorrents, fn($a, $b) => $b['seeders'] - $a['seeders']);
                    $bestTorrent = reset($matchingTorrents);
                    Log::info('Found batch torrent on Nyaa', ['torrent' => $bestTorrent['title']]);
                    return $bestTorrent['link'];
                }
            }
        } catch (\Exception $e) {
            Log::error('Error with Nyaa fallback', ['error' => $e->getMessage()]);
        }
        return null;
    }

    private function filterTorrents(array $torrents, string $title, string $seasonFormatted, string $episodeFormatted, int $episodeNumber, string $titleKey = 'title', string $seedsKey = 'seeds'): array
    {
        $matchingTorrents = [];
        foreach ($torrents as $torrent) {
            $torrentTitle = $torrent[$titleKey] ?? '';
            $showNameVariations = [
                $title,
                str_replace(' ', '.', $title),
                str_replace(' ', '-', $title),
                str_replace('The ', '', $title),
                str_replace('The ', '', str_replace(' ', '.', $title)),
                str_replace('The ', '', str_replace(' ', '-', $title))
            ];
            $hasShowName = false;
            foreach ($showNameVariations as $variation) {
                if (stripos($torrentTitle, $variation) !== false) {
                    $hasShowName = true;
                    break;
                }
            }
            $seasonEpisodePatterns = [
                "S{$seasonFormatted}E{$episodeFormatted}",
                "s{$seasonFormatted}e{$episodeFormatted}",
                "{$seasonFormatted}x{$episodeFormatted}",
                "E{$episodeFormatted}",
                " {$episodeFormatted} ",
                "- {$episodeFormatted}",
                "[{$episodeFormatted}]",
                "Episode {$episodeNumber}"
            ];
            $hasSeasonEpisode = false;
            foreach ($seasonEpisodePatterns as $pattern) {
                if (stripos($torrentTitle, $pattern) !== false) {
                    $hasSeasonEpisode = true;
                    break;
                }
            }
            if ($hasShowName && $hasSeasonEpisode) {
                $matchingTorrents[] = $torrent;
            }
        }
        return $matchingTorrents;
    }

    private function isMatchingTorrent(string $torrentTitle, string $title, string $seasonFormatted, string $episodeFormatted, int $episodeNumber): bool
    {
        $showNameVariations = [
            $title,
            str_replace(' ', '.', $title),
            str_replace(' ', '-', $title),
            str_replace('The ', '', $title),
            str_replace('The ', '', str_replace(' ', '.', $title)),
            str_replace('The ', '', str_replace(' ', '-', $title))
        ];
        $hasShowName = false;
        foreach ($showNameVariations as $variation) {
            if (stripos($torrentTitle, $variation) !== false) {
                $hasShowName = true;
                break;
            }
        }
        $seasonEpisodePatterns = [
            "S{$seasonFormatted}E{$episodeFormatted}",
            "s{$seasonFormatted}e{$episodeFormatted}",
            "{$seasonFormatted}x{$episodeFormatted}",
            "E{$episodeFormatted}",
            " {$episodeFormatted} ",
            "- {$episodeFormatted}",
            "[{$episodeFormatted}]",
            "Episode {$episodeNumber}"
        ];
        $hasSeasonEpisode = false;
        foreach ($seasonEpisodePatterns as $pattern) {
            if (stripos($torrentTitle, $pattern) !== false) {
                $hasSeasonEpisode = true;
                break;
            }
        }
        return $hasShowName && $hasSeasonEpisode;
    }

    private function isAnime(string $title): bool
    {
        $animeKeywords = [
            'anime',
            'attack on titan',
            'shingeki no kyojin',
            'naruto',
            'one piece',
            'dragon ball',
            'demon slayer',
            'my hero academia',
            'jujutsu kaisen',
            'bleach',
            'fullmetal alchemist',
            'hunter x hunter'
        ];
        foreach ($animeKeywords as $keyword) {
            if (stripos($title, $keyword) !== false) {
                return true;
            }
        }
        return preg_match('/[\p{Hiragana}\p{Katakana}\p{Han}]/u', $title) || stripos($title, 'sub') !== false || stripos($title, 'dub') !== false;
    }
}