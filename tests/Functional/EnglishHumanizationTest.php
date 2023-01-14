<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Functional;

use EDTF\EdtfFactory;
use Generator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\PackagePrivate\Humanizer\InternationalizedHumanizer
 * @covers \EDTF\PackagePrivate\Humanizer\PrivateStructuredHumanizer
 * @covers \EDTF\PackagePrivate\Parser\Parser
 * @covers \EDTF\Model\Interval
 * @covers \EDTF\Model\IntervalSide
 * @covers \EDTF\PackagePrivate\Humanizer\Strategy\EnglishStrategy
 */
class EnglishHumanizationTest extends TestCase {

	public function humanizationProvider(): Generator {
		yield 'Full date' => [ '1975-07-01', 'July 1st, 1975' ];
		yield 'Year and month' => [ '1975-07', 'July 1975' ];
		yield 'Year only' => [ '1975', '1975' ];

		yield 'Leading zeroes' => [ '0042', 'Year 42' ];
		yield 'Year 0' => [ '0', 'Year 0' ];
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
		yield 'Interval with seasons' => [ '2010-21/2012-26', 'Spring 2010 to Summer (Northern Hemisphere) 2012' ];

		yield 'Interval with open end' => [ '2019/..', '2019 or later' ];
		yield 'Interval with open start' => [ '../2021', '2021 or earlier' ];
		yield 'Interval with unknown end' => [ '2019/', 'From 2019 to unknown' ];
		yield 'Interval with unknown start' => [ '/2021', 'From unknown to 2021' ];

		yield 'Entire full date approximate' => [ '2019-04-01~', 'Circa April 1st, 2019 (date is approximate)' ];
		yield 'Entire full date uncertain' => [ '2019-04-01?', 'Maybe April 1st, 2019 (date is uncertain)' ];
		yield 'Entire full date uncertain, verbose notation' => [ '?2019-?04-?01', 'Maybe April 1st, 2019 (date is uncertain)' ];
		yield 'Entire date uncertain, without day' => [ '2023-01?', 'Maybe January 2023 (date is uncertain)' ];
		yield 'Entire date uncertain, only day and month' => [ '?2023-?01', 'Maybe January 2023 (date is uncertain)' ];

		yield 'Only day uncertain' => [ '2023-01-?14', 'January 14th, 2023 (day is uncertain)' ];
		yield 'Only month uncertain' => [ '2023-?01-14', 'January 14th, 2023 (month is uncertain)' ];
		yield 'Only year uncertain' => [ '?2023-01-14', 'January 14th, 2023 (year is uncertain)' ];
		yield 'Only month approximate' => [ '2023-~01-14', 'January 14th, 2023 (month is approximate)' ];
		yield 'Only month uncertain and approximate' => [ '2023-?01-14', 'January 14th, 2023 (month is uncertain)' ];

		yield 'Year approximate' => [ '2019~', 'Circa 2019 (date is approximate)' ];
		yield 'Year uncertain' => [ '2019?', 'Maybe 2019 (date is uncertain)' ];
		yield 'Year uncertain approximation' => [ '2019%', 'Maybe circa 2019 (date is uncertain and approximate)' ];

		yield 'mixed approximation' => [ '1985-%12-?01', 'December 1st, 1985 (month is uncertain and approximate, and day is uncertain)' ];
		yield 'approximation all parts' => [ '%1985-%12-%01', 'Maybe circa December 1st, 1985 (date is uncertain and approximate)' ];
		yield 'approximation whole date' => [ '1985-12-01%', 'Maybe circa December 1st, 1985 (date is uncertain and approximate)' ];
		yield 'mixed approximation all' => [ '~1985-%12-?01', 'December 1st, 1985 (year is approximate, month is uncertain and approximate, and day is uncertain)' ];

		yield 'Time with UTC' => [ '1985-04-12T23:20:30Z', '23:20:30 UTC April 12th, 1985' ];
		yield 'Time with local time' => [ '1985-04-12T23:20:30', '23:20:30 (local time) April 12th, 1985' ];
		yield 'Time with positive UTC' => [ '1985-04-12T23:20:30+04', '23:20:30 UTC+4 April 12th, 1985' ];
		yield 'Time with negative UTC' => [ '1985-04-12T23:20:30-04', '23:20:30 UTC-4 April 12th, 1985' ];
		yield 'Time with UTC+4:30' => [ '1985-04-12T23:20:30+04:30', '23:20:30 UTC+4:30 April 12th, 1985' ];
		yield 'Time with UTC-11:45' => [ '1985-04-12T23:20:30-11:45', '23:20:30 UTC-11:45 April 12th, 1985' ];
		yield 'Time with UTC+00:05' => [ '1985-04-12T23:20:30+00:05', '23:20:30 UTC+0:05 April 12th, 1985' ];

		yield 'Time with leading zeroes' => [ '1985-04-12T01:02:03Z', '01:02:03 UTC April 12th, 1985' ];
		yield 'Time with all zeroes' => [ '1985-04-12T00:00:00Z', '00:00:00 UTC April 12th, 1985' ];
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

	/**
	 * @dataProvider setHumanizationProvider
	 */
	public function testSetHumanization( string $edtf, string $humanized ): void {
		$this->assertSame(
			$humanized,
			EdtfFactory::newStructuredHumanizerForLanguage( 'en' )->humanize(
				EdtfFactory::newParser()->parse( $edtf )->getEdtfValue()
			)->getSimpleHumanization()
		);
	}

	public function setHumanizationProvider(): Generator {
		yield 'Open set with space at the beginning' => [ '{ ..1983}', 'The year 1983 and all earlier years' ];
		yield 'Open set with space at the end' => [ '{ 1983.. }', 'The year 1983 and all later years' ];

		yield 'Disjunction' => [ '[2020, 2021]', '2020 or 2021' ];
		yield 'Conjunction' => [ '{2020, 2021}', '2020 and 2021' ];

		yield 'Open start all years included' => [ '{..2020}', 'The year 2020 and all earlier years' ];
		yield 'Open end all years included' => [ '{2020..}', 'The year 2020 and all later years' ];
		yield 'Open start one year of a set' => [ '[..2020]', 'The year 2020 or an earlier year' ];
		yield 'Open end one year of a set' => [ '[2020..]', 'The year 2020 or a later year' ];

		yield 'Open start all months included' => [ '{..1975-07}', 'July 1975 and all earlier months' ];
		yield 'Open end all months included' => [ '{1975-07..}', 'July 1975 and all later months' ];
		yield 'Open start one month of a set' => [ '[..1975-03]', 'March 1975 or an earlier month' ];
		yield 'Open end one month of a set' => [ '[1967-11..]', 'November 1967 or a later month' ];

		yield 'Open start all days included' => [ '{..1975-07-11}', 'July 11th, 1975; and all earlier dates' ];
		yield 'Open end all days included' => [ '{1975-07-11..}', 'July 11th, 1975; and all later dates' ];
		yield 'Open start one day of a set' => [ '[..1975-03-26]', 'March 26th, 1975; or an earlier date' ];
		yield 'Open end one day of a set' => [ '[1967-11-26..]', 'November 26th, 1967; or a later date' ];

		yield 'Open start all seasons included' => [ '{..1987-21}', 'Spring 1987 and all earlier seasons' ];
		yield 'Open end all seasons included' => [ '{2005-22..}', 'Summer 2005 and all later seasons' ];
		yield 'Open start one season of a set' => [ '[..1990-23]', 'Autumn 1990 or an earlier season' ];
		yield 'Open end one season of a set' => [ '[1992-21..]', 'Spring 1992 or a later season' ];

		yield 'All years range' => [ '{2020..2022}', 'All years from 2020 to 2022' ];
		yield 'All months range' => [ '{2020-01..2020-03}', 'All months from January 2020 to March 2020' ];
		yield 'All days range' => [ '{2020-01-01..2021-03-22}', 'All days from January 1st, 2020 to March 22nd, 2021' ];

		yield 'One year range' => [ '[1000..4242]', '1000, 4242 or a year in between' ];
		yield 'One month range' => [ '[2020-03..2022-01]', 'March 2020, January 2022 or a month in between' ];
		yield 'One day range' => [ '[1000-01-01..4242-04-20]', 'January 1st, 1000, April 20th, 4242 or a day in between' ];
		
		// yield 'Plural' => [ '1985-XX-01', 'January 1st, 1000, April 20th, 4242 or a day in between' ];
	}

	public function testStructuredSetHumanization(): void {
		$this->assertSame(
			[
				'December 31st, 1983; and all earlier dates',
				'All days from October 10th, 1984 to November 1st, 1984',
				'November 5th, 1984; and all later dates'
			],
			$this->getStructuredHumanization( '{..1983-12-31,1984-10-10..1984-11-01,1984-11-05..}' )
		);
	}

	private function getStructuredHumanization( string $edtf ): array {
		return EdtfFactory::newStructuredHumanizerForLanguage( 'en' )->humanize(
			EdtfFactory::newParser()->parse( $edtf )->getEdtfValue()
		)->getStructuredHumanization();
	}

}
