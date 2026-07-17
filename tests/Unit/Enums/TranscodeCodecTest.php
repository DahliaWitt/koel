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
        self::assertSame('m4a', TranscodeCodec::AAC->extension());
        self::assertSame('audio/mp4', TranscodeCodec::AAC->mimeType());
        self::assertSame('weba', TranscodeCodec::OPUS->extension());
        self::assertSame('audio/webm', TranscodeCodec::OPUS->mimeType());
    }

    #[Test]
    public function preservesLegacyAacCacheDirectory(): void
    {
        self::assertSame('256', TranscodeCodec::AAC->cacheDirectory(256));
        self::assertSame('opus/256', TranscodeCodec::OPUS->cacheDirectory(256));
    }
}
