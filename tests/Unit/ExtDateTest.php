<?php

namespace EDTF\Tests\Unit;

use Carbon\Carbon;
use EDTF\Exceptions\ExtDateException;
use EDTF\ExtDate;
use EDTF\Qualification;
use EDTF\UnspecifiedDigit;
use EDTF\Utils\DatetimeFactory\CarbonFactory;
use EDTF\Utils\DatetimeFactory\DatetimeFactoryException;
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
        $this->assertEquals('2010-10-01', $d->getInput());
        $this->assertEquals('ExtDate', $d->getType());

        $this->assertTrue($d->isNormalInterval());
        $this->assertFalse($d->isOpenInterval());
        $this->assertFalse($d->isUnknownInterval());
    }

    /**
     * @param ExtDate $extDate
     * @param int $expectedMin
     * @throws \EDTF\Exceptions\ExtDateException
     * @dataProvider minDataProvider
     */
    public function testGetMin(ExtDate $extDate, int $expectedMin)
    {
        $this->assertEquals($expectedMin, $extDate->getMin());
    }

    /**
     * @param ExtDate $extDate
     * @param int $expectedMax
     * @dataProvider maxDataProvider
     */
    public function testGetMax(ExtDate $extDate, int $expectedMax)
    {
        $this->assertEquals($expectedMax, $extDate->getMax());
    }

    public function testGetMinThrowsException()
    {
        $year = 1987;
        $month = 12;
        $day = 100;

        $date = new ExtDate("1987-12-100", $year, $month, $day);

        $dateTimeFactoryMock = $this->createMock(CarbonFactory::class);
        $dateTimeFactoryMock->method('create')
            ->with($year, $month, $day)
            ->willThrowException(new DatetimeFactoryException);

        $this->expectException(ExtDateException::class);
        $this->expectExceptionMessage("Can't generate minimum date.");

        $date->setDatetimeFactory($dateTimeFactoryMock);
        $date->getMin();
    }

    public function testGetMaxThrowsException()
    {
        $year = 1987;
        $month = 100;

        $date = new ExtDate("1987-100-XX", $year, $month);

        $dateTimeFactoryMock = $this->createMock(CarbonFactory::class);
        $dateTimeFactoryMock->expects($this->once())
            ->method('create')
            ->with($year, $month)
            ->willThrowException(new DatetimeFactoryException);

        $this->expectException(ExtDateException::class);
        $this->expectExceptionMessage("Can't generate max value from '1987-100-XX'");

        $date->setDatetimeFactory($dateTimeFactoryMock);
        $date->getMax();
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

    public function minDataProvider()
    {
        return [
            [
                new ExtDate('1987-10-01', 1987, 10, 1),
                Carbon::create(1987, 10)->timestamp
            ],
            [
                new ExtDate('1987', 1987),
                Carbon::create(1987)->timestamp
            ],
            [
                new ExtDate('1987-12', 1987, 12),
                Carbon::create(1987, 12)->timestamp
            ]
        ];
    }

    public function maxDataProvider()
    {
        return [
            [
                new ExtDate('1987-10-01', 1987, 10, 1),
                Carbon::create(1987, 10, 1, 23, 59, 59)->timestamp
            ],
            [
                new ExtDate('1988', 1988),
                Carbon::create(1988, 12, 31, 23, 59, 59)->timestamp
            ],
            [
                new ExtDate('1987-02', 1987, 2),
                $daysInMonth = Carbon::create(1987, 2)->lastOfMonth()->endOfDay()->timestamp
            ]
        ];
    }
}
