<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Functional;

use EDTF\EdtfFactory;
use Generator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\PackagePrivate\Humanizer\InternationalizedHumanizer
 * @covers \EDTF\PackagePrivate\Parser\Parser
 * @covers \EDTF\Model\Interval
 * @covers \EDTF\Model\IntervalSide
 * @covers \EDTF\PackagePrivate\Humanizer\Strategy\DefaultStrategy
 */
class FrenchHumanizationTest extends TestCase {

	public function humanizationProvider(): Generator {
		yield 'Interval year and month' => [ '2019-01/2021-02', 'du janvier 2019 au février 2021' ];
		yield 'Full date' => [ '1975-07-10', '10 juillet 1975' ];
		yield 'Full date first day' => [ '1975-07-01', '1er juillet 1975' ];
		yield 'Year and month' => [ '1975-07', 'juillet 1975' ];
		yield 'Year only' => [ '1975', '1975' ];
		yield 'Leading zeroes' => [ '0042', 'année 42' ];

		yield 'Interval with open end' => [ '2019/..', 'depuis le 2019 (fin indéterminée)' ];
		yield 'Interval with open start' => [ '../2021', '2021 ou antérieur' ];
		yield 'Interval with unknown end' => [ '2019/', 'depuis le 2019 jusqu’à une date inconnue' ];
		yield 'Interval with unknown start' => [ '/2021', 'depuis une date inconnue jusqu’àu 2021' ];

		yield 'Year approximate' => [ '2019~', 'autour du 2019 (date approximative)' ];
		yield 'Year uncertain' => [ '2019?', '2019 (date incertaine)' ];
		yield 'Year uncertain approximation' => [ '2019%', 'autour du 2019 (date incertaine et approximative)' ];

		yield 'Month approximate' => [ '2019-04~', 'autour du avril 2019 (date approximative)' ];
		yield 'Month uncertain' => [ '2019-04?', 'avril 2019 (date incertaine)' ];
		yield 'Day approximate' => [ '2019-04-01~', 'autour du 1er avril 2019 (date approximative)' ];
		yield 'Day uncertain' => [ '2019-04-01?', '1er avril 2019 (date incertaine)' ];

		yield 'Time with UTC' => [ '1985-04-12T23:20:30Z', '23:20:30 UTC 12 avril 1985' ];
		yield 'Time with local time' => [ '1985-04-12T23:20:30', '23:20:30 (heure locale) 12 avril 1985' ];
		yield 'Time with positive UTC' => [ '1985-04-12T23:20:30+04', '23:20:30 UTC+4 12 avril 1985' ];
		yield 'Time with negative UTC' => [ '1985-04-12T23:20:30-04', '23:20:30 UTC-4 12 avril 1985' ];
		yield 'Time with UTC+4:30' => [ '1985-04-12T23:20:30+04:30', '23:20:30 UTC+4:30 12 avril 1985' ];
		yield 'Time with UTC-11:45' => [ '1985-04-12T23:20:30-11:45', '23:20:30 UTC-11:45 12 avril 1985' ];
		yield 'Time with UTC+00:05' => [ '1985-04-12T23:20:30+00:05', '23:20:30 UTC+0:05 12 avril 1985' ];

		yield 'Time with leading zeroes' => [ '1985-04-12T01:02:03Z', '01:02:03 UTC 12 avril 1985' ];
		yield 'Time with all zeroes' => [ '1985-04-12T00:00:00Z', '00:00:00 UTC 12 avril 1985' ];
	}

	/**
	 * @dataProvider humanizationProvider
	 */
	public function testHumanization( string $edtf, string $humanized ): void {
		$this->assertSame(
			$humanized,
			EdtfFactory::newHumanizerForLanguage( 'fr' )->humanize(
				EdtfFactory::newParser()->parse( $edtf )->getEdtfValue()
			)
		);
	}
}
