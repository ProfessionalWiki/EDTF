<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Unit\Model;

use Carbon\Carbon;
use EDTF\Model\Season;
use EDTF\PackagePrivate\SaneParser;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\Model\Season
 * @package EDTF\Tests\Unit
 */
class SeasonTest extends TestCase {

	public function testCreate(): void {
		$season = new Season( 2010, 33 );
		$this->assertSame( 2010, $season->getYear() );
		$this->assertSame( 33, $season->getSeason() );
		$this->assertSame( [ 1, 2, 3 ], $season->getMonths() );
		$this->assertSame( 1, $season->getStartMonth() );
		$this->assertSame( 3, $season->getEndMonth() );
	}

	public function testSpringValues(): void {
		$this->assertSeasonValues( '2010-21', '2010-03-01', '2010-05-31' );
		$this->assertSeasonValues( '2010-25', '2010-03-01', '2010-05-31' );
		$this->assertSeasonValues( '2010-29', '2010-03-01', '2010-05-31' );
	}

	public function testSummerValues(): void {
		$this->assertSeasonValues( '2010-22', '2010-06-01', '2010-08-31' );
		$this->assertSeasonValues( '2010-26', '2010-06-01', '2010-08-31' );
		$this->assertSeasonValues( '2010-30', '2010-06-01', '2010-08-31' );
	}

	public function testAutumnValues(): void {
		$this->assertSeasonValues( '2010-23', '2010-09-01', '2010-11-30' );
		$this->assertSeasonValues( '2010-27', '2010-09-01', '2010-11-30' );
		$this->assertSeasonValues( '2010-31', '2010-09-01', '2010-11-30' );
	}

	public function testWinterValues(): void {
		$this->assertSeasonValues( '2010-24', '2010-12-01', '2010-02-28' );
		$this->assertSeasonValues( '2010-28', '2010-12-01', '2010-02-28' );
		$this->assertSeasonValues( '2010-32', '2010-12-01', '2010-02-28' );
	}

	public function testQuarterValues(): void {
		$this->assertSeasonValues( '2010-33', '2010-01-01', '2010-03-31' );
		$this->assertSeasonValues( '2010-34', '2010-04-01', '2010-06-30' );
		$this->assertSeasonValues( '2010-35', '2010-07-01', '2010-09-30' );
		$this->assertSeasonValues( '2010-36', '2010-10-01', '2010-12-31' );
	}

	public function testQuadrimesterValues(): void {
		$this->assertSeasonValues( '2010-37', '2010-01-01', '2010-04-30' );
		$this->assertSeasonValues( '2010-38', '2010-05-01', '2010-08-31' );
		$this->assertSeasonValues( '2010-39', '2010-09-01', '2010-12-31' );
	}

	public function testSemesterValues(): void {
		$this->assertSeasonValues( '2010-40', '2010-01-01', '2010-06-30' );
		$this->assertSeasonValues( '2010-41', '2010-07-01', '2010-12-31' );
	}

	private function assertSeasonValues( string $input, string $expectedStart, string $expectedEnd ): void {
		$season = ( new SaneParser() )->parse( $input )->getEdtfValue();
		$expectedStart = Carbon::parse( $expectedStart );
		$expectedEnd = Carbon::parse( $expectedEnd );

		$seasonStart = Carbon::createFromTimestamp( $season->getMin() );
		$seasonEnd = Carbon::createFromTimestamp( $season->getMax() );

		// start season validation
		$this->assertSame( $expectedStart->year, $seasonStart->year );
		$this->assertSame( $expectedStart->month, $seasonStart->month );
		$this->assertSame( $expectedStart->day, $seasonStart->day );

		// end season validation
		$this->assertSame( $expectedEnd->year, $seasonEnd->year );
		$this->assertSame( $expectedEnd->month, $seasonEnd->month );
		$this->assertSame( $expectedEnd->day, $seasonEnd->day );
	}
}
