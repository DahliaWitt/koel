<?php

namespace Tests\Unit\Enums;

use App\Enums\TranscodeCodec;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class TranscodeCodecTest extends TestCase
{
    #[Test]
    public function providesContainerMetadata(): void
    {
        self::assertSame('m4a', TranscodeCodec::Aac->extension());
        self::assertSame('audio/mp4', TranscodeCodec::Aac->mimeType());
        self::assertSame('weba', TranscodeCodec::Opus->extension());
        self::assertSame('audio/webm', TranscodeCodec::Opus->mimeType());
    }

    #[Test]
    public function preservesLegacyAacCacheDirectory(): void
    {
        self::assertSame('256', TranscodeCodec::Aac->cacheDirectory(256));
        self::assertSame('opus/256', TranscodeCodec::Opus->cacheDirectory(256));
    }
}
