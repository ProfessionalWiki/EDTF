<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Unit\PackagePrivate\Humanizer;

use EDTF\EdtfFactory;
use EDTF\EdtfValue;
use EDTF\Model\ExtDate;
use EDTF\Model\Season;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\PackagePrivate\Humanizer\InternationalizedHumanizer
 */
class InternationalizedHumanizerTest extends TestCase {

	/**
	 * @dataProvider seasonProvider
	 */
	public function testSeasons( string $expected, Season $season ): void {
		$this->assertHumanizes( $expected, $season );
	}

	private function assertHumanizes( string $expected, EdtfValue $input ): void {
		$this->assertSame(
			$expected,
			EdtfFactory::newStructuredHumanizerForLanguage( 'en' )->humanize( $input )->getSimpleHumanization()
		);
	}

	public function seasonProvider(): \Generator {
		yield [ 'Spring 2001', new Season( 2001, 21 ) ];
		yield [ 'Summer 1234', new Season( 1234, 22 ) ];
		yield [ 'Autumn 10000', new Season( 10000, 23 ) ];
		yield [ 'Winter 42', new Season( 42, 24 ) ];
		yield [ 'Winter 0', new Season( 0, 24 ) ];
		yield [ 'Winter -1', new Season( -1, 24 ) ];
		yield [ 'First quarter 2001', new Season( 2001, 33 ) ];
		yield [ 'Second quadrimester 2001', new Season( 2001, 38 ) ];
		yield [ 'Second semester 2001', new Season( 2001, 41 ) ];
		yield [ 'Autumn (Southern Hemisphere) 2001', new Season( 2001, 31 ) ];
	}

	/**
	 * @dataProvider simpleDateProvider
	 */
	public function testSimpleDates( string $expected, ExtDate $date ): void {
		$this->assertHumanizes( $expected, $date );
	}

	public function simpleDateProvider(): \Generator {
		yield [ 'January 1st, 2021', new ExtDate( 2021, 1, 1 ) ];
		yield [ 'February 9th, 2021', new ExtDate( 2021, 2, 9 ) ];
		yield [ 'March 13th, 2021', new ExtDate( 2021, 3, 13 ) ];
		yield [ 'April 14th, 2021', new ExtDate( 2021, 4, 14 ) ];
		yield [ 'May 15th, 2021', new ExtDate( 2021, 5, 15 ) ];
		yield [ 'June 16th, 2021', new ExtDate( 2021, 6, 16 ) ];
		yield [ 'July 17th, 2021', new ExtDate( 2021, 7, 17 ) ];
		yield [ 'August 18th, 2021', new ExtDate( 2021, 8, 18 ) ];
		yield [ 'September 19th, 2021', new ExtDate( 2021, 9, 19 ) ];
		yield [ 'October 20th, 2021', new ExtDate( 2021, 10, 20 ) ];
		yield [ 'November 21st, 2021', new ExtDate( 2021, 11, 21 ) ];
		yield [ 'December 30th, 2021', new ExtDate( 2021, 12, 30 ) ];

		yield [ 'January 2021', new ExtDate( 2021, 1 ) ];
		yield [ 'February 2021', new ExtDate( 2021, 2 ) ];

		yield [ '2021', new ExtDate( 2021 ) ];
		yield [ '0', new ExtDate( 0 ) ];
		yield [ '1 BC', new ExtDate( -1 ) ];

		yield [ 'August', new ExtDate( null, 8 ) ];
		yield [ 'January', new ExtDate( null, 1 ) ];

		yield [ 'August 22nd', new ExtDate( null, 8, 22 ) ];
		yield [ 'January 3rd', new ExtDate( null, 1, 3 ) ];

		yield [ '3rd of unknown month, 2021', new ExtDate( 2021, null, 3 ) ];
		yield [ '22nd of unknown month, 2021', new ExtDate( 2021, null, 22 ) ];
	}

}
