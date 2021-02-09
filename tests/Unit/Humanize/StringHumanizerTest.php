<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Unit\Humanize;

use EDTF\EdtfParser;
use EDTF\EdtfValue;
use EDTF\Humanize\Humanizer;
use EDTF\Humanize\HumanizerFactory;
use EDTF\Humanize\StringHumanizer;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\Humanize\StringHumanizer
 * @covers \EDTF\Humanize\HumanizerFactory
 */
class StringHumanizerTest extends TestCase {

	public function testLeadingZeroesAreStripped(): void {
		$this->assertSame(
			'43',
			$this->humanize( '0043' )
		);
	}

	private function humanize( string $edtf ): string {
		return HumanizerFactory::newStringHumanizerForLanguage( 'en' )->humanize( $edtf );
	}

	public function testReturnsUnsupportedEdtfAsIs(): void {
		$stringHumanizer = new StringHumanizer(
			new class implements Humanizer {
				public function humanize( EdtfValue $edtf ): string {
					return '';
				}
			},
			new EdtfParser()
		);

		$this->assertSame(
			'0043',
			$stringHumanizer->humanize( '0043' )
		);
	}

}
