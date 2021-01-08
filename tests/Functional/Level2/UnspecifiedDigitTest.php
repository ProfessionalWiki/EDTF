<?php

declare(strict_types=1);

namespace EDTF\Tests\Functional\Level2;


use EDTF\Tests\Unit\FactoryTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\PackagePrivate\Parser
 * @covers \EDTF\ExtDate
 * @package EDTF\Tests\Functional
 */
class UnspecifiedDigitTest extends TestCase
{
    use FactoryTrait;

    public function testWithOneDigitYearOnly()
    {
        $d = $this->createExtDate('156X-12-25');

        $this->assertTrue($d->unspecified());
        $this->assertTrue($d->unspecified('year'));
        $this->assertFalse($d->unspecified('month'));
        $this->assertFalse($d->unspecified('day'));

        $this->assertSame(1560, $d->getYear());
    }

    public function testWithTwoDigitYear()
    {
        $d = $this->createExtDate('15XX-12-25');

        $this->assertTrue($d->unspecified());
        $this->assertTrue($d->unspecified('year'));
        $this->assertFalse($d->unspecified('month'));
        $this->assertFalse($d->unspecified('day'));

        $this->assertSame(1500, $d->getYear());
    }

    public function testWithUnspecifiedYearAndDay()
    {
        $d = $this->createExtDate('XXXX-12-XX');

        $this->assertTrue($d->unspecified());
        $this->assertTrue($d->unspecified('year'));
        $this->assertFalse($d->unspecified('month'));
        $this->assertTrue($d->unspecified('day'));

        $this->assertNull($d->getYear());
    }

    public function testWithThreeDigitYearAndTwoDigitMonth()
    {
        $d = $this->createExtDate('1XXX-XX');

        $this->assertTrue($d->unspecified());
        $this->assertTrue($d->unspecified('year'));
        $this->assertTrue($d->unspecified('month'));
        $this->assertFalse($d->unspecified('day'));

        $this->assertSame(1000, $d->getYear());
        $this->assertNull($d->getMonth());
    }

    public function testWithThreeDigitYearOnly()
    {
        $d = $this->createExtDate('1XXX-12');

        $this->assertTrue($d->unspecified());
        $this->assertTrue($d->unspecified('year'));
        $this->assertFalse($d->unspecified('month'));
        $this->assertFalse($d->unspecified('day'));

        $this->assertSame(1000, $d->getYear());
        $this->assertSame(12, $d->getMonth());
    }

    public function testWithOneDigitMonth()
    {
        $d = $this->createExtDate('1984-1X');

        $this->assertTrue($d->unspecified());
        $this->assertFalse($d->unspecified('year'));
        $this->assertTrue($d->unspecified('month'));
        $this->assertFalse($d->unspecified('day'));

        $this->assertSame(1984, $d->getYear());
        $this->assertSame(10, $d->getMonth());
    }

}