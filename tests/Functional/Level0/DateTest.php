<?php

declare(strict_types=1);

namespace EDTF\Tests\Functional\Level0;


use EDTF\ExtDate;
use EDTF\Tests\Unit\FactoryTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\PackagePrivate\Parser
 * @covers \EDTF\ExtDate
 * @package EDTF\Tests\Unit
 */
class DateTest extends TestCase
{
    use FactoryTrait;

    public function testCompleteRepresentation()
    {
        $date = $this->createExtDate('1985-04-12');

        $this->assertInstanceOf(ExtDate::class, $date);
        $this->assertSame(1985, $date->getYear());
        $this->assertSame(4, $date->getMonth());
        $this->assertSame(12, $date->getDay());
    }

    public function testReducedPrecisionForYearAndMonth()
    {
        $date = $this->createExtDate('1985-04');

        $this->assertInstanceOf(ExtDate::class, $date);
        $this->assertSame(1985, $date->getYear());
        $this->assertSame(4, $date->getMonth());
        $this->assertNull($date->getDay());
    }

    public function testReducedPrecisionForYear()
    {
        $date = $this->createExtDate('1985');

        $this->assertInstanceOf(ExtDate::class, $date);
        $this->assertSame(1985, $date->getYear());
        $this->assertNull($date->getMonth());
        $this->assertNull($date->getDay());
    }
}