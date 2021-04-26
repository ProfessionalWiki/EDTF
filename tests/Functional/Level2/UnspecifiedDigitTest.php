<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Functional\Level2;

use Carbon\Carbon;
use EDTF\Tests\Unit\FactoryTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\PackagePrivate\Parser\Parser
 * @covers \EDTF\Model\ExtDate
 * @package EDTF\Tests\Functional
 */
class UnspecifiedDigitTest extends TestCase {

	use FactoryTrait;

	public function testWithOneDigitYearOnly() {
		$d = $this->createExtDate( '156X-12-25' );

		$this->assertTrue( $d->unspecified() );
		$this->assertTrue( $d->unspecified( 'year' ) );
		$this->assertFalse( $d->unspecified( 'month' ) );
		$this->assertFalse( $d->unspecified( 'day' ) );

		$this->assertSame( 1560, $d->getYear() );

		$expectedMin = Carbon::create( 1560, 12, 25 )->getTimestamp();
		$this->assertSame( $expectedMin, $d->getMin() );

		$expectedMax = Carbon::create( 1569, 12, 25, 23, 59, 59 )->getTimestamp();
		$this->assertSame( $expectedMax, $d->getMax() );
	}

	public function testWithTwoDigitYear() {
		$d = $this->createExtDate( '15XX-12-25' );

		$this->assertTrue( $d->unspecified() );
		$this->assertTrue( $d->unspecified( 'year' ) );
		$this->assertFalse( $d->unspecified( 'month' ) );
		$this->assertFalse( $d->unspecified( 'day' ) );

		$this->assertSame( 1500, $d->getYear() );

		$expectedMin = Carbon::create( 1500, 12, 25 )->getTimestamp();
		$this->assertSame( $expectedMin, $d->getMin() );

		$expectedMax = Carbon::create( 1599, 12, 25, 23, 59, 59 )->getTimestamp();
		$this->assertSame( $expectedMax, $d->getMax() );
	}

	public function testWithUnspecifiedYearAndDay() {
		$d = $this->createExtDate( 'XXXX-12-XX' );

		$this->assertTrue( $d->unspecified() );
		$this->assertTrue( $d->unspecified( 'year' ) );
		$this->assertFalse( $d->unspecified( 'month' ) );
		$this->assertTrue( $d->unspecified( 'day' ) );

		$this->assertNull( $d->getYear() );
		$this->assertSame( 0, $d->getMin() );
		$this->assertSame( 0, $d->getMax() );
	}

	public function testWithThreeDigitYearAndTwoDigitMonth() {
		$d = $this->createExtDate( '1XXX-XX' );

		$this->assertTrue( $d->unspecified() );
		$this->assertTrue( $d->unspecified( 'year' ) );
		$this->assertTrue( $d->unspecified( 'month' ) );
		$this->assertFalse( $d->unspecified( 'day' ) );

		$this->assertSame( 1000, $d->getYear() );
		$this->assertNull( $d->getMonth() );

		$expectedMin = Carbon::create( 1000 )->getTimestamp();
		$this->assertSame( $expectedMin, $d->getMin() );

		$expectedMax = Carbon::create( 1999, 12, 31, 23, 59, 59 )->getTimestamp();
		$this->assertSame( $expectedMax, $d->getMax() );
	}

	public function testWithThreeDigitYearOnly() {
		$d = $this->createExtDate( '1XXX-12' );

		$this->assertTrue( $d->unspecified() );
		$this->assertTrue( $d->unspecified( 'year' ) );
		$this->assertFalse( $d->unspecified( 'month' ) );
		$this->assertFalse( $d->unspecified( 'day' ) );

		$this->assertSame( 1000, $d->getYear() );
		$this->assertSame( 12, $d->getMonth() );

		$expectedMin = Carbon::create( 1000, 12 )->getTimestamp();
		$this->assertSame( $expectedMin, $d->getMin() );

		$expectedMax = Carbon::create( 1999, 12, 31, 23, 59, 59 )->getTimestamp();
		$this->assertSame( $expectedMax, $d->getMax() );
	}

	public function testWithOneDigitMonth() {
		$d = $this->createExtDate( '1984-1X' );

		$this->assertTrue( $d->unspecified() );
		$this->assertFalse( $d->unspecified( 'year' ) );
		$this->assertTrue( $d->unspecified( 'month' ) );
		$this->assertFalse( $d->unspecified( 'day' ) );

		$this->assertSame( 1984, $d->getYear() );
		$this->assertSame( 10, $d->getMonth() );

		$expectedMin = Carbon::create( 1984, 10 )->getTimestamp();
		$this->assertSame( $expectedMin, $d->getMin() );

		$expectedMax = Carbon::create( 1984, 12, 31, 23, 59, 59 )->getTimestamp();
		$this->assertSame( $expectedMax, $d->getMax() );
	}

	public function testWithThreeDigitYearAndOneDigitMonthLessThan10() {
		$d = $this->createExtDate( '1XXX-0X' );
		$this->assertTrue( $d->unspecified() );
		$this->assertTrue( $d->unspecified( 'year' ) );
		$this->assertTrue( $d->unspecified( 'month' ) );
		$this->assertFalse( $d->unspecified( 'day' ) );

		$this->assertSame( 1000, $d->getYear() );

		// TODO: implement the logic to support this case (in case of unspecified, return the least possible)
		// $this->assertSame(1, $d->getMonth());

		$expectedMin = Carbon::create( 1000 )->getTimestamp();
		$this->assertSame( $expectedMin, $d->getMin() );

		$expectedMax = Carbon::create( 1999, 9, 30, 23, 59, 59 )->getTimestamp();
		$this->assertSame( $expectedMax, $d->getMax() );
	}

}