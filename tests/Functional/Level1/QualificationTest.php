<?php

declare(strict_types=1);

namespace EDTF\Tests\Functional\Level1;

use EDTF\Tests\Unit\FactoryTrait;
use PHPUnit\Framework\TestCase;

/**
 * Class L1DateTest
 *
 * @covers \EDTF\Parser
 * @covers \EDTF\Interval
 * @covers \EDTF\ExtDateTime
 *
 * @package EDTF\Tests\Unit
 */
class QualificationTest extends TestCase
{
    use FactoryTrait;

    public function testUncertainYear()
    {
        $date = $this->createExtDate('1984?');
        $this->assertTrue($date->uncertain('year'));
    }


    public function testApproximateYearAndMonth()
    {
        $date = $this->createExtDate("2004-06~");

        $this->assertTrue($date->approximate('year'));
        $this->assertTrue($date->approximate('month'));
        $this->assertSame(2004, $date->getYear());
        $this->assertSame(6, $date->getMonth());
    }


    public function testApproximateAndUncertainYearMonthDay()
    {
        $date = $this->createExtDate("2004-06-11%");

        $this->assertTrue($date->uncertain() && $date->approximate());
        $this->assertTrue($date->uncertain('day') && $date->approximate('day'));
        $this->assertTrue($date->uncertain('month') && $date->approximate('month'));
        $this->assertTrue($date->uncertain('year')  && $date->approximate('year'));

        $this->assertSame(2004, $date->getYear());
        $this->assertSame(6, $date->getMonth());
        $this->assertSame(11, $date->getDay());
    }
}