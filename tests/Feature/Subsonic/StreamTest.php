<?php

namespace Tests\Feature\Subsonic;

use App\Enums\TranscodeCodec;
use App\Models\Song;
use App\Services\Streamer\Adapters\LocalStreamerAdapter;
use App\Services\Streamer\Adapters\TranscodingStreamerAdapter;
use App\Values\RequestedStreamingConfig;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

use function Tests\create_user;
use function Tests\test_path;

class StreamTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        ob_start();
    }

    protected function tearDown(): void
    {
        ob_end_clean();

        parent::tearDown();
    }

    #[Test]
    public function streamsAudioBytes(): void
    {
        $user = create_user();
        $song = Song::factory()->createOne([
            'path' => test_path('songs/blank.mp3'),
            'owner_id' => $user->id,
        ]);

        $this->mock(LocalStreamerAdapter::class)->expects('stream');

        $this->get("/rest/stream.view?id={$song->id}&apiKey={$user->subsonic_api_key}")->assertOk();
    }

    #[Test]
    public function maxBitRateTriggersTranscoding(): void
    {
        config(['koel.streaming.transcode_codec' => TranscodeCodec::OPUS]);
        $user = create_user();
        $song = Song::factory()->createOne([
            'path' => test_path('songs/blank.mp3'),
            'owner_id' => $user->id,
        ]);

        $this
            ->mock(TranscodingStreamerAdapter::class)
            ->expects('stream')
            ->withArgs(
                static fn (Song $streamedSong, RequestedStreamingConfig $config): bool => (
                    $streamedSong->is($song)
                    && $config->codec === TranscodeCodec::AAC
                ),
            );

        $this->get("/rest/stream.view?id={$song->id}&apiKey={$user->subsonic_api_key}&maxBitRate=128")->assertOk();
    }

    #[Test]
    public function unknownIdReturnsCode70(): void
    {
        $user = create_user();

        $this
            ->getJson("/rest/stream.view?apiKey={$user->subsonic_api_key}&f=json&id=does-not-exist")
            ->assertOk()
            ->assertJsonPath('subsonic-response.status', 'failed')
            ->assertJsonPath('subsonic-response.error.code', 70);
    }
}
