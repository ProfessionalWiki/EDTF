<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Unit\PackagePrivate\Humanizer;

use EDTF\PackagePrivate\Humanizer\HumanizedSetDates;
use Generator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\PackagePrivate\Humanizer\HumanizedSetDates
 */
class HumanizedSetDatesTest extends TestCase {

	/**
	 * @dataProvider notUseListProvider
	 */
	public function testShouldNotUseList( string ...$dates ): void {
		$this->assertFalse( ( new HumanizedSetDates( $dates ) )->shouldUseList() );
	}

	public function notUseListProvider(): Generator {
		yield [];
		yield [ '2020' ];
		yield [ '2020', '2021' ];
		yield [ '2020', '2021', '2022', '2023', '2024' ];

		yield [ 'Circa April 2019' ];
		yield [ '23:20:30 (local time) April 12th' ];
		yield [ '23:20:30 UTC-11:45 April 12th' ];
		yield [ 'January 2019 to February 2021' ];

		yield 'Long list with only years' => [
			'2020',
			'2021',
			'2022',
			'2023',
			'2024',
			'2025',
			'2026',
			'2027',
			'2028',
			'2029',
			'2030',
			'2031',
			'2032',
			'2033',
			'2034',
			'2035',
			'2036',
			'2037',
			'2038',
			'2039',
		];
	}

	/**
	 * @dataProvider useListProvider
	 */
	public function testShouldUseList( string ...$dates ): void {
		$this->assertTrue( ( new HumanizedSetDates( $dates ) )->shouldUseList() );
	}

	public function useListProvider(): Generator {
		yield 'Date with a comma' => [ '2020', 'July 1st, 2021' ];

		yield 'Long total text' => [
			'Maybe Circa April 2019',
			'Maybe Circa April 2020',
			'Maybe Circa April 2021',
			'Maybe Circa April 2022',
		];
	}

}
