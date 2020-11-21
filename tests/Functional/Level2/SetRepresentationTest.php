<?php

declare(strict_types=1);

namespace EDTF\Tests\Functional\Level2;


use EDTF\Tests\Unit\FactoryTrait;
use PHPUnit\Framework\TestCase;

/**
 * Class SetRepresentationTest
 *
 * @covers \EDTF\Parser
 * @covers \EDTF\Set
 * @package EDTF\Tests\Functional
 */
class SetRepresentationTest extends TestCase
{
    use FactoryTrait;

    public function testOneOfTheYears()
    {
        $set = $this->createSet('[1667,1668,1670..1672]');

        $lists = $set->getLists();

        $this->assertFalse($set->isAllMembers());
        $this->assertSame(1667, $lists[0]->getYear());
        $this->assertSame(1668, $lists[1]->getYear());
        $this->assertSame(1672, $lists[4]->getYear());
    }

    public function testOneOfWithEarlierDate()
    {
        $set = $this->createSet('[..1760-12-03]');
        $lists = $set->getLists();

        $this->assertFalse($set->isAllMembers());
        $this->assertTrue($set->isEarlier());
        $this->assertFalse($set->isLater());

        $this->assertSame(1760, $lists[0]->getYear());
        $this->assertSame(12, $lists[0]->getMonth());
        $this->assertSame(3, $lists[0]->getDay());
    }

    public function testOneOfWithLaterMonth()
    {
        $set = $this->createSet('[1760-12..]');
        $lists = $set->getLists();

        $this->assertFalse($set->isAllMembers());
        $this->assertFalse($set->isEarlier());
        $this->assertTrue($set->isLater());

        $this->assertSame(1760, $lists[0]->getYear());
        $this->assertSame(12, $lists[0]->getMonth());
        $this->assertNull($lists[0]->getDay());
    }

    public function testOneOfWithLaterMonthAndPrecision()
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
    }

    public function testOneOfWithYearPrecisionOrYearMonthPrecision()
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
    }

    public function testOneOfWithYearOnlyPrecisionAndEarlier()
    {
        $set = $this->createSet('[..1984]');
        $lists = $set->getLists();

        $this->assertFalse($set->isAllMembers());
        $this->assertTrue($set->isEarlier());
        $this->assertFalse($set->isLater());

        $this->assertCount(1, $lists);
        $this->assertSame(1984, $lists[0]->getYear());
    }

    public function testAllMembersWithAllOfTheYears()
    {
        $set = $this->createSet('{1667,1668,1670..1672}');
        $lists = $set->getLists();

        $this->assertTrue($set->isAllMembers());
        $this->assertFalse($set->isEarlier());
        $this->assertFalse($set->isLater());

        $this->assertCount(5, $lists);
    }

    public function testAllMembersWithYearAndYearMonthPrecision()
    {
        $set = $this->createSet('{1960,1961-12}');
        $lists = $set->getLists();

        $this->assertTrue($set->isAllMembers());
        $this->assertFalse($set->isEarlier());
        $this->assertFalse($set->isLater());

        $this->assertCount(2, $lists);
    }

    public function testAllMembersWithYearOnlyPrecisionAndEarlier()
    {
        $set = $this->createSet('{..1984}');
        $lists = $set->getLists();

        $this->assertTrue($set->isAllMembers());
        $this->assertTrue($set->isEarlier());
        $this->assertFalse($set->isLater());

        $this->assertCount(1, $lists);
    }
}