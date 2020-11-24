<?php

declare(strict_types=1);

namespace EDTF\Tests\Functional\Level1;

use EDTF\Tests\Unit\FactoryTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\Season
 * @covers \EDTF\Parser
 */
class SeasonTest extends TestCase
{
    use FactoryTrait;

    public function testCreatingSeason()
    {
        $season = $this->createSeason('2001-21');

        $this->assertSame(2001, $season->getYear());
        $this->assertSame(21, $season->getSeason());
        $this->assertSame('Spring', $season->getName());
    }
}