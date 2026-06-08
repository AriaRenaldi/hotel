<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait ResolvesPublicStorageUrl
{
    protected function resolvePublicStorageUrl(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        if (Str::startsWith($value, ['http://', 'https://'])) {
            return $this->normalizeLocalhostUrl($value);
        }

        if (Str::startsWith($value, '/storage/')) {
            return url($value);
        }

        if (Str::startsWith($value, 'storage/')) {
            return url('/' . $value);
        }

        $storageUrl = Storage::disk('public')->url($value);

        if (Str::startsWith($storageUrl, ['http://', 'https://'])) {
            return $this->normalizeLocalhostUrl($storageUrl);
        }

        return url($storageUrl);
    }

    protected function normalizeLocalhostUrl(string $url): string
    {
        $parts = parse_url($url);
        $host = $parts['host'] ?? null;
        $path = $parts['path'] ?? null;

        if (!$host || !$path || !Str::startsWith($path, '/storage/')) {
            return $url;
        }

        if (!in_array($host, ['localhost', '127.0.0.1', '::1'], true)) {
            return $url;
        }

        if (!app()->bound('request')) {
            return $url;
        }

        $request = request();
        $query = isset($parts['query']) ? '?' . $parts['query'] : '';

        return rtrim($request->getSchemeAndHttpHost(), '/') . $path . $query;
    }
}
