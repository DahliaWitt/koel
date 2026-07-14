<?php

namespace App\Enums;

enum TranscodeCodec: string
{
    case Aac = 'aac';
    case Opus = 'opus';

    public function extension(): string
    {
        return match ($this) {
            self::Aac => 'm4a',
            self::Opus => 'weba',
        };
    }

    public function mimeType(): string
    {
        return match ($this) {
            self::Aac => 'audio/mp4',
            self::Opus => 'audio/webm',
        };
    }

    public function cacheDirectory(int $bitRate): string
    {
        return $this === self::Aac ? (string) $bitRate : sprintf('%s/%d', $this->value, $bitRate);
    }
}
