<?php

namespace EDTF\Tests\Unit;

use EDTF\UnspecifiedDigit;
use PHPUnit\Framework\TestCase;

/**
 * Class UnspecifiedTest
 *
 * @covers \EDTF\UnspecifiedDigit
 * @package EDTF\Tests\Unit
 */
class UnspecifiedDigitTest extends TestCase
{
    public function testDefaultValue()
    {
        $u = new UnspecifiedDigit();
        $this->assertTrue($u->specified('year'));
        $this->assertTrue($u->specified('month'));
        $this->assertTrue($u->specified('day'));
    }

    public function testSpecified()
    {
        $u = new UnspecifiedDigit(UnspecifiedDigit::SPECIFIED);
        $this->assertTrue($u->specified('year'));
    }

    public function testSpecifiedWithNullValue()
    {
        $u = new UnspecifiedDigit(UnspecifiedDigit::SPECIFIED);
        $this->assertTrue($u->specified());
    }

    public function testSpecifiedThrowExceptionWithInvalidPart()
    {
        $this->expectException(\InvalidArgumentException::class);
        $u = new UnspecifiedDigit(UnspecifiedDigit::SPECIFIED);
        $u->specified('invalid');
    }

    public function testUnspecified()
    {
        $u = new UnspecifiedDigit(UnspecifiedDigit::UNSPECIFIED);
        $this->assertTrue($u->unspecified('year'));
    }

    public function testUnspecifiedWithNullValue()
    {
        $u = new UnspecifiedDigit(0,UnspecifiedDigit::UNSPECIFIED, 0);
        $this->assertTrue($u->unspecified());
        $this->assertFalse($u->unspecified('year'));
        $this->assertTrue($u->unspecified('month'));
        $this->assertFalse($u->unspecified('day'));
    }

    public function testUnspecifiedThrowExceptionWithInvalidPart()
    {
        $this->expectException(\InvalidArgumentException::class);

        $u = new UnspecifiedDigit(UnspecifiedDigit::UNSPECIFIED);
        $u->unspecified('invalid');
    }
}
