<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Functional\Level2;

use Carbon\Carbon;
use EDTF\Model\ExtDate;
use EDTF\Tests\Unit\FactoryTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\PackagePrivate\Parser\Parser
 * @covers \EDTF\Model\Set
 * @covers \EDTF\Model\SetElement\SingleDateSetElement
 * @covers \EDTF\Model\SetElement\RangeSetElement
 * @covers \EDTF\Model\SetElement\OpenSetElement
 * @package EDTF\Tests\Functional
 */
class SetRepresentationTest extends TestCase {

	use FactoryTrait;

	public function testOneOfTheYears(): void {
		$set = $this->createSet( '[1667,1668,1670..1672]' );

		$this->assertFalse( $set->isAllMembers() );

		$this->assertEquals(
			[
				new ExtDate( 1667 ),
				new ExtDate( 1668 ),
				new ExtDate( 1670 ),
				new ExtDate( 1671 ),
				new ExtDate( 1672 ),
			],
			$set->getDates()
		);

		$expectedMin = Carbon::create( 1667 )->getTimestamp();
		$this->assertSame( $expectedMin, $set->getMin() );

		$expectedMax = Carbon::create( 1672, 12, 31, 23, 59, 59 )->getTimestamp();
		$this->assertSame( $expectedMax, $set->getMax() );
	}

	public function testOneOfWithEarlierDate(): void {
		$set = $this->createSet( '[..1760-12-03]' );

		$this->assertFalse( $set->isAllMembers() );

		$this->assertEquals(
			[
				new ExtDate( 1760, 12, 3 ),
			],
			$set->getDates()
		);

		$this->assertSame( 0, $set->getMin() );

		$expectedMax = Carbon::create( 1760, 12, 3, 23, 59, 59 )->getTimestamp();
		$this->assertSame( $expectedMax, $set->getMax() );
	}

	public function testOneOfWithLaterMonth(): void {
		$set = $this->createSet( '[1760-12..]' );

		$this->assertFalse( $set->isAllMembers() );

		$this->assertEquals(
			[
				new ExtDate( 1760, 12, null ),
			],
			$set->getDates()
		);

		$expectedMin = Carbon::create( 1760, 12, 1 )->getTimestamp();
		$this->assertSame( $expectedMin, $set->getMin() );

		$this->assertSame( 0, $set->getMax() );
	}

	public function testOneOfWithLaterMonthAndPrecision(): void {
		$set = $this->createSet( '[1760-01,1760-02,1760-12..]' );

		$this->assertFalse( $set->isAllMembers() );

		$this->assertEquals(
			[
				new ExtDate( 1760, 1, null ),
				new ExtDate( 1760, 2, null ),
				new ExtDate( 1760, 12, null ),
			],
			$set->getDates()
		);

		$expectedMin = Carbon::create( 1760 )->getTimestamp();
		$this->assertSame( $expectedMin, $set->getMin() );

		$this->assertSame( 0, $set->getMax() );
	}

	public function testOneOfWithYearPrecisionOrYearMonthPrecision(): void {
		$set = $this->createSet( '[1667,1760-12]' );

		$this->assertFalse( $set->isAllMembers() );

		$this->assertEquals(
			[
				new ExtDate( 1667, null, null ),
				new ExtDate( 1760, 12, null ),
			],
			$set->getDates()
		);

		$expectedMin = Carbon::create( 1667 )->getTimestamp();
		$this->assertSame( $expectedMin, $set->getMin() );

		$expectedMax = Carbon::create( 1760, 12, 31, 23, 59, 59 )->getTimestamp();
		$this->assertSame( $expectedMax, $set->getMax() );
	}

	public function testOneOfWithYearOnlyPrecisionAndEarlier(): void {
		$set = $this->createSet( '[..1984]' );

		$this->assertFalse( $set->isAllMembers() );

		$this->assertEquals(
			[
				new ExtDate( 1984, null, null ),
			],
			$set->getDates()
		);

		$this->assertSame( 0, $set->getMin() );

		$expectedMax = Carbon::create( 1984, 12, 31, 23, 59, 59 )->getTimestamp();
		$this->assertSame( $expectedMax, $set->getMax() );
	}

	public function testAllMembersWithAllOfTheYears(): void {
		$set = $this->createSet( '{1667,1668,1670..1672}' );
		$lists = $set->getDates();

		$this->assertTrue( $set->isAllMembers() );

		$this->assertCount( 5, $lists );

		$expectedMin = Carbon::create( 1667 )->getTimestamp();
		$this->assertSame( $expectedMin, $set->getMin() );

		$expectedMax = Carbon::create( 1672, 12, 31, 23, 59, 59 )->getTimestamp();
		$this->assertSame( $expectedMax, $set->getMax() );
	}

