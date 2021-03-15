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
    private ArrayMessageBuilder $localeMessageBuilder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->localeMessageBuilder = $this->createMock(ArrayMessageBuilder::class);
        $this->localeMessageBuilder
            ->method('buildMessage')
            ->willThrowException(new UnknownMessageKey());
    }

    public function testBuildMessageThrowsException(): void
    {
        $fallbackMessageBuilder = new FallbackMessageBuilder($this->localeMessageBuilder, []);
        $this->expectException(UnknownMessageKey::class);
        $fallbackMessageBuilder->buildMessage('not-existing-key');
    }

    public function testBuildMessageUseFallbackTranslation(): void
    {
        $fallbackMessageBuilder = new FallbackMessageBuilder($this->localeMessageBuilder, ['random-string' => "Random string"]);
        $this->assertSame("Random string", $fallbackMessageBuilder->buildMessage('random-string'));
    }
}