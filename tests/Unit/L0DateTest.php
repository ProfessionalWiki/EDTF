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
        $date = EDTF::from('2001-02-03');

        $this->assertInstanceOf(ExtDateTime::class, $date);
        $this->assertSame(2001, $date->getYear());
        $this->assertSame(2, $date->getMonth());
        $this->assertSame(3, $date->getDay());
    }

    public function testWithMonthAndYear()
    {
        /* @var \EDTF\ExtDateTime $date */
        $date = EDTF::from('2008-12');

        $this->assertInstanceOf(ExtDateTime::class, $date);
        $this->assertSame(2008, $date->getYear());
        $this->assertSame(12, $date->getMonth());
        $this->assertNull($date->getDay());
    }

    public function testWithYearOnly()
    {
        $date = EDTF::from('2008');

        $this->assertInstanceOf(ExtDateTime::class, $date);
        $this->assertSame(2008, $date->getYear());
        $this->assertNull($date->getMonth());
        $this->assertNull($date->getDay());
    }

    public function testNegativeYear()
    {
        $date = EDTF::from('-0999');

        $this->assertInstanceOf(ExtDateTime::class, $date);
        $this->assertSame(-999, $date->getYear());
        $this->assertNull($date->getMonth());
        $this->assertNull($date->getDay());
    }

    public function testWithZeroYear()
    {
        $date = EDTF::from('0000');

        $this->assertInstanceOf(ExtDateTime::class, $date);
        $this->assertSame(0, $date->getYear());
        $this->assertNull($date->getMonth());
        $this->assertNull($date->getDay());
    }

    public function testWithEmptyDate()
    {
        $date = EDTF::from("");

        $this->assertInstanceOf(ExtDateTime::class, $date);
        $this->assertNull($date->getYear());
        $this->assertNull($date->getMonth());
        $this->assertNull($date->getDay());
    }
}