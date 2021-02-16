<?php

declare(strict_types=1);

namespace EDTF\Tests\Functional\Level2;


use EDTF\Tests\Unit\FactoryTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\PackagePrivate\Parser\Parser
 * @covers \EDTF\ExtDate
 * @package EDTF\Tests\Functional
 */
class QualificationTest extends TestCase
{
    use FactoryTrait;

    public function testYearMonthDayUncertainAndApproximate()
    {
        $d = $this->createExtDate('2004-06-11%');

        $this->assertTrue($d->uncertain());
        $this->assertTrue($d->approximate());

        $this->assertTrue($d->uncertain('year'));
        $this->assertTrue($d->uncertain('month'));
        $this->assertTrue($d->uncertain('day'));
    }

    public function testYearAndMonthApproximate()
    {
        $d = $this->createExtDate('2004-06~-11');

        $this->assertTrue($d->approximate());
        $this->assertTrue($d->approximate('year'));
        $this->assertTrue($d->approximate('month'));
        $this->assertFalse($d->approximate('day'));
    }

    public function testYearUncertain()
    {
        $d = $this->createExtDate('2004?-06-11');

        $this->assertTrue($d->uncertain());
        $this->assertTrue($d->uncertain('year'));
        $this->assertFalse($d->uncertain('month'));
        $this->assertFalse($d->uncertain('day'));
    }

    public function testIndividualComponentWithYearAndDay()
    {
        $d = $this->createExtDate('?2004-06-~11');

        $this->assertTrue($d->uncertain() && $d->approximate());
        $this->assertTrue($d->uncertain('year'));
        $this->assertFalse($d->uncertain('month'));
        $this->assertTrue($d->approximate('day'));
    }

    public function testIndividualComponentWithMonth()
    {
        $d = $this->createExtDate('2004-%06-11');

        $this->assertTrue($d->uncertain() && $d->approximate());
        $this->assertFalse($d->uncertain('year'));
        $this->assertTrue($d->uncertain('month') && $d->approximate('month'));
        $this->assertFalse($d->uncertain('day'));
    }
}