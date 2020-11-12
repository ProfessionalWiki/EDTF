<?php

declare(strict_types=1);

namespace EDTF\Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Class L1DateTest
 * @covers \EDTF\Parser
 * @covers \EDTF\Interval
 * @covers \EDTF\ExtDateTime
 * @package EDTF\Tests\Unit
 */
class L1QualificationTest extends TestCase
{
    use FactoryTrait;

    public function testApproximateYear()
    {
        $dateTime = $this->createExtDateTime("1984~");

        $this->assertTrue($dateTime->isQualificationApproximate());
        $this->assertSame(1984, $dateTime->getYear());
    }

    public function testApproximateMonth()
    {
        $dateTime = $this->createExtDateTime("2004-06~");

        $this->assertTrue($dateTime->isQualificationApproximate());
        $this->assertTrue($dateTime->isStatusTypeNormal());
        $this->assertSame(2004, $dateTime->getYear());
        $this->assertSame(6, $dateTime->getMonth());
    }

    public function testUncertainMonth()
    {
        $dateTime = $this->createExtDateTime("2004-06?");

        $this->assertTrue($dateTime->isQualificationUncertain());
        $this->assertSame(2004, $dateTime->getYear());
        $this->assertSame(6, $dateTime->getMonth());
    }

    public function testApproximateAndUncertain()
    {
        $dateTime = $this->createExtDateTime("2004-06-11%");

        $this->assertTrue($dateTime->isQualificationBoth());
        $this->assertTrue($dateTime->isStatusTypeNormal());
        $this->assertSame(2004, $dateTime->getYear());
        $this->assertSame(6, $dateTime->getMonth());
        $this->assertSame(11, $dateTime->getDay());
    }
}