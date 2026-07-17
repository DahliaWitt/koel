<?php

namespace App\Enums;

enum TranscodeCodec: string
{
    case AAC = 'aac';
    case OPUS = 'opus';

    public function extension(): string
    {
        return match ($this) {
            self::AAC => 'm4a',
            self::OPUS => 'weba',
        };
    }

    public function mimeType(): string
    {
        return match ($this) {
            self::AAC => 'audio/mp4',
            self::OPUS => 'audio/webm',
        };
    }

    public function cacheDirectory(int $bitRate): string
    {
        return $this === self::AAC ? (string) $bitRate : sprintf('%s/%d', $this->value, $bitRate);
    }
}
