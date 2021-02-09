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

    public function testOpenEndTimeWithDayPrecision(): void
    {
        $interval = $this->createInterval("1985-04-12/..");

        $this->assertSame(1985, $interval->getStart()->getYear());
        $this->assertSame(4, $interval->getStart()->getMonth());
        $this->assertSame(12, $interval->getStart()->getDay());

        $this->assertTrue($interval->isOpenInterval());
    }

    public function testOpenEndTimeWithMonthPrecision(): void
    {
        $interval = $this->createInterval("1985-04/..");

        $this->assertSame(1985, $interval->getStart()->getYear());
        $this->assertSame(4, $interval->getStart()->getMonth());
        $this->assertNull($interval->getStart()->getDay());

        $this->assertTrue($interval->isOpenInterval());
    }

    public function testOpenEndTimeWithYearPrecision(): void
    {
        $interval = $this->createInterval("1985/..");

        $this->assertSame(1985, $interval->getStart()->getYear());
        $this->assertNull($interval->getStart()->getMonth());
        $this->assertNull($interval->getStart()->getDay());

        $this->assertTrue($interval->isOpenInterval());
    }

    public function testOpenStartTimeDayPrecision(): void
    {
        $interval = $this->createInterval("../1985-04-12");

        $this->assertTrue($interval->isOpenInterval());

        $this->assertSame(1985, $interval->getEnd()->getYear());
        $this->assertSame(4, $interval->getEnd()->getMonth());
        $this->assertSame(12, $interval->getEnd()->getDay());
    }

    public function testOpenStartTimeMonthPrecision(): void
    {
        $interval = $this->createInterval("../1985-04");

        $this->assertSame(1985, $interval->getEnd()->getYear());
        $this->assertSame(4, $interval->getEnd()->getMonth());
        $this->assertNull($interval->getEnd()->getDay());
    }

    public function testOpenStartTimeYearPrecision(): void
    {
        $interval = $this->createInterval("../1985");

        $this->assertSame(1985, $interval->getEnd()->getYear());
        $this->assertNull($interval->getEnd()->getMonth());
        $this->assertNull($interval->getEnd()->getDay());
    }

    public function testStartWithDayPrecisionAndEndWithUnknown(): void
    {
        $interval = $this->createInterval("1985-05-12/");
		$this->assertTrue($interval->isUnknownInterval());
    }

    public function testStartWithMonthPrecisionEndWithUnknown(): void
    {
        $interval = $this->createInterval("1985-05/");
		$this->assertTrue($interval->isUnknownInterval());
    }

    public function testStartWithYearPrecisionEndWithUnknown(): void
    {
        $interval = $this->createInterval("1985/");
		$this->assertTrue($interval->isUnknownInterval());
    }

    public function testWithUnknownStartAndEndWithDayPrecision(): void
    {
        $interval = $this->createInterval("/1985-04-12");
		$this->assertTrue($interval->isUnknownInterval());
    }
}
