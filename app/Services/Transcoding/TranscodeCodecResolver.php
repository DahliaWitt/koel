<?php

namespace App\Services\Transcoding;

use App\Enums\TranscodeCodec;
use App\Models\Song;
use Illuminate\Container\Attributes\Config;

class TranscodeCodecResolver
{
    public function __construct(
        #[Config('koel.streaming.transcode_compatibility_codec')]
        private readonly TranscodeCodec $compatibilityCodec,
        private readonly Transcoder $transcoder,
    ) {}

    /**
     * Resolve the codec for a streaming request. Forced (mobile) and FLAC-policy transcodes stay
     * on AAC so existing clients keep their current behavior; only automatic compatibility
     * transcoding uses the configured codec, provided FFmpeg supports it.
     */
    public function resolve(Song $song, bool $forceTranscode): TranscodeCodec
    {
        if ($forceTranscode || $song->isFlac()) {
            return TranscodeCodec::Aac;
        }

        if (!$this->transcoder->supports($this->compatibilityCodec)) {
            return TranscodeCodec::Aac;
        }

        return $this->compatibilityCodec;
    }
}
