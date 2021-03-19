<?php

declare(strict_types=1);

namespace EDTF\Tests\Functional\Level2;

use Carbon\Carbon;
use EDTF\Model\ExtDate;
use EDTF\Tests\Unit\FactoryTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\PackagePrivate\Parser\Parser
 * @covers \EDTF\Model\Set
 * @package EDTF\Tests\Functional
 */
class SetRepresentationTest extends TestCase
{
    use FactoryTrait;

    public function testOneOfTheYears(): void
    {
        $set = $this->createSet('[1667,1668,1670..1672]');

        $this->assertFalse($set->isAllMembers());

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

        $expectedMin = Carbon::create(1667)->getTimestamp();
        $this->assertSame($expectedMin, $set->getMin());

        $expectedMax = Carbon::create(1672, 12, 31, 23, 59, 59)->getTimestamp();
        $this->assertSame($expectedMax, $set->getMax());
    }

    public function testOneOfWithEarlierDate(): void
    {
        $set = $this->createSet('[..1760-12-03]');

        $this->assertFalse($set->isAllMembers());
        $this->assertTrue($set->hasOpenStart());
        $this->assertFalse($set->hasOpenEnd());

		$this->assertEquals(
			[
				new ExtDate( 1760, 12, 3 ),
			],
			$set->getDates()
		);

        $this->assertSame(0, $set->getMin());

        $expectedMax = Carbon::create(1760, 12, 3, 23, 59, 59)->getTimestamp();
        $this->assertSame($expectedMax, $set->getMax());
    }

    public function testOneOfWithLaterMonth(): void
    {
        $set = $this->createSet('[1760-12..]');

        $this->assertFalse($set->isAllMembers());
        $this->assertFalse($set->hasOpenStart());
        $this->assertTrue($set->hasOpenEnd());

		$this->assertEquals(
			[
				new ExtDate( 1760, 12, null ),
			],
			$set->getDates()
		);

        $expectedMin = Carbon::create(1760, 12, 1)->getTimestamp();
        $this->assertSame($expectedMin, $set->getMin());

        $this->assertSame(0, $set->getMax());
    }

    public function testOneOfWithLaterMonthAndPrecision(): void
    {
        $set = $this->createSet('[1760-01,1760-02,1760-12..]');

        $this->assertFalse($set->isAllMembers());
        $this->assertFalse($set->hasOpenStart());
        $this->assertTrue($set->hasOpenEnd());

		$this->assertEquals(
			[
				new ExtDate( 1760, 1, null ),
				new ExtDate( 1760, 2, null ),
				new ExtDate( 1760, 12, null ),
			],
			$set->getDates()
		);

        $expectedMin = Carbon::create(1760)->getTimestamp();
        $this->assertSame($expectedMin, $set->getMin());

        $this->assertSame(0, $set->getMax());
    }

    public function testOneOfWithYearPrecisionOrYearMonthPrecision(): void
    {
        $set = $this->createSet('[1667,1760-12]');

        $this->assertFalse($set->isAllMembers());
        $this->assertFalse($set->hasOpenStart());
        $this->assertFalse($set->hasOpenEnd());

		$this->assertEquals(
			[
				new ExtDate( 1667, null, null ),
				new ExtDate( 1760, 12, null ),
			],
			$set->getDates()
		);

        $expectedMin = Carbon::create(1667)->getTimestamp();
        $this->assertSame($expectedMin, $set->getMin());

        $expectedMax = Carbon::create(1760, 12, 31, 23, 59, 59)->getTimestamp();
        $this->assertSame($expectedMax, $set->getMax());
    }

    public function testOneOfWithYearOnlyPrecisionAndEarlier(): void
    {
        $set = $this->createSet('[..1984]');

        $this->assertFalse($set->isAllMembers());
        $this->assertTrue($set->hasOpenStart());
        $this->assertFalse($set->hasOpenEnd());

		$this->assertEquals(
			[
				new ExtDate( 1984, null, null ),
			],
			$set->getDates()
		);

        $this->assertSame(0, $set->getMin());

        $expectedMax = Carbon::create(1984, 12, 31, 23, 59, 59)->getTimestamp();
        $this->assertSame($expectedMax, $set->getMax());
    }

    public function testAllMembersWithAllOfTheYears(): void
    {
        $set = $this->createSet('{1667,1668,1670..1672}');
        $lists = $set->getDates();

        $this->assertTrue($set->isAllMembers());
        $this->assertFalse($set->hasOpenStart());
        $this->assertFalse($set->hasOpenEnd());

        $this->assertCount(5, $lists);

        $expectedMin = Carbon::create(1667)->getTimestamp();
        $this->assertSame($expectedMin, $set->getMin());

        $expectedMax = Carbon::create(1672, 12, 31, 23, 59, 59)->getTimestamp();
        $this->assertSame($expectedMax, $set->getMax());
    }

    public function testAllMembersWithYearAndYearMonthPrecision(): void
    {
        $set = $this->createSet('{1960,1961-12}');
        $lists = $set->getDates();

        $this->assertTrue($set->isAllMembers());
        $this->assertFalse($set->hasOpenStart());
        $this->assertFalse($set->hasOpenEnd());

        $this->assertCount(2, $lists);

        $expectedMin = Carbon::create(1960)->getTimestamp();
        $this->assertSame($expectedMin, $set->getMin());

        $expectedMax = Carbon::create(1961, 12, 31, 23, 59, 59)->getTimestamp();
        $this->assertSame($expectedMax, $set->getMax());
    }

    public function testAllMembersWithYearOnlyPrecisionAndEarlier(): void
    {
        $set = $this->createSet('{..1984}');
        $lists = $set->getDates();

        $this->assertTrue($set->isAllMembers());
        $this->assertTrue($set->hasOpenStart());
        $this->assertFalse($set->hasOpenEnd());

        $this->assertCount(1, $lists);

        $this->assertSame(0, $set->getMin());

        $expectedMax = Carbon::create(1984, 12, 31, 23, 59, 59)->getTimestamp();
        $this->assertSame($expectedMax, $set->getMax());
    }
}