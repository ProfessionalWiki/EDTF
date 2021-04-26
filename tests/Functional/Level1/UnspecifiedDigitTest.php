<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Functional\Level1;

use Carbon\Carbon;
use EDTF\Tests\Unit\FactoryTrait;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers \EDTF\Model\ExtDate
 * @covers \EDTF\Model\UnspecifiedDigit
 *
 * @package EDTF\Tests\Functional
 */
class UnspecifiedDigitTest extends TestCase {

	use FactoryTrait;

	/**
	 * @dataProvider unspecifiedYear
	 * @throws RuntimeException
	 */
	public function testWithUnspecifiedYear( string $input, int $expectedYear, int $expectedMin, int $expectedMax ) {
		$d = $this->createExtDate( $input );
		$this->assertTrue( $d->unspecified() );
		$this->assertTrue( $d->unspecified( 'year' ) );
		$this->assertFalse( $d->unspecified( 'month' ) );
		$this->assertFalse( $d->unspecified( 'day' ) );
		$this->assertSame( $expectedYear, $d->getYear() );

		$this->assertSame( $expectedMin, $d->getMin() );
		$this->assertSame( $expectedMax, $d->getMax() );
	}

	public function testWithUnspecifiedMonth() {
		$d = $this->createExtDate( '2004-XX' );
		$this->assertTrue( $d->unspecified() );
		$this->assertFalse( $d->unspecified( 'year' ) );
		$this->assertTrue( $d->unspecified( 'month' ) );

		$this->assertSame( 2004, $d->getYear() );
		$this->assertNull( $d->getMonth() );

		$expectedMin = Carbon::create( 2004 )->getTimestamp();
		$this->assertSame( $expectedMin, $d->getMin() );

		$expectedMax = Carbon::create( 2004, 12, 31, 23, 59, 59 )->getTimestamp();
		$this->assertSame( $expectedMax, $d->getMax() );
	}

	/**
	 * @dataProvider unspecifiedDay
	 * @throws RuntimeException
	 */
	public function testWithUnspecifiedDay( string $input, int $expectedYear, int $expectedMonth, $expectedDay, int $expectedMin, int $expectedMax ) {
		$d = $this->createExtDate( $input );
		$this->assertTrue( $d->unspecified() );
		$this->assertFalse( $d->unspecified( 'year' ) );
		$this->assertFalse( $d->unspecified( 'month' ) );
		$this->assertTrue( $d->unspecified( 'day' ) );

		$this->assertSame( $expectedYear, $d->getYear() );
		$this->assertSame( $expectedMonth, $d->getMonth() );
		$this->assertSame( $expectedDay, $d->getDay() );

		$this->assertSame( $expectedMin, $d->getMin() );
		$this->assertSame( $expectedMax, $d->getMax() );
	}

	public function testWithUnspecifiedMonthAndDay() {
		$d = $this->createExtDate( '1985-XX-XX' );

		$this->assertTrue( $d->unspecified() );
		$this->assertFalse( $d->unspecified( 'year' ) );
		$this->assertTrue( $d->unspecified( 'month' ) );
		$this->assertTrue( $d->unspecified( 'day' ) );

		$this->assertSame( 1985, $d->getYear() );
		$this->assertNull( $d->getMonth() );
		$this->assertNull( $d->getDay() );

		$expectedMin = Carbon::create( 1985 )->getTimestamp();
		$this->assertSame( $expectedMin, $d->getMin() );

		$expectedMax = Carbon::create( 1985, 12, 31, 23, 59, 59 )->getTimestamp();
		$this->assertSame( $expectedMax, $d->getMax() );
	}

	// TODO: check more cases

	public function unspecifiedYear() {
		return [
			[
				'201X',
				2010,
				Carbon::create( 2010 )->getTimestamp(),
				Carbon::create( 2019, 12, 31, 23, 59, 59 )->getTimestamp()
			],
			[
				'21XX',
				2100,
				Carbon::create( 2100 )->getTimestamp(),
				Carbon::create( 2199, 12, 31, 23, 59, 59 )->getTimestamp()
			],
			[
				'1XXX',
				1000,
				Carbon::create( 1000 )->getTimestamp(),
				Carbon::create( 1999, 12, 31, 23, 59, 59 )->getTimestamp()
			]
		];
	}

	public function unspecifiedDay() {
		return [
			[
				'1985-04-XX',
				1985,
				4,
				null,
				Carbon::create( 1985, 4 )->getTimestamp(),
				Carbon::create( 1985, 4, 30, 23, 59, 59 )->getTimestamp()
			],
			[
				'1985-02-1X',
				1985,
				2,
				10,
				Carbon::create( 1985, 2, 10 )->getTimestamp(),
				Carbon::create( 1985, 2, 19, 23, 59, 59 )->getTimestamp()
			],
			[
				'1985-02-2X',
				1985,
				2,
				20,
				Carbon::create( 1985, 2, 20 )->getTimestamp(),
				Carbon::create( 1985, 2, 28, 23, 59, 59 )->getTimestamp()
			],
			/* TODO: wrong behaviour! check this case
			ExtDate object lives as 2000-02-30 but it shouldn't (only 29 days in February)
			[
				'2000-02-3X',
				2000,
				2,
				30,
				Carbon::create(2000, 2, 29, 0, 0, 0)->getTimestamp(),
				Carbon::create(2000, 2, 29, 23, 59, 59)->getTimestamp()
			],
			*/
			[
				'2010-01-3X',
				2010,
				1,
				30,
				Carbon::create( 2010, 1, 30 )->getTimestamp(),
				Carbon::create( 2010, 1, 31, 23, 59, 59 )->getTimestamp()
			],
			[
				'1700-05-0X',
				1700,
				5,
				null,
				Carbon::create( 1700, 5 )->getTimestamp(),
				Carbon::create( 1700, 5, 9, 23, 59, 59 )->getTimestamp()
			],
			[
				'1700-02-0X',
				1700,
				2,
				null,
				Carbon::create( 1700, 2 )->getTimestamp(),
				Carbon::create( 1700, 2, 9, 23, 59, 59 )->getTimestamp()
			]
		];
	}
}