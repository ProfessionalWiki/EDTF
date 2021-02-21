<?php

declare(strict_types=1);

namespace EDTF\Tests\Unit\Model;

use Carbon\Carbon;
use EDTF\Model\ExtDate;
use EDTF\Model\Set;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\Model\Set
 */
class SetTest extends TestCase
{
    public function testCreateOneOfASet(): void
    {
        $date1 = new ExtDate(1960);
        $date2 = new ExtDate(1961);
        $date3 = new ExtDate(1963, 6);
        $date4 = new ExtDate(1965, 4);

        $set = new Set([$date1, $date2, $date3, $date4], false, false, false);

        $this->assertFalse($set->isAllMembers());
        $this->assertFalse($set->hasOpenEnd());
        $this->assertFalse($set->hasOpenStart());
        $this->assertCount(4, $set->getDates());

        $expectedMin = Carbon::create(1960)->getTimestamp();
        $this->assertSame($expectedMin, $set->getMin());

        $expectedMax = Carbon::create(1965, 4, 30, 23, 59, 59)->getTimestamp();
        $this->assertSame($expectedMax, $set->getMax());

        $this->assertTrue($set->covers(new ExtDate(1961)));
        $this->assertFalse($set->covers(new ExtDate(1963, 8)));
        $this->assertTrue($set->covers(new ExtDate(1963, 6, 20)));
    }
}