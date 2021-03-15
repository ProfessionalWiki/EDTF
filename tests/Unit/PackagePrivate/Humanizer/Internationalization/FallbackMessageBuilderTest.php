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
    private ArrayMessageBuilder $primaryBuilder;

    private ArrayMessageBuilder $fallbackBuilder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->primaryBuilder = $this->createMock(ArrayMessageBuilder::class);
        $this->primaryBuilder
            ->method('buildMessage')
            ->willThrowException(new UnknownMessageKey());

        $this->fallbackBuilder = $this->createMock(ArrayMessageBuilder::class);
    }

    public function testBuildMessageThrowsException(): void
    {
        $builder = new FallbackMessageBuilder($this->primaryBuilder, $this->fallbackBuilder);
        $this->expectException(UnknownMessageKey::class);

        $this->fallbackBuilder
            ->expects($this->once())
            ->method('buildMessage')
            ->with('not-existing-key')
            ->willThrowException(new UnknownMessageKey());

        $builder->buildMessage('not-existing-key');
    }

    public function testBuildMessageUseFallbackTranslation(): void
    {
        $builder = new FallbackMessageBuilder($this->primaryBuilder, $this->fallbackBuilder);

        $this->fallbackBuilder
            ->expects($this->once())
            ->method('buildMessage')
            ->with('random-string')
            ->willReturn("Random string");

        $this->assertSame("Random string", $builder->buildMessage('random-string'));
    }
}