<?php

namespace EDTF\Tests\Unit\PackagePrivate\ValueObjects\Composites;

use EDTF\PackagePrivate\ValueObjects\Composites\Time;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\PackagePrivate\ValueObjects\Composites\Time
 */
class TimeTest extends TestCase
{
    public function testCreateZeroTime()
    {
        $time = new Time(0, 0, 0);

        $this->assertSame(0, $time->getHour());
        $this->assertSame(0, $time->getMinute());
        $this->assertSame(0, $time->getSecond());
    }

    public function testCreateSpecifiedTime()
    {
        $time = new Time(23, 14, 50);

        $this->assertSame(23, $time->getHour());
        $this->assertSame(14, $time->getMinute());
        $this->assertSame(50, $time->getSecond());
    }
}