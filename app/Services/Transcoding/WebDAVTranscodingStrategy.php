<?php

namespace App\Services\Transcoding;

use App\Enums\SongStorageType;
use App\Enums\TranscodeCodec;
use App\Helpers\Ulid;
use App\Models\Song;
use App\Services\SongStorages\WebDAVStorage;
use Illuminate\Support\Facades\File;
use Throwable;
use Webmozart\Assert\Assert;

class WebDAVTranscodingStrategy extends TranscodingStrategy
{
    public function getTranscodeLocation(Song $song, int $bitRate, TranscodeCodec $codec): string
    {
        $transcode = $this->findTranscode($song, $bitRate, $codec);

        if ($transcode?->isValid()) {
            return $transcode->location;
        }

        if ($transcode) {
            File::delete($transcode->location);
        }

        /** @var WebDAVStorage $storage */
        $storage = app(WebDAVStorage::class);
        $tmpSource = $storage->copyToLocal($song->storage_metadata->getPath());

        $destination = artifact_path(sprintf(
            'transcodes/%s/%s.%s',
            $codec->cacheDirectory($bitRate),
            Ulid::generate(),
            $codec->extension(),
        ));

        try {
            $this->transcodeAndUpsert($song, $tmpSource, $destination, $bitRate, $codec);
        } catch (Throwable $e) {
            File::delete($destination);

            throw $e;
        } finally {
            File::delete($tmpSource);
        }

        return $destination;
    }

    public function deleteTranscodeFile(string $location, SongStorageType $storageType): void
    {
        Assert::eq($storageType, SongStorageType::WEBDAV);

        File::delete($location);
    }
}
