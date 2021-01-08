<?php

namespace EDTF\Tests\Unit;

use EDTF\ExtDate;
use EDTF\Interval;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\Interval
 * @package EDTF\Tests\Unit
 */
class IntervalTest extends TestCase
{
    public function testCreate()
    {
        $date = $this->createMock(ExtDate::class);
        $interval = new Interval("",$date, $date);
        $this->assertSame($date, $interval->getStart());
        $this->assertSame($date, $interval->getEnd());
    }
}
