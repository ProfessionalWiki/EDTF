<?php

declare(strict_types=1);

namespace EDTF\Tests\Functional\Level1;

use EDTF\Tests\Unit\FactoryTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\PackagePrivate\Parser
 * @covers \EDTF\Interval
 * @package EDTF\Tests\Unit
 */
class ExtendedIntervalTest extends TestCase
{
    use FactoryTrait;

    public function testOpenEndTimeWithDayPrecision()
    {
        $interval = $this->createInterval("1985-04-12/..");

        $this->assertTrue($interval->getStart()->isNormalInterval());
        $this->assertSame(1985, $interval->getStart()->getYear());
        $this->assertSame(4, $interval->getStart()->getMonth());
        $this->assertSame(12, $interval->getStart()->getDay());

        $this->assertTrue($interval->getEnd()->isOpenInterval());
    }

    public function testOpenEndTimeWithMonthPrecision()
    {
        $interval = $this->createInterval("1985-04/..");

        $this->assertTrue($interval->getStart()->isNormalInterval());
        $this->assertSame(1985, $interval->getStart()->getYear());
        $this->assertSame(4, $interval->getStart()->getMonth());
        $this->assertNull($interval->getStart()->getDay());

        $this->assertTrue($interval->getEnd()->isOpenInterval());
    }

    public function testOpenEndTimeWithYearPrecision()
    {
        $interval = $this->createInterval("1985/..");

        $this->assertTrue($interval->getStart()->isNormalInterval());
        $this->assertSame(1985, $interval->getStart()->getYear());
        $this->assertNull($interval->getStart()->getMonth());
        $this->assertNull($interval->getStart()->getDay());

        $this->assertTrue($interval->getEnd()->isOpenInterval());
    }

    public function testOpenStartTimeDayPrecision()
    {
        $interval = $this->createInterval("../1985-04-12");

        // start assertion
        $this->assertTrue($interval->getStart()->isOpenInterval());

        // end assertion
        $this->assertTrue($interval->getEnd()->isNormalInterval());
        $this->assertSame(1985, $interval->getEnd()->getYear());
        $this->assertSame(4, $interval->getEnd()->getMonth());
        $this->assertSame(12, $interval->getEnd()->getDay());
    }

    public function testOpenStartTimeMonthPrecision()
    {
        $interval = $this->createInterval("../1985-04");

        // start assertion
        $this->assertTrue($interval->getStart()->isOpenInterval());

        // end assertion
        $this->assertTrue($interval->getEnd()->isNormalInterval());
        $this->assertSame(1985, $interval->getEnd()->getYear());
        $this->assertSame(4, $interval->getEnd()->getMonth());
        $this->assertNull($interval->getEnd()->getDay());
    }

    public function testOpenStartTimeYearPrecision()
    {
        $interval = $this->createInterval("../1985");

        // start assertion
        $this->assertTrue($interval->getStart()->isOpenInterval());

        // end assertion
        $this->assertTrue($interval->getEnd()->isNormalInterval());
        $this->assertSame(1985, $interval->getEnd()->getYear());
        $this->assertNull($interval->getEnd()->getMonth());
        $this->assertNull($interval->getEnd()->getDay());
    }

    public function testStartWithDayPrecisionAndEndWithUnknown()
    {
        $interval = $this->createInterval("1985-05-12/");

        $this->assertTrue($interval->getStart()->isNormalInterval());
        $this->assertTrue($interval->getEnd()->isUnknownInterval());
    }

    public function testStartWithMonthPrecisionEndWithUnknown()
    {
        $interval = $this->createInterval("1985-05/");

        $this->assertTrue($interval->getStart()->isNormalInterval());
        $this->assertTrue($interval->getEnd()->isUnknownInterval());
    }

    public function testStartWithYearPrecisionEndWithUnknown()
    {
        $interval = $this->createInterval("1985/");

        $this->assertTrue($interval->getStart()->isNormalInterval());
        $this->assertTrue($interval->getEnd()->isUnknownInterval());
    }

    public function testWithUnknownStartAndEndWithDayPrecision()
    {
        $interval = $this->createInterval("/1985-04-12");

        $this->assertTrue($interval->getStart()->isUnknownInterval());
        $this->assertTrue($interval->getEnd()->isNormalInterval());
    }
}
