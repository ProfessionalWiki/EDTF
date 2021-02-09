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

	private function humanize( string $edtf ): string {
		return HumanizerFactory::newStringHumanizerForLanguage( 'en' )->humanize( $edtf );
	}

	/**
	 * @dataProvider humanizationProvider
	 */
	public function testUnspecifiedDigits( string $edtf, string $humanized ): void {
		$this->assertSame(
			$humanized,
			$this->humanize( $edtf )
		);
	}

	public function humanizationProvider(): \Generator {
		yield 'Full date' => [ '1975-07-01', 'July 1st, 1975' ];
		yield 'Year and month' => [ '1975-07', 'July 1975' ];
		yield 'Year only' => [ '1975', '1975' ];

		yield 'Leading zeroes' => [ '0042', '42' ];

		yield 'Seasons' => [ '1975-22', 'Summer 1975' ];

		yield 'Month only' => [ 'XXXX-12-XX', 'December' ];
		yield 'Day only' => [ 'XXXX-XX-12', '12th' ];
		yield 'Month and day' => [ 'XXXX-12-11', 'December 11th' ];
		yield 'Year and day' => [ '2020-XX-11', '11th of unknown month, 2020' ];
	}

}
