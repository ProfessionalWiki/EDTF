<?php

declare(strict_types=1);

namespace EDTF\Tests\Unit;

use Carbon\Carbon;
use EDTF\EdtfParser;
use EDTF\Season;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\Season
 * @package EDTF\Tests\Unit
 */
class SeasonTest extends TestCase
{
    public function testCreate()
    {
        $season = new Season(2010, 33);
        $this->assertSame(2010, $season->getYear());
        $this->assertSame(33, $season->getSeason());
    }

    public function testQuarterValues()
    {
        $this->assertSeasonValues('2010-33', '2010-01-01', '2010-03-31');
        $this->assertSeasonValues('2010-34', '2010-04-01', '2010-06-30');
        $this->assertSeasonValues('2010-35', '2010-07-01', '2010-09-30');
        $this->assertSeasonValues('2010-36', '2010-10-01', '2010-12-31');
    }

    public function testQuadrimesterValues()
    {
        $this->assertSeasonValues('2010-37', '2010-01-01', '2010-04-30');
        $this->assertSeasonValues('2010-38', '2010-05-01', '2010-08-31');
        $this->assertSeasonValues('2010-39', '2010-09-01', '2010-12-31');
    }

    public function testSemesterValues()
    {
        $this->assertSeasonValues('2010-40', '2010-01-01', '2010-06-30');
        $this->assertSeasonValues('2010-41', '2010-07-01', '2010-12-31');
    }

    private function assertSeasonValues(string $input, string $expectedStart, string $expectedEnd)
    {
        $season = (new EdtfParser())->parse($input)->getEdtfValue();
        $expectedStart = Carbon::parse($expectedStart);
        $expectedEnd = Carbon::parse($expectedEnd);

        $seasonStart = Carbon::createFromTimestamp($season->getMin());
        $seasonEnd = Carbon::createFromTimestamp($season->getMax());

        // start season validation
        $this->assertSame($expectedStart->year, $seasonStart->year);
        $this->assertSame($expectedStart->month, $seasonStart->month);
        $this->assertSame($expectedStart->day, $seasonStart->day);

        // end season validation
        $this->assertSame($expectedEnd->year, $seasonEnd->year);
        $this->assertSame($expectedEnd->month, $seasonEnd->month);
        $this->assertSame($expectedEnd->day, $seasonEnd->day);
    }
}
