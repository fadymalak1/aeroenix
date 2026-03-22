<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PublicFile
{
    public static function store(?UploadedFile $file, string $directory): ?string
    {
        if (! $file) {
            return null;
        }

        return $file->store($directory, 'public');
    }

    public static function deleteIfStored(?string $path): void
    {
        if (! $path || self::isExternalUrl($path)) {
            return;
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    public static function url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (self::isExternalUrl($path)) {
            return $path;
        }

        return asset('storage/'.$path);
    }

    public static function isExternalUrl(string $path): bool
    {
        return str_starts_with($path, 'http://') || str_starts_with($path, 'https://');
    }
}
