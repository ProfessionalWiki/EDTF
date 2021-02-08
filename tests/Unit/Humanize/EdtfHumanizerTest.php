<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Unit\Humanize;

use EDTF\Humanize\EdtfHumanizer;
use EDTF\Season;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\Humanize\EdtfHumanizer
 */
class EdtfHumanizerTest extends TestCase {

	/**
	 * @dataProvider seasonProvider
	 */
	public function testSeason( string $expected, Season $season ): void {
		$this->assertSame(
			$expected,
			( new EdtfHumanizer() )->humanize( $season )
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

}
