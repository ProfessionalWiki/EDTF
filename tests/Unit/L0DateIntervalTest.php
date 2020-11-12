<?php

declare(strict_types=1);

namespace EDTF\Tests\Unit;


use EDTF\EDTF;
use EDTF\ExtDateTime;
use EDTF\Interval;
use PHPUnit\Framework\TestCase;

/**
 * Class L0DateIntervalTest
 *
 * @covers \EDTF\Interval
 * @covers \EDTF\Parser
 * @package EDTF\Tests\Unit
 */
class L0DateIntervalTest extends TestCase
{
    public function testWithYearOnly()
    {
        /* @var \EDTF\Interval $interval */
        $test = "1964/2008";
        $interval = EDTF::from($test);

        $this->assertInstanceOf(Interval::class, $interval);
        $this->assertInstanceOf(ExtDateTime::class, $interval->getUpper());
        $this->assertInstanceOf(ExtDateTime::class, $interval->getLower());

        $this->assertSame(1964, $interval->getLower()->getYear());
        $this->assertSame(2008, $interval->getUpper()->getYear());
    }

    public function testWithYearAndMonth()
    {
        /* @var \EDTF\Interval $interval */
        $test = "2004-06/2006-08";
        $interval = EDTF::from($test);

        $this->assertInstanceOf(Interval::class, $interval);
        $this->assertInstanceOf(ExtDateTime::class, $interval->getUpper());
        $this->assertInstanceOf(ExtDateTime::class, $interval->getLower());

        // lower tests
        $this->assertSame(2004, $interval->getLower()->getYear());
        $this->assertSame(6, $interval->getLower()->getMonth());

        // upper tests
        $this->assertSame(2006, $interval->getUpper()->getYear());
        $this->assertSame(8, $interval->getUpper()->getMonth());
    }

    public function testWithCompleteDate()
    {
        /* @var \EDTF\Interval $interval */
        $test = "2004-02-01/2005-02-08";
        $interval = EDTF::from($test);

        $this->assertInstanceOf(Interval::class, $interval);
        $this->assertInstanceOf(ExtDateTime::class, $interval->getUpper());
        $this->assertInstanceOf(ExtDateTime::class, $interval->getLower());

        // lower tests
        $this->assertSame(2004, $interval->getLower()->getYear());
        $this->assertSame(2, $interval->getLower()->getMonth());
        $this->assertSame(1, $interval->getLower()->getDay());

        // upper tests
        $this->assertSame(2005, $interval->getUpper()->getYear());
        $this->assertSame(2, $interval->getUpper()->getMonth());
        $this->assertSame(8, $interval->getUpper()->getDay());
    }

    public function testCustom1()
    {
        /* @var \EDTF\Interval $interval */
        $test = "2004-02-01/2005-02";
        $interval = EDTF::from($test);

        $this->assertInstanceOf(Interval::class, $interval);
        $this->assertInstanceOf(ExtDateTime::class, $interval->getUpper());
        $this->assertInstanceOf(ExtDateTime::class, $interval->getLower());

        // lower tests
        $this->assertSame(2004, $interval->getLower()->getYear());
        $this->assertSame(2, $interval->getLower()->getMonth());
        $this->assertSame(1, $interval->getLower()->getDay());

        // upper tests
        $this->assertSame(2005, $interval->getUpper()->getYear());
        $this->assertSame(2, $interval->getUpper()->getMonth());
        $this->assertNull($interval->getUpper()->getDay());
    }

    public function testCustom2()
    {
        /* @var \EDTF\Interval $interval */
        $test = "2005/2006-02";
        $interval = EDTF::from($test);

        $this->assertInstanceOf(Interval::class, $interval);
        $this->assertInstanceOf(ExtDateTime::class, $interval->getUpper());
        $this->assertInstanceOf(ExtDateTime::class, $interval->getLower());

        // lower tests
        $this->assertSame(2005, $interval->getLower()->getYear());
        $this->assertNull($interval->getLower()->getMonth());
        $this->assertNull($interval->getLower()->getDay());

        // upper tests
        $this->assertSame(2006, $interval->getUpper()->getYear());
        $this->assertSame(2, $interval->getUpper()->getMonth());
        $this->assertNull($interval->getUpper()->getDay());
    }
}