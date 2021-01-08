<?php

namespace EDTF\Tests\Unit;

use EDTF\ExtDate;
use EDTF\Qualification;
use EDTF\UnspecifiedDigit;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\ExtDate
 * @package EDTF\Tests\Unit
 */
class ExtDateTest extends TestCase
{
    use FactoryTrait;

    public function testCreate()
    {
        $q = $this->createMock(Qualification::class);
        $u = $this->createMock(UnspecifiedDigit::class);

        $d = new ExtDate('2010-10-01', 2010,10,1, $q, $u);

        $this->assertSame(2010, $d->getYear());
        $this->assertSame(10, $d->getMonth());
        $this->assertSame(1, $d->getDay());
        $this->assertSame($q, $d->getQualification());
        $this->assertSame($u, $d->getUnspecifiedDigit());
    }

    public function testShouldProvideUncertainInfo()
    {
        $q = new Qualification(Qualification::UNCERTAIN);
        $d = new ExtDate("", null,null,null, $q);

        $this->assertTrue($d->uncertain());
        $this->assertTrue($d->uncertain('year'));
        $this->assertFalse($d->uncertain('month'));
        $this->assertFalse($d->uncertain('day'));
    }

    public function testShouldProvideApproximateInfo()
    {
        $q = new Qualification(Qualification::UNDEFINED, Qualification::APPROXIMATE);
        $d = new ExtDate("", null,null,null, $q);

        $this->assertTrue($d->approximate());
        $this->assertFalse($d->approximate('year'));
        $this->assertTrue($d->approximate('month'));
        $this->assertFalse($d->approximate('day'));
    }

    public function testShouldProvideUncertainAndApproximateInfo()
    {
        $q = new Qualification(Qualification::UNDEFINED, Qualification::UNDEFINED, Qualification::UNCERTAIN_AND_APPROXIMATE);
        $d = new ExtDate("", null,null,null, $q);

        $this->assertTrue($d->uncertain() && $d->approximate());
        $this->assertFalse($d->uncertain('year'));
        $this->assertFalse($d->uncertain('month'));
        $this->assertTrue($d->uncertain('day') && $d->approximate('day'));
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
        $this->assertNull($date->getYear());
        $this->assertNull($date->getMonth());
        $this->assertNull($date->getDay());
    }
}
