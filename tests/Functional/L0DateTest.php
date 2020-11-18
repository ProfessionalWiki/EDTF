<?php

declare(strict_types=1);

namespace EDTF\Tests\Functional;


use EDTF\ExtDate;
use EDTF\Tests\Unit\FactoryTrait;
use PHPUnit\Framework\TestCase;

/**
 * Class L0DateTest
 *
 * @covers \EDTF\Parser
 * @covers \EDTF\EDTF
 * @covers \EDTF\ExtDate
 * @package EDTF\Tests\Unit
 */
class L0DateTest extends TestCase
{
    use FactoryTrait;

    public function testCompleteDate()
    {
        $date = $this->createExtDate('2001-02-03');

        $this->assertInstanceOf(ExtDate::class, $date);
        $this->assertSame(2001, $date->getYear());
        $this->assertSame(2, $date->getMonth());
        $this->assertSame(3, $date->getDay());
    }

    public function testWithMonthAndYear()
    {
        $date = $this->createExtDate('2008-12');

        $this->assertInstanceOf(ExtDate::class, $date);
        $this->assertSame(2008, $date->getYear());
        $this->assertSame(12, $date->getMonth());
        $this->assertNull($date->getDay());
    }

    public function testWithYearOnly()
    {
        $date = $this->createExtDate('2008');

        $this->assertInstanceOf(ExtDate::class, $date);
        $this->assertSame(2008, $date->getYear());
        $this->assertNull($date->getMonth());
        $this->assertNull($date->getDay());
    }

    public function testNegativeYear()
    {
        $date = $this->createExtDate('-0999');

        $this->assertInstanceOf(ExtDate::class, $date);
        $this->assertSame(-999, $date->getYear());
        $this->assertNull($date->getMonth());
        $this->assertNull($date->getDay());
    }

    public function testWithZeroYear()
    {
        $date = $this->createExtDate('0000');

        $this->assertInstanceOf(ExtDate::class, $date);
        $this->assertSame(0, $date->getYear());
        $this->assertNull($date->getMonth());
        $this->assertNull($date->getDay());
    }

    public function testWithEmptyDate()
    {
        $date = $this->createExtDate("");

        $this->assertInstanceOf(ExtDate::class, $date);
        $this->assertNull($date->getYear());
        $this->assertNull($date->getMonth());
        $this->assertNull($date->getDay());
    }
}