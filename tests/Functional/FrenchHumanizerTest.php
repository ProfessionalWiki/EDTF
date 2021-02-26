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
        yield 'Interval year and month' => [ '2019-01/2021-02', 'De janvier 2019 à février 2021' ];
        yield 'Full date' => [ '1975-07-10', '10 juillet 1975' ];
        yield 'Full date first day' => [ '1975-07-01', '1er juillet 1975' ];
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