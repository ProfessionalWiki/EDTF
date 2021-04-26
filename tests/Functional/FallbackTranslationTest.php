<?php

namespace EDTF\Tests\Functional;

use EDTF\EdtfFactory;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\PackagePrivate\Humanizer\InternationalizedHumanizer
 * @covers \EDTF\PackagePrivate\Humanizer\Internationalization\TranslationsLoader\JsonFileLoader
 */
class FallbackTranslationTest extends TestCase {

	public function testFallbackEnglishTranslationForMissingJson(): void {
		$humanizer = EdtfFactory::newHumanizerForLanguage( '404', 'en' );

		$this->assertSame(
			"January 1980",
			$humanizer->humanize( EdtfFactory::newParser()->parse( '1980-01' )->getEdtfValue() )
		);
	}
}