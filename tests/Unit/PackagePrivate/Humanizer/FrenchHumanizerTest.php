<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Unit\PackagePrivate\Humanizer;

use EDTF\PackagePrivate\Humanizer\FrenchHumanizer;
use EDTF\Model\Season;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\PackagePrivate\Humanizer\FrenchHumanizer
 */
class FrenchHumanizerTest extends TestCase {

	/**
	 * @dataProvider seasonProvider
	 */
	public function testSeasons( string $expected, Season $season ): void {
		$this->assertSame(
			$expected,
			( new FrenchHumanizer() )->humanize( $season )
		);
	}

	public function seasonProvider(): \Generator {
		yield [ 'Printemps 2021', new Season( 2021, 21 ) ];
		yield [ 'Été 2021', new Season( 2021, 22 ) ];
		yield [ 'Automne 2021', new Season( 2021, 23 ) ];
		yield [ 'Hiver 2021', new Season( 2021, 24 ) ];
		yield [ 'Printemps (Hémisphère nord) 2021', new Season( 2021, 25 ) ];
	}

}
