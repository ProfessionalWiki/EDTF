<?php

declare(strict_types=1);

namespace EDTF\Tests\Functional\Level2;

use Carbon\Carbon;
use EDTF\Tests\Unit\FactoryTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\PackagePrivate\Parser
 * @covers \EDTF\Set
 * @package EDTF\Tests\Functional
 */
class SetRepresentationTest extends TestCase
{
    use FactoryTrait;

    public function testOneOfTheYears(): void
    {
        $set = $this->createSet('[1667,1668,1670..1672]');

        $lists = $set->getLists();

        $this->assertFalse($set->isAllMembers());
        $this->assertSame(1667, $lists[0]->getYear());
        $this->assertSame(1668, $lists[1]->getYear());
        $this->assertSame(1670, $lists[2]->getYear());
        $this->assertSame(1671, $lists[3]->getYear());
        $this->assertSame(1672, $lists[4]->getYear());

        $expectedMin = Carbon::create(1667)->getTimestamp();
        $this->assertSame($expectedMin, $set->getMin());

        $expectedMax = Carbon::create(1672, 12, 31, 23, 59, 59)->getTimestamp();
        $this->assertSame($expectedMax, $set->getMax());
    }

    public function testOneOfWithEarlierDate(): void
    {
        $set = $this->createSet('[..1760-12-03]');
        $lists = $set->getLists();

        $this->assertFalse($set->isAllMembers());
        $this->assertTrue($set->isEarlier());
        $this->assertFalse($set->isLater());

        $this->assertSame(1760, $lists[0]->getYear());
        $this->assertSame(12, $lists[0]->getMonth());
        $this->assertSame(3, $lists[0]->getDay());

        $this->assertSame(0, $set->getMin());

        $expectedMax = Carbon::create(1760, 12, 3, 23, 59, 59)->getTimestamp();
        $this->assertSame($expectedMax, $set->getMax());
    }

    public function testOneOfWithLaterMonth(): void
    {
        $set = $this->createSet('[1760-12..]');
        $lists = $set->getLists();

        $this->assertFalse($set->isAllMembers());
        $this->assertFalse($set->isEarlier());
        $this->assertTrue($set->isLater());

        $this->assertSame(1760, $lists[0]->getYear());
        $this->assertSame(12, $lists[0]->getMonth());
        $this->assertNull($lists[0]->getDay());

        $expectedMin = Carbon::create(1760, 12, 1)->getTimestamp();
        $this->assertSame($expectedMin, $set->getMin());

        $this->assertSame(0, $set->getMax());
    }

    public function testOneOfWithLaterMonthAndPrecision(): void
    {
        $set = $this->createSet('[1760-01,1760-02,1760-12..]');
        $lists = $set->getLists();

        $this->assertFalse($set->isAllMembers());
        $this->assertFalse($set->isEarlier());
        $this->assertTrue($set->isLater());

        $this->assertCount(3, $lists);
        $this->assertSame(1, $lists[0]->getMonth());
        $this->assertSame(2, $lists[1]->getMonth());
        $this->assertSame(12, $lists[2]->getMonth());

        $expectedMin = Carbon::create(1760)->getTimestamp();
        $this->assertSame($expectedMin, $set->getMin());

        $this->assertSame(0, $set->getMax());
    }

    public function testOneOfWithYearPrecisionOrYearMonthPrecision(): void
    {
        $set = $this->createSet('[1667,1760-12]');
        $lists = $set->getLists();

        $this->assertFalse($set->isAllMembers());
        $this->assertFalse($set->isEarlier());
        $this->assertFalse($set->isLater());

        $this->assertCount(2, $lists);
        $this->assertSame(1667, $lists[0]->getYear());
        $this->assertSame(1760, $lists[1]->getYear());
        $this->assertSame(12, $lists[1]->getMonth());

        $expectedMin = Carbon::create(1667)->getTimestamp();
        $this->assertSame($expectedMin, $set->getMin());

        $expectedMax = Carbon::create(1760, 12, 31, 23, 59, 59)->getTimestamp();
        $this->assertSame($expectedMax, $set->getMax());
    }

    public function testOneOfWithYearOnlyPrecisionAndEarlier(): void
    {
        $set = $this->createSet('[..1984]');
        $lists = $set->getLists();

        $this->assertFalse($set->isAllMembers());
        $this->assertTrue($set->isEarlier());
        $this->assertFalse($set->isLater());

        $this->assertCount(1, $lists);
        $this->assertSame(1984, $lists[0]->getYear());

        $this->assertSame(0, $set->getMin());

        $expectedMax = Carbon::create(1984, 12, 31, 23, 59, 59)->getTimestamp();
        $this->assertSame($expectedMax, $set->getMax());
    }

    public function testAllMembersWithAllOfTheYears(): void
    {
        $set = $this->createSet('{1667,1668,1670..1672}');
        $lists = $set->getLists();

        $this->assertTrue($set->isAllMembers());
        $this->assertFalse($set->isEarlier());
        $this->assertFalse($set->isLater());

        $this->assertCount(5, $lists);

        $expectedMin = Carbon::create(1667)->getTimestamp();
        $this->assertSame($expectedMin, $set->getMin());

        $expectedMax = Carbon::create(1672, 12, 31, 23, 59, 59)->getTimestamp();
        $this->assertSame($expectedMax, $set->getMax());
    }

    public function testAllMembersWithYearAndYearMonthPrecision(): void
    {
        $set = $this->createSet('{1960,1961-12}');
        $lists = $set->getLists();

        $this->assertTrue($set->isAllMembers());
        $this->assertFalse($set->isEarlier());
        $this->assertFalse($set->isLater());

        $this->assertCount(2, $lists);

        $expectedMin = Carbon::create(1960)->getTimestamp();
        $this->assertSame($expectedMin, $set->getMin());

        $expectedMax = Carbon::create(1961, 12, 31, 23, 59, 59)->getTimestamp();
        $this->assertSame($expectedMax, $set->getMax());
    }

    public function testAllMembersWithYearOnlyPrecisionAndEarlier(): void
    {
        $set = $this->createSet('{..1984}');
        $lists = $set->getLists();

        $this->assertTrue($set->isAllMembers());
        $this->assertTrue($set->isEarlier());
        $this->assertFalse($set->isLater());

        $this->assertCount(1, $lists);

        $this->assertSame(0, $set->getMin());

        $expectedMax = Carbon::create(1984, 12, 31, 23, 59, 59)->getTimestamp();
        $this->assertSame($expectedMax, $set->getMax());
    }
}