<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Unit\PackagePrivate\Humanizer\Internationalization;

use EDTF\PackagePrivate\Humanizer\Internationalization\ArrayMessageBuilder;
use EDTF\PackagePrivate\Humanizer\Internationalization\FallbackMessageBuilder;
use EDTF\PackagePrivate\Humanizer\Internationalization\UnknownMessageKey;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\PackagePrivate\Humanizer\Internationalization\FallbackMessageBuilder
 */
class FallbackMessageBuilderTest extends TestCase
{
    public function testBuildMessageThrowsException(): void
    {
        $primaryBuilder = $this->createMock(ArrayMessageBuilder::class);
        $primaryBuilder
            ->method('buildMessage')
            ->willThrowException(new UnknownMessageKey());

        $fallbackBuilder = $this->createMock(ArrayMessageBuilder::class);
        $fallbackBuilder
            ->expects($this->once())
            ->method('buildMessage')
            ->with('not-existing-key')
            ->willThrowException(new UnknownMessageKey());

        $builder = new FallbackMessageBuilder($primaryBuilder, $fallbackBuilder);
        $this->expectException(UnknownMessageKey::class);

        $builder->buildMessage('not-existing-key');
    }

    public function testBuildMessageUseFallbackTranslation(): void
    {
        $primaryBuilder = $this->createMock(ArrayMessageBuilder::class);
        $primaryBuilder
            ->method('buildMessage')
            ->willThrowException(new UnknownMessageKey());

        $fallbackBuilder = $this->createMock(ArrayMessageBuilder::class);
        $fallbackBuilder
            ->expects($this->once())
            ->method('buildMessage')
            ->with('random-string')
            ->willReturn("Random string");

        $builder = new FallbackMessageBuilder($primaryBuilder, $fallbackBuilder);

        $this->assertSame("Random string", $builder->buildMessage('random-string'));
    }

    public function testBuildMessageUsePrimaryTranslation(): void
    {
        $primaryBuilder = $this->createMock(ArrayMessageBuilder::class);
        $primaryBuilder
            ->method('buildMessage')
            ->with('random-string')
            ->willReturn('Random string');

        $fallbackBuilder = $this->createMock(ArrayMessageBuilder::class);
        $fallbackBuilder
            ->expects($this->never())
            ->method('buildMessage');

        $builder = new FallbackMessageBuilder($primaryBuilder, $fallbackBuilder);
        $this->assertSame("Random string", $builder->buildMessage('random-string'));
    }
}