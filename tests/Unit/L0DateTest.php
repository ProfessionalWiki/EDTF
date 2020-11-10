<?php

declare(strict_types=1);

namespace EDTF\Tests\Unit;


use EDTF\EDTF;
use EDTF\ExtDateTime;
use PHPUnit\Framework\TestCase;

/**
 * Class L0DateTest
 *
 * @covers \EDTF\Parser
 * @covers \EDTF\EDTF
 * @covers \EDTF\ExtDateTime
 * @package EDTF\Tests\Unit
 */
class L0DateTest extends TestCase
{
    public function testCompleteDate()
    {
        $testDate = '2001-02-03';
        $date = EDTF::from($testDate);

        $this->assertInstanceOf(ExtDateTime::class, $date);
        $this->assertEquals(2001, $date->getYear());
        $this->assertEquals(2, $date->getMonth());
        $this->assertEquals(3, $date->getDay());
    }

    public function testWithMonthAndYear()
    {
        /* @var \EDTF\ExtDateTime $date */
        $testDate = '2008-12';
        $date = EDTF::from($testDate);

        $this->assertInstanceOf(ExtDateTime::class, $date);
        $this->assertEquals(2008, $date->getYear());
        $this->assertEquals(12, $date->getMonth());
        $this->assertNull($date->getDay());
    }

    public function testWithYearOnly()
    {
        $testDate = '2008';
        $date = EDTF::from($testDate);

        $this->assertInstanceOf(ExtDateTime::class, $date);
        $this->assertEquals(2008, $date->getYear());
        $this->assertNull($date->getMonth());
        $this->assertNull($date->getDay());
    }

    public function testNegativeYear()
    {
        $testDate = '-0999';
        $date = EDTF::from($testDate);

        $this->assertInstanceOf(ExtDateTime::class, $date);
        $this->assertEquals(-999, $date->getYear());
        $this->assertNull($date->getMonth());
        $this->assertNull($date->getDay());
    }

    public function testWithZeroYear()
    {
        $testDate = '0000';
        $date = EDTF::from($testDate);

        $this->assertInstanceOf(ExtDateTime::class, $date);
        $this->assertEquals(0, $date->getYear());
        $this->assertNull($date->getMonth());
        $this->assertNull($date->getDay());
    }

    public function testWithEmptyDate()
    {
        $testDate = "";
        $date = EDTF::from($testDate);

        $this->assertInstanceOf(ExtDateTime::class, $date);
        $this->assertNull($date->getYear());
        $this->assertNull($date->getMonth());
        $this->assertNull($date->getDay());
    }
}