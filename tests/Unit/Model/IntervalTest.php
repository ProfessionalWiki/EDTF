<?php

namespace EDTF\Tests\Unit\Model;

use EDTF\Model\ExtDate;
use EDTF\Model\Interval;
use EDTF\Model\IntervalSide;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\Model\Interval
 * @package EDTF\Tests\Unit
 */
class IntervalTest extends TestCase
{
    public function testCannotCreateIntervalWithTwoOpenSides(): void
    {
    	$openSide = IntervalSide::newOpenInterval();

		$this->expectException( \InvalidArgumentException::class );
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
}
