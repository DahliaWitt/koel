<?php

namespace App\Services\Streamer\Adapters;

use App\Enums\TranscodeCodec;
use App\Models\Song;
use App\Services\Streamer\Adapters\Concerns\StreamsLocalPath;
use App\Services\Transcoding\TranscodeStrategyFactory;
use App\Values\RequestedStreamingConfig;
use Illuminate\Container\Attributes\Config;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class TranscodingStreamerAdapter implements StreamerAdapter
{
    use StreamsLocalPath;

    public function __construct(
        #[Config('koel.streaming.bitrate')]
        private readonly int $defaultBitRate,
    ) {}

    public function stream(Song $song, ?RequestedStreamingConfig $config = null)
    {
        abort_unless(
            is_executable(config('koel.streaming.ffmpeg_path')),
            Response::HTTP_INTERNAL_SERVER_ERROR,
            'ffmpeg not found or not executable.',
        );

        $codec = $config->codec ?? TranscodeCodec::default();
        $bitRate = $config?->bitRate ?: $this->defaultBitRate;

        $transcodePath = TranscodeStrategyFactory::make($song->storage)->getTranscodeLocation($song, $bitRate, $codec);

        if (Str::startsWith($transcodePath, ['http://', 'https://'])) {
            return response()->redirectTo($transcodePath);
        }

        $this->streamLocalPath($transcodePath, $codec->mimeType());
    }
}
