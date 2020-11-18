<?php

declare(strict_types=1);

namespace EDTF\Tests\Functional;


use EDTF\EDTF;
use EDTF\ExtDate;
use EDTF\ExtDateTime;
use EDTF\Interval;
use EDTF\Tests\Unit\FactoryTrait;
use PHPUnit\Framework\TestCase;

/**
 * Class L0DateIntervalTest
 *
 * @covers \EDTF\Interval
 * @covers \EDTF\Parser
 * @package EDTF\Tests\Unit
 */
class L0IntervalTest extends TestCase
{
    use FactoryTrait;

    public function testWithYearOnly()
    {
        $interval = $this->createInterval('1964/2008');

        $this->assertInstanceOf(Interval::class, $interval);
        $this->assertInstanceOf(ExtDate::class, $interval->getEnd());
        $this->assertInstanceOf(ExtDate::class, $interval->getStart());

        $this->assertSame(1964, $interval->getStart()->getYear());
        $this->assertSame(2008, $interval->getEnd()->getYear());
    }

    public function testWithYearAndMonth()
    {
        $interval = $this->createInterval("2004-06/2006-08");

        $this->assertInstanceOf(Interval::class, $interval);
        $this->assertInstanceOf(ExtDate::class, $interval->getEnd());
        $this->assertInstanceOf(ExtDate::class, $interval->getStart());

        // lower tests
        $this->assertSame(2004, $interval->getStart()->getYear());
        $this->assertSame(6, $interval->getStart()->getMonth());

        // upper tests
        $this->assertSame(2006, $interval->getEnd()->getYear());
        $this->assertSame(8, $interval->getEnd()->getMonth());
    }

    public function testWithCompleteDate()
    {
        $interval = $this->createInterval("2004-02-01/2005-02-08");

        $this->assertInstanceOf(Interval::class, $interval);
        $this->assertInstanceOf(ExtDate::class, $interval->getEnd());
        $this->assertInstanceOf(ExtDate::class, $interval->getStart());

        // lower tests
        $this->assertSame(2004, $interval->getStart()->getYear());
        $this->assertSame(2, $interval->getStart()->getMonth());
        $this->assertSame(1, $interval->getStart()->getDay());

        // upper tests
        $this->assertSame(2005, $interval->getEnd()->getYear());
        $this->assertSame(2, $interval->getEnd()->getMonth());
        $this->assertSame(8, $interval->getEnd()->getDay());
    }

    public function testCustom1()
    {
        $interval = $this->createInterval("2004-02-01/2005-02");

        $this->assertInstanceOf(Interval::class, $interval);
        $this->assertInstanceOf(ExtDate::class, $interval->getEnd());
        $this->assertInstanceOf(ExtDate::class, $interval->getStart());

        // lower tests
        $this->assertSame(2004, $interval->getStart()->getYear());
        $this->assertSame(2, $interval->getStart()->getMonth());
        $this->assertSame(1, $interval->getStart()->getDay());

        // upper tests
        $this->assertSame(2005, $interval->getEnd()->getYear());
        $this->assertSame(2, $interval->getEnd()->getMonth());
        $this->assertNull($interval->getEnd()->getDay());
    }

    public function testCustom2()
    {
        $interval = $this->createInterval("2005/2006-02");

        $this->assertInstanceOf(Interval::class, $interval);
        $this->assertInstanceOf(ExtDate::class, $interval->getEnd());
        $this->assertInstanceOf(ExtDate::class, $interval->getStart());

        // lower tests
        $this->assertSame(2005, $interval->getStart()->getYear());
        $this->assertNull($interval->getStart()->getMonth());
        $this->assertNull($interval->getStart()->getDay());

        // upper tests
        $this->assertSame(2006, $interval->getEnd()->getYear());
        $this->assertSame(2, $interval->getEnd()->getMonth());
        $this->assertNull($interval->getEnd()->getDay());
    }
}