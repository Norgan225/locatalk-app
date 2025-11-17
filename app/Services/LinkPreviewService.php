<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LinkPreviewService
{
    /**
     * Extraire les métadonnées d'une URL
     *
     * @param string $url
     * @return array|null
     */
    public function extractMetadata($url)
    {
        // Valider l'URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        // Vérifier le cache (24h)
        $cacheKey = 'link_preview_' . md5($url);
        $cached = Cache::get($cacheKey);

        if ($cached) {
            return $cached;
        }

        try {
            // Récupérer le contenu HTML
            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (compatible; LocaTalk/1.0; +https://locatalk.com)'
                ])
                ->get($url);

            if (!$response->successful()) {
                return null;
            }

            $html = $response->body();

            // Extraire les métadonnées
            $metadata = [
                'url' => $url,
                'title' => $this->extractTitle($html, $url),
                'description' => $this->extractDescription($html),
                'image' => $this->extractImage($html, $url),
                'site_name' => $this->extractSiteName($html, $url),
                'favicon' => $this->extractFavicon($html, $url),
                'type' => $this->extractType($html),
                'video' => $this->extractVideo($html),
            ];

            // Nettoyer les métadonnées
            $metadata = array_filter($metadata, function($value) {
                return !empty($value);
            });

            // Mettre en cache pour 24h
            Cache::put($cacheKey, $metadata, now()->addHours(24));

            return $metadata;

        } catch (\Exception $e) {
            Log::error('Link preview error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Extraire le titre
     */
    private function extractTitle($html, $url)
    {
        // Open Graph
        if (preg_match('/<meta\s+property=["\']og:title["\']\s+content=["\'](.*?)["\']/i', $html, $matches)) {
            return html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
        }

        // Twitter Card
        if (preg_match('/<meta\s+name=["\']twitter:title["\']\s+content=["\'](.*?)["\']/i', $html, $matches)) {
            return html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
        }

        // Title tag
        if (preg_match('/<title>(.*?)<\/title>/i', $html, $matches)) {
            return html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
        }

        // Fallback: domaine
        return parse_url($url, PHP_URL_HOST);
    }

    /**
     * Extraire la description
     */
    private function extractDescription($html)
    {
        // Open Graph
        if (preg_match('/<meta\s+property=["\']og:description["\']\s+content=["\'](.*?)["\']/i', $html, $matches)) {
            return html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
        }

        // Twitter Card
        if (preg_match('/<meta\s+name=["\']twitter:description["\']\s+content=["\'](.*?)["\']/i', $html, $matches)) {
            return html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
        }

        // Meta description
        if (preg_match('/<meta\s+name=["\']description["\']\s+content=["\'](.*?)["\']/i', $html, $matches)) {
            return html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
        }

        return null;
    }

    /**
     * Extraire l'image
     */
    private function extractImage($html, $url)
    {
        // Open Graph
        if (preg_match('/<meta\s+property=["\']og:image["\']\s+content=["\'](.*?)["\']/i', $html, $matches)) {
            return $this->normalizeUrl($matches[1], $url);
        }

        // Twitter Card
        if (preg_match('/<meta\s+name=["\']twitter:image["\']\s+content=["\'](.*?)["\']/i', $html, $matches)) {
            return $this->normalizeUrl($matches[1], $url);
        }

        return null;
    }

    /**
     * Extraire le nom du site
     */
    private function extractSiteName($html, $url)
    {
        // Open Graph
        if (preg_match('/<meta\s+property=["\']og:site_name["\']\s+content=["\'](.*?)["\']/i', $html, $matches)) {
            return html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
        }

        // Fallback: domaine
        return parse_url($url, PHP_URL_HOST);
    }

    /**
     * Extraire le favicon
     */
    private function extractFavicon($html, $url)
    {
        // Link rel="icon"
        if (preg_match('/<link[^>]+rel=["\'](?:shortcut )?icon["\'][^>]+href=["\'](.*?)["\']/i', $html, $matches)) {
            return $this->normalizeUrl($matches[1], $url);
        }

        // Fallback: /favicon.ico
        $baseUrl = parse_url($url, PHP_URL_SCHEME) . '://' . parse_url($url, PHP_URL_HOST);
        return $baseUrl . '/favicon.ico';
    }

    /**
     * Extraire le type
     */
    private function extractType($html)
    {
        // Open Graph type
        if (preg_match('/<meta\s+property=["\']og:type["\']\s+content=["\'](.*?)["\']/i', $html, $matches)) {
            return $matches[1];
        }

        return 'website';
    }

    /**
     * Extraire la vidéo
     */
    private function extractVideo($html)
    {
        // Open Graph video
        if (preg_match('/<meta\s+property=["\']og:video["\']\s+content=["\'](.*?)["\']/i', $html, $matches)) {
            return $matches[1];
        }

        // Détection YouTube
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $html, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }

        return null;
    }

    /**
     * Normaliser une URL (relative -> absolue)
     */
    private function normalizeUrl($imageUrl, $baseUrl)
    {
        // Déjà absolue
        if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            return $imageUrl;
        }

        // Protocol-relative URL (//example.com/image.jpg)
        if (str_starts_with($imageUrl, '//')) {
            return parse_url($baseUrl, PHP_URL_SCHEME) . ':' . $imageUrl;
        }

        // Relative URL
        $base = parse_url($baseUrl);
        $scheme = $base['scheme'];
        $host = $base['host'];

        // Absolute path (/image.jpg)
        if (str_starts_with($imageUrl, '/')) {
            return "{$scheme}://{$host}{$imageUrl}";
        }

        // Relative path (image.jpg)
        $path = dirname($base['path'] ?? '/');
        return "{$scheme}://{$host}{$path}/{$imageUrl}";
    }

    /**
     * Détecter les URLs dans un texte
     *
     * @param string $text
     * @return array
     */
    public function detectUrls($text)
    {
        $pattern = '/https?:\/\/[^\s<>"{}|\\^`\[\]]+/i';
        preg_match_all($pattern, $text, $matches);

        return $matches[0] ?? [];
    }

    /**
     * Extraire les métadonnées de plusieurs URLs
     *
     * @param array $urls
     * @return array
     */
    public function extractMultiple(array $urls)
    {
        $previews = [];

        foreach ($urls as $url) {
            $metadata = $this->extractMetadata($url);
            if ($metadata) {
                $previews[] = $metadata;
            }
        }

        return $previews;
    }
}
