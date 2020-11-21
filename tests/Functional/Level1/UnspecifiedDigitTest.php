<?php

declare(strict_types=1);

namespace EDTF\Tests\Functional\Level1;

use EDTF\Tests\Unit\FactoryTrait;
use PHPUnit\Framework\TestCase;

/**
 * Class UnspecifiedDigitTest
 *
 * @covers \EDTF\EDTF
 * @covers \EDTF\ExtDate
 * @covers \EDTF\UnspecifiedDigit
 *
 * @package EDTF\Tests\Functional
 */
class UnspecifiedDigitTest extends TestCase
{
    use FactoryTrait;

    public function testWithUnspecifiedOneOrTwoDigitYear()
    {
        $d = $this->createExtDate('201X');
        $this->assertTrue($d->unspecified());
        $this->assertTrue($d->unspecified('year'));
        $this->assertFalse($d->unspecified('month'));
        $this->assertSame(2010, $d->getYear());

        $d = $this->createExtDate('20XX');
        $this->assertTrue($d->unspecified());
        $this->assertTrue($d->unspecified('year'));
        $this->assertSame(2000, $d->getYear());
    }

    public function testWithUnspecifiedMonth()
    {
        $d = $this->createExtDate('2004-XX');
        $this->assertTrue($d->unspecified());
        $this->assertFalse($d->unspecified('year'));
        $this->assertTrue($d->unspecified('month'));

        $this->assertSame(2004, $d->getYear());
        $this->assertNull($d->getMonth());
    }

    public function testWithUnspecifiedDay()
    {
        $d = $this->createExtDate('1985-04-XX');
        $this->assertTrue($d->unspecified());
        $this->assertFalse($d->unspecified('year'));
        $this->assertFalse($d->unspecified('month'));
        $this->assertTrue($d->unspecified('day'));

        $this->assertSame(1985, $d->getYear());
        $this->assertSame(4, $d->getMonth());
        $this->assertNull($d->getDay());
    }

    public function testWithUnspecifiedMonthAndDay()
    {
        $d = $this->createExtDate('1985-XX-XX');

        $this->assertTrue($d->unspecified());
        $this->assertFalse($d->unspecified('year'));
        $this->assertTrue($d->unspecified('month'));
        $this->assertTrue($d->unspecified('day'));

        $this->assertSame(1985, $d->getYear());
        $this->assertNull($d->getMonth());
        $this->assertNull($d->getDay());
    }
}