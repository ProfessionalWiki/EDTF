<?php

namespace EDTF\Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Class EDTFTest
 *
 * @package EDTF\Tests\Unit
 * @covers \EDTF\DateTime
 * @covers \EDTF\Parser
 * @covers edtf_datetime
 */
class DateTimeTest extends TestCase
{
    public function setUp(): void
    {
        date_default_timezone_set('Etc/GMT-6');
    }

    /**
     * @param string $data
     * @param array $expectedYMD
     * @param array $expectedHMS
     * @dataProvider getTestLevel0DateTime
     */
    public function testLevel0DateTime($data, $expectedYMD = [], $expectedHMS = [])
    {
        $edtf = edtf_datetime($data);

        if(!empty($expectedYMD)){
            $this->assertEquals(
                $expectedYMD,[$edtf->getYear(),$edtf->getMonth(), $edtf->getDay()]);
        }

        if(!empty($expectedHMS)){
            $this->assertEquals(
                $expectedHMS,[
                    $edtf->getHour(),
                    $edtf->getMinute(),
                    $edtf->getSecond(),
                    $edtf->getTimezone()
                ]
            );
        }
    }

    public function getTestLevel0DateTime()
    {
        return [
            // Date
            ['1985-04-12',[1985,4,12]],
            ['1985-04', [1985,4,null]],
            ['1985',[1985,null,null]],

            // Date and Time
            ['1985-04-12T23:20:30', [1985,04,12],[23,20,30,null]],
            ['1985-04-12T23:20:30Z',[1985,04,12],[23,20,30,'UTC']],
            ['1985-04-12T23:20:30-04:00', [1985,04,12], [23,20,30,'-04:00']],
            ['1985-04-12T23:20:30+04:30',[1985,04,12],[23,20,30,'+04:30']],
        ];
    }
}