	public function testAllMembersWithYearAndYearMonthPrecision(): void {
		$set = $this->createSet( '{1960,1961-12}' );
		$lists = $set->getDates();

		$this->assertTrue( $set->isAllMembers() );

		$this->assertCount( 2, $lists );

		$expectedMin = Carbon::create( 1960 )->getTimestamp();
		$this->assertSame( $expectedMin, $set->getMin() );

		$expectedMax = Carbon::create( 1961, 12, 31, 23, 59, 59 )->getTimestamp();
		$this->assertSame( $expectedMax, $set->getMax() );
	}

	public function testAllMembersWithYearOnlyPrecisionAndEarlier(): void {
		$set = $this->createSet( '{..1984}' );
		$lists = $set->getDates();

		$this->assertTrue( $set->isAllMembers() );

		$this->assertCount( 1, $lists );

		$this->assertSame( 0, $set->getMin() );

		$expectedMax = Carbon::create( 1984, 12, 31, 23, 59, 59 )->getTimestamp();
		$this->assertSame( $expectedMax, $set->getMax() );
	}

	public function testOpenMiddleWithMonthsPrecisionWithinAYear(): void {
		$this->assertEquals(
			[
				new ExtDate( 2020, 1, null ),
				new ExtDate( 2020, 2, null ),
				new ExtDate( 2020, 3, null ),
				new ExtDate( 2020, 4, null ),
			],
			$this->createSet( '{2020-01..2020-04}' )->getDates()
		);
	}

	public function testOpenMiddleWithMonthsPrecisionCrossingOneYear(): void {
		$this->assertEquals(
			[
				new ExtDate( 2020, 9, null ),
				new ExtDate( 2020, 10, null ),
				new ExtDate( 2020, 11, null ),
				new ExtDate( 2020, 12, null ),
				new ExtDate( 2021, 1, null ),
				new ExtDate( 2021, 2, null ),
			],
			$this->createSet( '{2020-09..2021-02}' )->getDates()
		);
	}

	public function testOpenMiddleWithMonthsPrecisionCrossingTwoYears(): void {
		$this->assertEquals(
			[
				new ExtDate( 2020, 9, null ),
				new ExtDate( 2020, 10, null ),
				new ExtDate( 2020, 11, null ),
				new ExtDate( 2020, 12, null ),
				new ExtDate( 2021, 1, null ),
				new ExtDate( 2021, 2, null ),
				new ExtDate( 2021, 3, null ),
				new ExtDate( 2021, 4, null ),
				new ExtDate( 2021, 5, null ),
				new ExtDate( 2021, 6, null ),
				new ExtDate( 2021, 7, null ),
				new ExtDate( 2021, 8, null ),
				new ExtDate( 2021, 9, null ),
				new ExtDate( 2021, 10, null ),
				new ExtDate( 2021, 11, null ),
				new ExtDate( 2021, 12, null ),
				new ExtDate( 2022, 1, null ),
				new ExtDate( 2022, 2, null ),
			],
			$this->createSet( '{2020-09..2022-02}' )->getDates()
		);
	}

	public function testOpenMiddleWithDayPrecisionWithinAMonth(): void {
		$this->assertEquals(
			[
				new ExtDate( 2020, 1, 3 ),
				new ExtDate( 2020, 1, 4 ),
				new ExtDate( 2020, 1, 5 ),
			],
			$this->createSet( '{2020-01-03..2020-01-05}' )->getDates()
		);
	}

	public function testOpenMiddleWithDayPrecisionCrossingMonthWithinAYear(): void {
		$this->assertEquals(
			[
				new ExtDate( 2020, 1, 31 ),
				new ExtDate( 2020, 2, 1 ),
				new ExtDate( 2020, 2, 2 ),
			],
			$this->createSet( '{2020-01-31..2020-02-02}' )->getDates()
		);
	}

	public function testOpenMiddleWithDayPrecisionCrossingOneYear(): void {
		$set = $this->createSet( '{1985-01-01..1986-01-01}' );
		$this->assertCount(
			366,
			$set->getDates()
		);
		$this->assertEquals(
			new ExtDate( 1985, 1, 1 ),
			$set->getDates()[0]
		);
		$this->assertEquals(
			new ExtDate( 1986, 1, 1 ),
			$set->getDates()[365]
		);
	}

	public function testOpenMiddleWithDayPrecisionCrossingOneYearAndMonth(): void {
		$set = $this->createSet( '{1987-01-01..1988-02-25}' );
		$this->assertCount(
			421,
			$set->getDates()
		);
		$this->assertEquals(
			new ExtDate( 1987, 1, 1 ),
			$set->getDates()[0]
		);
		$this->assertEquals(
			new ExtDate( 1988, 2, 25 ),
			$set->getDates()[420]
		);
	}

	public function testOpenMiddleWithDayPrecisionCrossingOneMultipleYears(): void {
		$set = $this->createSet( '{2001-01-02..2003-01-02}' );
		$this->assertCount(
			731,
			$set->getDates()
		);
		$this->assertEquals(
			new ExtDate( 2001, 1, 2 ),
			$set->getDates()[0]
		);
		$this->assertEquals(
			new ExtDate( 2003, 1, 2 ),
			$set->getDates()[730]
		);
	}
}