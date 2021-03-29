<?php

namespace EDTF\Tests\Functional;

use EDTF\EdtfFactory;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\PackagePrivate\Humanizer\InternationalizedHumanizer
 * @covers \EDTF\PackagePrivate\Humanizer\Internationalization\TranslationsLoader\JsonFileLoader
 */
class FallbackTranslationTest extends TestCase
{
    private const TEST_RESOURCES_PATH = __DIR__ . '/resources/i18n';

    public function testFallbackEnglishTranslationForJanuary(): void
    {
        $humanizer = EdtfFactory::newHumanizerForLanguage('fr', 'en', self::TEST_RESOURCES_PATH);

        $this->assertSame(
            "January 1980",
            $humanizer->humanize(EdtfFactory::newParser()->parse('1980-01')->getEdtfValue())
        );

        $this->assertSame(
            "Février 1980",
            $humanizer->humanize(EdtfFactory::newParser()->parse('1980-02')->getEdtfValue())
        );
    }

    public function testFallbackEnglishTranslationForMissingJson(): void
    {
        $humanizer = EdtfFactory::newHumanizerForLanguage('de', 'en', self::TEST_RESOURCES_PATH);

        $this->assertSame(
            "January 1980",
            $humanizer->humanize(EdtfFactory::newParser()->parse('1980-01')->getEdtfValue())
        );
    }
}