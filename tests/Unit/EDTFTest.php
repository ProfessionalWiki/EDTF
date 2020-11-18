<?php
declare(strict_types=1);

namespace EDTF\Tests\Unit;

use EDTF\EDTF;
use EDTF\ExtDate;
use EDTF\ExtDateTime;
use EDTF\Interval;
use PHPUnit\Framework\TestCase;

/**
 * Class EDTFTest
 *
 * @package EDTF\Tests\Unit
 * @covers \EDTF\EDTF
 */
class EDTFTest extends TestCase
{
    public function testCreatingEdtfObjects()
    {
        $this->assertInstanceOf(ExtDate::class, EDTF::from('2016-03-01'));
        $this->assertInstanceOf(Interval::class, EDTF::from('2016/2019'));
    }

    public function testThrowExceptionWhenCreatingEdtfFromEmptyString()
    {
        $this->expectException(\InvalidArgumentException::class);

        EDTF::from("");
    }
}
