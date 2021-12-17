<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Unit\PackagePrivate\Humanizer;

use EDTF\PackagePrivate\Humanizer\Strategy\EnglishStrategy;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\PackagePrivate\Humanizer\Strategy\EnglishStrategy
 */
class EnglishStrategyTest extends TestCase {

	/**
	 * @dataProvider ordinalProvider
	 */
	public function testOrdinals( int $number, string $expected ): void {
		$this->assertSame(
			$expected,
			( new EnglishStrategy() )->applyOrdinalEnding( $number )
		);
	}

	public function ordinalProvider(): iterable {
		yield [ 0, '0th' ];
		yield [ 1, '1st' ];
		yield [ 2, '2nd' ];
		yield [ 3, '3rd' ];
		yield [ 4, '4th' ];
		yield [ 5, '5th' ];
		yield [ 9, '9th' ];

		yield [ 10, '10th' ];
		yield [ 11, '11th' ];
		yield [ 13, '13th' ];
		yield [ 1337, '1337th' ];

		yield [ -1, '-1st' ];
		yield [ -2, '-2nd' ];
		yield [ -1337, '-1337th' ];
	}
	
}
