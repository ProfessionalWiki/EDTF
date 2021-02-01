<?php

namespace EDTF\Tests\Unit\Contracts;

use EDTF\ExtDate;
use PHPUnit\Framework\TestCase;

/**
 * Class CoversTraitTest
 * @package EDTF\Tests\Unit\Contracts
 * @covers \EDTF\Contracts\CoversTrait
 */
class CoversTraitTest extends TestCase
{
    public function testCoversYearAgainstYear()
    {
        $edtf = new ExtDate("1987", 1987);
        $this->assertTrue($edtf->covers(new ExtDate("1987", 1987)));
        $this->assertFalse($edtf->covers(new ExtDate("1986", 1986)));
    }

    public function testCoversYearAgainstYearAndMonth()
    {
        $edtf = new ExtDate("1987", 1987);
        $this->assertTrue($edtf->covers(new ExtDate("1987-02", 1987, 2)));
        $this->assertFalse($edtf->covers(new ExtDate("1986-04", 1986, 4)));
    }

    public function testCoversYearAgainstFullDate()
    {
        $edtf = new ExtDate("1987", 1987);
        $this->assertTrue($edtf->covers(new ExtDate("1987-02-01", 1987, 2, 1)));
        $this->assertTrue($edtf->covers(new ExtDate("1987-01-01", 1987, 1, 1)));
        $this->assertTrue($edtf->covers(new ExtDate("1987-11-30", 1987, 11, 30)));
        $this->assertTrue($edtf->covers(new ExtDate("1987-12-31", 1987, 12, 31)));
        $this->assertFalse($edtf->covers(new ExtDate("1986-02-01", 1986, 2, 1)));
    }

    public function testCoversYearAndMonthAgainstYearAndMonth()
    {
        $edtf = new ExtDate("1987-01", 1987, 1);
        $this->assertTrue($edtf->covers(new ExtDate("1987-01", 1987, 1)));
        $this->assertFalse($edtf->covers(new ExtDate("1987-02", 1987, 2)));
    }

    public function testCoversYearAndMonthAgainstFullDate()
    {
        $edtf = new ExtDate("1987-01", 1987, 1);
        $this->assertTrue($edtf->covers(new ExtDate("1987-01-25", 1987, 1, 25)));
        $this->assertTrue($edtf->covers(new ExtDate("1987-01-01", 1987, 1, 1)));
        $this->assertTrue($edtf->covers(new ExtDate("1987-01-31", 1987, 1, 31)));
        $this->assertFalse($edtf->covers(new ExtDate("1987-02-01", 1987, 2, 1)));
    }

    public function testCoversFullDateAgainstFullDate()
    {
        $edtf = new ExtDate("1987-10-12", 1987, 10, 12);
        $this->assertTrue($edtf->covers(new ExtDate("1987-10-12", 1987, 10, 12)));
        $this->assertFalse($edtf->covers(new ExtDate("1987-10-13", 1987, 10, 13)));
    }
}