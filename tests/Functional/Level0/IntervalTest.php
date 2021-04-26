<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Functional\Level0;

use EDTF\Model\ExtDate;
use EDTF\Model\Interval;
use EDTF\Model\Season;
use EDTF\Tests\Unit\FactoryTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\Model\Interval
 * @covers \EDTF\PackagePrivate\Parser\Parser
 * @package EDTF\Tests\Unit
 */
class IntervalTest extends TestCase {

	use FactoryTrait;

	public function testWithYearPrecision(): void {
		$interval = $this->createInterval( '1964/2008' );

		$this->assertInstanceOf( Interval::class, $interval );
		$this->assertInstanceOf( ExtDate::class, $interval->getEndDate() );
		$this->assertInstanceOf( ExtDate::class, $interval->getStartDate() );

		$this->assertSame( 1964, $interval->getStartDate()->getYear() );
		$this->assertSame( 2008, $interval->getEndDate()->getYear() );
	}

	public function testWithMonthPrecision(): void {
		$interval = $this->createInterval( "2004-06/2006-08" );

		$this->assertInstanceOf( Interval::class, $interval );
		$this->assertInstanceOf( ExtDate::class, $interval->getEndDate() );
		$this->assertInstanceOf( ExtDate::class, $interval->getStartDate() );

		// lower tests
		$this->assertSame( 2004, $interval->getStartDate()->getYear() );
		$this->assertSame( 6, $interval->getStartDate()->getMonth() );

		// upper tests
		$this->assertSame( 2006, $interval->getEndDate()->getYear() );
		$this->assertSame( 8, $interval->getEndDate()->getMonth() );
	}

	public function testWithSeasonPrecision(): void {
		$interval = $this->createInterval( '2010-21/2012-26' );

		$this->assertInstanceOf( Interval::class, $interval );
		$this->assertInstanceOf( Season::class, $interval->getStartDate() );
		$this->assertInstanceOf( Season::class, $interval->getEndDate() );

		$this->assertSame( 2010, $interval->getStartDate()->getYear() );
		$this->assertSame( 21, $interval->getStartDate()->getSeason() );
		$this->assertSame( 2012, $interval->getEndDate()->getYear() );
		$this->assertSame( 26, $interval->getEndDate()->getSeason() );
	}

	public function testWithDayPrecision(): void {
		$interval = $this->createInterval( "2004-02-01/2005-02-08" );

		$this->assertInstanceOf( Interval::class, $interval );
		$this->assertInstanceOf( ExtDate::class, $interval->getEndDate() );
		$this->assertInstanceOf( ExtDate::class, $interval->getStartDate() );

		// lower tests
		$this->assertSame( 2004, $interval->getStartDate()->getYear() );
		$this->assertSame( 2, $interval->getStartDate()->getMonth() );
		$this->assertSame( 1, $interval->getStartDate()->getDay() );

		// upper tests
		$this->assertSame( 2005, $interval->getEndDate()->getYear() );
		$this->assertSame( 2, $interval->getEndDate()->getMonth() );
		$this->assertSame( 8, $interval->getEndDate()->getDay() );
	}

	public function testStartWithDayPrecisionAndEndWithMonthPrecision(): void {
		$interval = $this->createInterval( "2004-02-01/2005-02" );

		$this->assertInstanceOf( Interval::class, $interval );
		$this->assertInstanceOf( ExtDate::class, $interval->getEndDate() );
		$this->assertInstanceOf( ExtDate::class, $interval->getStartDate() );

		// lower tests
		$this->assertSame( 2004, $interval->getStartDate()->getYear() );
		$this->assertSame( 2, $interval->getStartDate()->getMonth() );
		$this->assertSame( 1, $interval->getStartDate()->getDay() );

		// upper tests
		$this->assertSame( 2005, $interval->getEndDate()->getYear() );
		$this->assertSame( 2, $interval->getEndDate()->getMonth() );
		$this->assertNull( $interval->getEndDate()->getDay() );
	}

	public function testStartWithDayPrecisionAndEndWithYearPrecision(): void {
		$interval = $this->createInterval( "2004-02-01/2005" );

		$this->assertInstanceOf( Interval::class, $interval );
		$this->assertInstanceOf( ExtDate::class, $interval->getEndDate() );
		$this->assertInstanceOf( ExtDate::class, $interval->getStartDate() );

		// lower tests
		$this->assertSame( 2004, $interval->getStartDate()->getYear() );
		$this->assertSame( 2, $interval->getStartDate()->getMonth() );
		$this->assertSame( 1, $interval->getStartDate()->getDay() );

		// upper tests
		$this->assertSame( 2005, $interval->getEndDate()->getYear() );
		$this->assertNull( $interval->getEndDate()->getMonth() );
		$this->assertNull( $interval->getEndDate()->getDay() );
	}

	public function testStartWithYearPrecisionEndWithMonthPrecision(): void {
		$interval = $this->createInterval( "2005/2006-02" );

		$this->assertInstanceOf( Interval::class, $interval );
		$this->assertInstanceOf( ExtDate::class, $interval->getEndDate() );
		$this->assertInstanceOf( ExtDate::class, $interval->getStartDate() );

		// lower tests
		$this->assertSame( 2005, $interval->getStartDate()->getYear() );
		$this->assertNull( $interval->getStartDate()->getMonth() );
		$this->assertNull( $interval->getStartDate()->getDay() );

		// upper tests
		$this->assertSame( 2006, $interval->getEndDate()->getYear() );
		$this->assertSame( 2, $interval->getEndDate()->getMonth() );
		$this->assertNull( $interval->getEndDate()->getDay() );
	}
}