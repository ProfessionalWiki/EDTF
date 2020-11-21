<?php

namespace EDTF\Tests\Unit;

use EDTF\Season;
use PHPUnit\Framework\TestCase;

/**
 * Class SeasonTest
 *
 * @covers \EDTF\Season
 * @package EDTF\Tests\Unit
 */
class SeasonTest extends TestCase
{
    public function testCreate()
    {
        $season = new Season(2010, 21);
        $this->assertSame(2010, $season->getYear());
        $this->assertSame(21, $season->getSeason());
    }
}
