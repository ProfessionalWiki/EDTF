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
class StringHumanizerTest extends TestCase {

	public function humanizationProvider(): \Generator {
		yield 'Full date' => [ '1975-07-01', 'July 1st, 1975' ];
		yield 'Year and month' => [ '1975-07', 'July 1975' ];
		yield 'Year only' => [ '1975', '1975' ];

		yield 'Leading zeroes' => [ '0042', '42' ];

		yield 'Negative years' => [ '-1234', '1234 BC' ];

		yield 'Seasons: 22' => [ '1975-22', 'Summer 1975' ];
		yield 'Seasons: 32' => [ '1975-32', 'Winter (Southern Hemisphere) 1975' ];
		yield 'Seasons: 41' => [ '1975-41', 'Second semester 1975' ];

		yield 'Month only' => [ 'XXXX-12-XX', 'December' ];
		yield 'Day only' => [ 'XXXX-XX-12', '12th' ];
		yield 'Month and day' => [ 'XXXX-12-11', 'December 11th' ];
		yield 'Year and day' => [ '2020-XX-11', '11th of unknown month, 2020' ];
		yield 'Unspecified year decade' => [ '197X', '1970s' ];
		yield 'Unspecified year century ' => [ '19XX', '1900s' ];

		yield 'Interval with year to year' => [ '2019/2021', '2019 to 2021' ];
		yield 'Interval year and month' => [ '2019-01/2021-02', 'January 2019 to February 2021' ];
		yield 'Interval different date formats' => [ '2019/2021-02-09', '2019 to February 9th, 2021' ];

		yield 'Interval with open end' => [ '2019/..', '2019 or later' ];
		yield 'Interval with open start' => [ '../2021', '2021 or earlier' ];
		yield 'Interval with unknown end' => [ '2019/', 'From 2019 to unknown' ];
		yield 'Interval with unknown start' => [ '/2021', 'From unknown to 2021' ];

		yield 'Year approximate' => [ '2019~', 'Circa 2019' ];
		yield 'Year uncertain' => [ '2019?', 'Maybe 2019' ];
		yield 'Year uncertain approximation' => [ '2019%', 'Maybe circa 2019' ];

		yield 'Month approximate' => [ '2019-04~', 'Circa April 2019' ];
		yield 'Month uncertain' => [ '2019-04?', 'Maybe April 2019' ];
		yield 'Day approximate' => [ '2019-04-01~', 'Circa April 1st, 2019' ];
		yield 'Day uncertain' => [ '2019-04-01?', 'Maybe April 1st, 2019' ];

		yield 'Time with UTC' => [ '1985-04-12T23:20:30Z', '23:20:30 UTC April 12th, 1985' ];
		yield 'Time with local time' => [ '1985-04-12T23:20:30', '23:20:30 (local time) April 12th, 1985' ];
		yield 'Time with positive UTC' => [ '1985-04-12T23:20:30+04', '23:20:30 UTC+4 April 12th, 1985' ];
		yield 'Time with negative UTC' => [ '1985-04-12T23:20:30-04', '23:20:30 UTC-4 April 12th, 1985' ];
		yield 'Time with UTC+4:30' => [ '1985-04-12T23:20:30+04:30', '23:20:30 UTC+4:30 April 12th, 1985' ];
		yield 'Time with UTC-11:45' => [ '1985-04-12T23:20:30-11:45', '23:20:30 UTC-11:45 April 12th, 1985' ];
		yield 'Time with UTC+00:05' => [ '1985-04-12T23:20:30+00:05', '23:20:30 UTC+0:05 April 12th, 1985' ];

		yield 'Time with leading zeroes' => [ '1985-04-12T01:02:03Z', '01:02:03 UTC April 12th, 1985' ];
//		yield 'Time with all zeroes' => [ '1985-04-12T00:00:00Z', '00:00:00 UTC April 12th, 1985' ];
	}

	/**
	 * @dataProvider humanizationProvider
	 */
	public function testHumanization( string $edtf, string $humanized ): void {
		$this->assertSame(
			$humanized,
			EdtfFactory::newHumanizerForLanguage( 'en' )->humanize(
				EdtfFactory::newParser()->parse( $edtf )->getEdtfValue()
			)
		);
	}

}
