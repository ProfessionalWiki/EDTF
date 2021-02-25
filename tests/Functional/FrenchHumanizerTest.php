<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Functional;

use EDTF\EdtfFactory;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\PackagePrivate\Humanizer\InternationalizedHumanizer
 * @covers \EDTF\PackagePrivate\Parser\Parser
 * @covers \EDTF\Model\Interval
 * @covers \EDTF\Model\IntervalSide
 */
class FrenchHumanizerTest extends TestCase
{
    public function humanizationProvider(): \Generator {
        // TODO: just a foundation to test French translations. Will be extended later
        // check what is the exact French translation below. Probably, it should be different
        yield 'Interval year and month' => [ '2019-01/2021-02', 'De Janvier 2019 à Février 2021' ];
    }

    /**
     * @dataProvider humanizationProvider
     */
    public function testHumanization(string $edtf, string $humanized): void
    {
        $this->assertSame(
            $humanized,
            EdtfFactory::newHumanizerForLanguage( 'fr' )->humanize(
                EdtfFactory::newParser()->parse( $edtf )->getEdtfValue()
            )
        );
    }
}