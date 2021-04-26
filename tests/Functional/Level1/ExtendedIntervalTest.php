<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Functional\Level1;

use EDTF\Tests\Unit\FactoryTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\PackagePrivate\Parser\Parser
 * @covers \EDTF\Model\Interval
 * @package EDTF\Tests\Unit
 */
class ExtendedIntervalTest extends TestCase {

	use FactoryTrait;

	public function testOpenEndTimeWithDayPrecision(): void {
		$interval = $this->createInterval( "1985-04-12/.." );

		$this->assertSame( 1985, $interval->getStartDate()->getYear() );
		$this->assertSame( 4, $interval->getStartDate()->getMonth() );
		$this->assertSame( 12, $interval->getStartDate()->getDay() );

		$this->assertTrue( $interval->isOpenInterval() );
	}

	public function testOpenEndTimeWithMonthPrecision(): void {
		$interval = $this->createInterval( "1985-04/.." );

		$this->assertSame( 1985, $interval->getStartDate()->getYear() );
		$this->assertSame( 4, $interval->getStartDate()->getMonth() );
		$this->assertNull( $interval->getStartDate()->getDay() );

		$this->assertTrue( $interval->isOpenInterval() );
	}

	public function testOpenEndTimeWithYearPrecision(): void {
		$interval = $this->createInterval( "1985/.." );

		$this->assertSame( 1985, $interval->getStartDate()->getYear() );
		$this->assertNull( $interval->getStartDate()->getMonth() );
		$this->assertNull( $interval->getStartDate()->getDay() );

		$this->assertTrue( $interval->isOpenInterval() );
	}

	public function testOpenStartTimeDayPrecision(): void {
		$interval = $this->createInterval( "../1985-04-12" );

		$this->assertTrue( $interval->isOpenInterval() );

		$this->assertSame( 1985, $interval->getEndDate()->getYear() );
		$this->assertSame( 4, $interval->getEndDate()->getMonth() );
		$this->assertSame( 12, $interval->getEndDate()->getDay() );
	}

	public function testOpenStartTimeMonthPrecision(): void {
		$interval = $this->createInterval( "../1985-04" );

		$this->assertSame( 1985, $interval->getEndDate()->getYear() );
		$this->assertSame( 4, $interval->getEndDate()->getMonth() );
		$this->assertNull( $interval->getEndDate()->getDay() );
	}

	public function testOpenStartTimeYearPrecision(): void {
		$interval = $this->createInterval( "../1985" );

		$this->assertSame( 1985, $interval->getEndDate()->getYear() );
		$this->assertNull( $interval->getEndDate()->getMonth() );
		$this->assertNull( $interval->getEndDate()->getDay() );
	}

	public function testStartWithDayPrecisionAndEndWithUnknown(): void {
		$interval = $this->createInterval( "1985-05-12/" );
		$this->assertTrue( $interval->isUnknownInterval() );
	}

	public function testStartWithMonthPrecisionEndWithUnknown(): void {
		$interval = $this->createInterval( "1985-05/" );
		$this->assertTrue( $interval->isUnknownInterval() );
	}

	public function testStartWithYearPrecisionEndWithUnknown(): void {
		$interval = $this->createInterval( "1985/" );
		$this->assertTrue( $interval->isUnknownInterval() );
	}

	public function testWithUnknownStartAndEndWithDayPrecision(): void {
		$interval = $this->createInterval( "/1985-04-12" );
		$this->assertTrue( $interval->isUnknownInterval() );
	}
}
