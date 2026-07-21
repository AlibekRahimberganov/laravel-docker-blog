<?php

namespace App\Services;

use getID3;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaMetadataService
{
    /**
     * Read ID3 tags off an audio file and persist any embedded cover art
     * to the public disk, returning artist name + stored cover path.
     */
    public function extractAudioMetadata(string $absolutePath): array
    {
        $getID3 = new getID3;
        $info = $getID3->analyze($absolutePath);

        $artist = $info['tags']['id3v2']['artist'][0]
            ?? $info['tags']['id3v1']['artist'][0]
            ?? null;
        $coverPath = null;

        $picture = $info['comments']['picture'][0] ?? null;
        if ($picture && ! empty($picture['data'])) {
            $extension = match ($picture['image_mime'] ?? 'image/jpeg') {
                'image/png' => 'png',
                'image/gif' => 'gif',
                default => 'jpg',
            };
            $coverPath = 'media/covers/'.Str::uuid().'.'.$extension;
            Storage::disk('public')->put($coverPath, $picture['data']);
        }

        return ['artist' => $artist, 'cover_path' => $coverPath];
    }
}
