<?php

namespace EDTF\Tests\Unit\Model;

use EDTF\Model\ExtDate;
use EDTF\Model\Interval;
use EDTF\Model\IntervalSide;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\Model\Interval
 * @package EDTF\Tests\Unit
 */
class IntervalTest extends TestCase {

	public function testCannotCreateIntervalWithTwoOpenSides(): void {
		$openSide = IntervalSide::newOpenInterval();

		$this->expectException( InvalidArgumentException::class );
		new Interval( $openSide, $openSide );
	}

	public function testNormalIntervalIsOnlyNormal(): void {
		$interval = new Interval(
			IntervalSide::newFromDate( new ExtDate( 2020, 2, 8 ) ),
			IntervalSide::newFromDate( new ExtDate( 2020, 2, 9 ) )
		);

		$this->assertTrue( $interval->isNormalInterval() );
		$this->assertFalse( $interval->isOpenInterval() );
		$this->assertFalse( $interval->isUnknownInterval() );
	}

	public function testOpenIntervalIsOnlyOpen(): void {
		$interval = new Interval(
			IntervalSide::newFromDate( new ExtDate( 2020, 2, 8 ) ),
			IntervalSide::newOpenInterval()
		);

		$this->assertFalse( $interval->isNormalInterval() );
		$this->assertTrue( $interval->isOpenInterval() );
		$this->assertFalse( $interval->isUnknownInterval() );
	}

	public function testUnknownIntervalIsOnlyUnknown(): void {
		$interval = new Interval(
			IntervalSide::newUnknownInterval(),
			IntervalSide::newFromDate( new ExtDate( 2020, 2, 8 ) )
		);

		$this->assertFalse( $interval->isNormalInterval() );
		$this->assertFalse( $interval->isOpenInterval() );
		$this->assertTrue( $interval->isUnknownInterval() );
	}

	public function testHasStartAndEndDateForIntervalWithOpenStart(): void {
		$interval = new Interval(
			IntervalSide::newOpenInterval(),
			IntervalSide::newFromDate( new ExtDate( 2020, 2, 8 ) )
		);

		$this->assertFalse( $interval->hasStartDate() );
		$this->assertTrue( $interval->hasEndDate() );
	}

	public function testHasStartAndEndDateForIntervalWithUnknownStart(): void {
		$interval = new Interval(
			IntervalSide::newUnknownInterval(),
			IntervalSide::newFromDate( new ExtDate( 2020, 2, 8 ) )
		);

		$this->assertFalse( $interval->hasStartDate() );
		$this->assertTrue( $interval->hasEndDate() );
	}

	public function testHasStartAndEndDateForIntervalWithOpenEnd(): void {
		$interval = new Interval(
			IntervalSide::newFromDate( new ExtDate( 2020, 2, 8 ) ),
			IntervalSide::newOpenInterval()
		);

		$this->assertFalse( $interval->hasEndDate() );
		$this->assertTrue( $interval->hasStartDate() );
	}

	public function testHasStartAndEndDateForIntervalWithUnknownEnd(): void {
		$interval = new Interval(
			IntervalSide::newFromDate( new ExtDate( 2020, 2, 8 ) ),
			IntervalSide::newUnknownInterval()
		);

		$this->assertFalse( $interval->hasEndDate() );
		$this->assertTrue( $interval->hasStartDate() );
	}

	public function testEndCannotBeBeforeStart(): void {
		$date2020 = IntervalSide::newFromDate( new ExtDate( 2020, 1, 1 ) );
		$date2021 = IntervalSide::newFromDate( new ExtDate( 2021, 1, 1 ) );

		$this->expectException( InvalidArgumentException::class );
		new Interval( $date2021, $date2020 );
	}

	public function testEndCannotEqualToStart(): void {
		$date2020 = IntervalSide::newFromDate( new ExtDate( 2020, 1, 1 ) );

		$this->expectException( InvalidArgumentException::class );
		new Interval( $date2020, $date2020 );
	}

}
