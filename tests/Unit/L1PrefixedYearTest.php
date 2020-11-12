<?php

declare(strict_types=1);

namespace EDTF\Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Class L1PrefixedYearTest
 *
 * @covers \EDTF\ExtDateTime
 * @covers \EDTF\Parser
 * @package EDTF\Tests\Unit
 */
class L1PrefixedYearTest extends TestCase
{
    use FactoryTrait;

    public function testWithoutDash()
    {
        $dateTime = $this->createExtDateTime('Y170000002');

        $this->assertSame(170000002, $dateTime->getYear());
        $this->assertTrue($dateTime->isQualificationUnspecified());
        $this->assertTrue($dateTime->isStatusTypeNormal());
    }

    public function testWithDash()
    {
        $dateTime = $this->createExtDateTime('Y-170000002');

        $this->assertSame(-170000002, $dateTime->getYear());
        $this->assertTrue($dateTime->isQualificationUnspecified());
        $this->assertTrue($dateTime->isStatusTypeNormal());
    }
}