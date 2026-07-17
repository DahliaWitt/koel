<?php

namespace App\Values;

use App\Enums\TranscodeCodec;

final readonly class RequestedStreamingConfig
{
    private function __construct(
        public bool $transcode,
        public ?int $bitRate,
        public float $startTime,
        public TranscodeCodec $codec,
    ) {}

    public static function make(
        bool $transcode = false,
        ?int $bitRate = 128,
        float $startTime = 0.0,
        TranscodeCodec $codec = TranscodeCodec::AAC,
    ): self {
        return new self($transcode, $bitRate, $startTime, $codec);
    }
}
