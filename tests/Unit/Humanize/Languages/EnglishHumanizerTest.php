<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Unit\Humanize\Languages;

use EDTF\EdtfValue;
use EDTF\ExtDate;
use EDTF\Humanize\Languages\EnglishHumanizer;
use EDTF\Season;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\Humanize\Languages\EnglishHumanizer
 */
class EnglishHumanizerTest extends TestCase {

	/**
	 * @dataProvider seasonProvider
	 */
	public function testSeasons( string $expected, Season $season ): void {
		$this->assertHumanizes( $expected, $season );
	}

	private function assertHumanizes( string $expected, EdtfValue $input ): void {
		$this->assertSame(
			$expected,
			( new EnglishHumanizer() )->humanize( $input )
		);
	}

	public function seasonProvider(): \Generator {
		yield [ 'Spring 2001', new Season( 2001, 21 ) ];
		yield [ 'Summer 1234', new Season( 1234, 22 ) ];
		yield [ 'Autumn 10000', new Season( 10000, 23 ) ];
		yield [ 'Winter 42', new Season( 42, 24 ) ];
		yield [ 'Winter 0', new Season( 0, 24 ) ];
		yield [ 'Winter -1', new Season( -1, 24 ) ];
		yield [ 'Quarter 1 2001', new Season( 2001, 33 ) ];
		yield [ 'Quadrimester 2 2001', new Season( 2001, 38 ) ];
		yield [ 'Semester 2 2001', new Season( 2001, 41 ) ];
		yield [ 'Autumn (Southern Hemisphere) 2001', new Season( 2001, 31 ) ];
	}

	/**
	 * @dataProvider simpleDateProvider
	 */
	public function testSimpleDates( string $expected, ExtDate $date ): void {
		$this->assertHumanizes( $expected, $date );
	}

	public function simpleDateProvider(): \Generator {
		yield [ 'January 1, 2021', new ExtDate( 2021, 1, 1 ) ];
		yield [ 'February 9, 2021', new ExtDate( 2021, 2, 9 ) ];
		yield [ 'March 13, 2021', new ExtDate( 2021, 3, 13 ) ];
		yield [ 'April 14, 2021', new ExtDate( 2021, 4, 14 ) ];
		yield [ 'May 15, 2021', new ExtDate( 2021, 5, 15 ) ];
		yield [ 'June 16, 2021', new ExtDate( 2021, 6, 16 ) ];
		yield [ 'July 17, 2021', new ExtDate( 2021, 7, 17 ) ];
		yield [ 'August 18, 2021', new ExtDate( 2021, 8, 18 ) ];
		yield [ 'September 19, 2021', new ExtDate( 2021, 9, 19 ) ];
		yield [ 'October 20, 2021', new ExtDate( 2021, 10, 20 ) ];
		yield [ 'November 21, 2021', new ExtDate( 2021, 11, 21 ) ];
		yield [ 'December 30, 2021', new ExtDate( 2021, 12, 30 ) ];

		yield [ 'January 2021', new ExtDate( 2021, 1 ) ];
		yield [ 'February 2021', new ExtDate( 2021, 2 ) ];

		yield [ '2021', new ExtDate( 2021 ) ];
		yield [ '0', new ExtDate( 0 ) ];
		yield [ '-1', new ExtDate( -1 ) ];

		yield [ 'August', new ExtDate( null, 8 ) ];
		yield [ 'January', new ExtDate( null, 1 ) ];

		yield [ 'August 10', new ExtDate( null, 8, 10 ) ];
		yield [ 'January 5', new ExtDate( null, 1, 5 ) ];
	}

}
