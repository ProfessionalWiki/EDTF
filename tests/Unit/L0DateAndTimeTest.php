<?php

declare(strict_types=1);

namespace EDTF\Tests\Unit;


use EDTF\EDTF;
use EDTF\ExtDateTime;
use PHPUnit\Framework\TestCase;

/**
 * Class L0DateAndTimeTest
 * @covers \EDTF\Parser
 * @covers \EDTF\ExtDateTime
 * @package EDTF\Tests\Unit
 */
class L0DateAndTimeTest extends TestCase
{
    public function testDefaultUTC()
    {
        $testDate = "2001-02-03T09:30:01";
        $date = EDTF::from($testDate);

        $this->assertInstanceOf(ExtDateTime::class, $date);
        $this->assertEquals(2001, $date->getYear());
        $this->assertEquals(2, $date->getMonth());
        $this->assertEquals(3, $date->getDay());
        $this->assertEquals(9, $date->getHour());
        $this->assertEquals(30, $date->getMinute());
        $this->assertEquals(1, $date->getSecond());
        $this->assertEquals(0, $date->getTimezoneOffset());
    }

    public function testWithZSuffix()
    {
        $testDate = "2004-01-01T10:10:10Z";
        $date = EDTF::from($testDate);

        $this->assertInstanceOf(ExtDateTime::class, $date);
        $this->assertEquals(2004, $date->getYear());
        $this->assertEquals(1, $date->getMonth());
        $this->assertEquals(1, $date->getDay());
        $this->assertEquals(10, $date->getHour());
        $this->assertEquals(10, $date->getMinute());
        $this->assertEquals(10, $date->getSecond());
        $this->assertEquals(0, $date->getTimezoneOffset());
    }

    public function testWithSpesificTimezone()
    {
        $testDate = "2004-01-01T10:10:10+05:00";
        $date = EDTF::from($testDate);

        $this->assertInstanceOf(ExtDateTime::class, $date);
        $this->assertEquals(2004, $date->getYear());
        $this->assertEquals(1, $date->getMonth());
        $this->assertEquals(1, $date->getDay());
        $this->assertEquals(10, $date->getHour());
        $this->assertEquals(10, $date->getMinute());
        $this->assertEquals(10, $date->getSecond());
        $this->assertEquals(300, $date->getTimezoneOffset());
    }
}