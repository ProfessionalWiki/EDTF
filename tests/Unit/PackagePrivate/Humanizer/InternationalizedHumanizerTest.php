<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Unit\PackagePrivate\Humanizer;

use EDTF\EdtfFactory;
use EDTF\EdtfValue;
use EDTF\Model\ExtDate;
use EDTF\Model\ExtDateTime;
use EDTF\Model\Interval;
use EDTF\Model\IntervalSide;
use EDTF\Model\Qualification;
use EDTF\Model\Season;
use EDTF\PackagePrivate\Humanizer\InternationalizedHumanizer;
use EDTF\PackagePrivate\Humanizer\Strategy\EnglishStrategy;
use EDTF\Tests\TestDoubles\MessageBuilderSpy;
use Generator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\PackagePrivate\Humanizer\InternationalizedHumanizer
 */
class InternationalizedHumanizerTest extends TestCase {

	private MessageBuilderSpy $messageBuilderSpy;

	private InternationalizedHumanizer $humanizer;

	protected function setUp(): void {
		parent::setUp();
		$this->messageBuilderSpy = new MessageBuilderSpy();
		$this->humanizer = new InternationalizedHumanizer( $this->messageBuilderSpy, new EnglishStrategy() );
	}

	/**
	 * @dataProvider seasonProvider
	 */
	public function testSeasons( string $expected, Season $season ): void {
		$this->assertHumanizes( $expected, $season );
	}

	private function assertHumanizes( string $expected, EdtfValue $input ): void {
		$this->assertSame(
			$expected,
			EdtfFactory::newStructuredHumanizerForLanguage( 'en' )->humanize( $input )->getSimpleHumanization()
		);
	}

	public function seasonProvider(): Generator {
		yield [ 'Spring 2001', new Season( 2001, 21 ) ];
		yield [ 'Summer 1234', new Season( 1234, 22 ) ];
		yield [ 'Autumn 10000', new Season( 10000, 23 ) ];
		yield [ 'Winter 42', new Season( 42, 24 ) ];
		yield [ 'Winter 0', new Season( 0, 24 ) ];
		yield [ 'Winter -1', new Season( -1, 24 ) ];
		yield [ 'First quarter 2001', new Season( 2001, 33 ) ];
		yield [ 'Second quadrimester 2001', new Season( 2001, 38 ) ];
		yield [ 'Second semester 2001', new Season( 2001, 41 ) ];
		yield [ 'Autumn (Southern Hemisphere) 2001', new Season( 2001, 31 ) ];
	}

	/**
	 * @dataProvider simpleDateProvider
	 */
	public function testSimpleDates( string $expected, ExtDate $date ): void {
		$this->assertHumanizes( $expected, $date );
	}

	public function simpleDateProvider(): Generator {
		yield [ 'January 1st, 2021', new ExtDate( 2021, 1, 1 ) ];
		yield [ 'February 9th, 2021', new ExtDate( 2021, 2, 9 ) ];
		yield [ 'March 13th, 2021', new ExtDate( 2021, 3, 13 ) ];
		yield [ 'April 14th, 2021', new ExtDate( 2021, 4, 14 ) ];
		yield [ 'May 15th, 2021', new ExtDate( 2021, 5, 15 ) ];
		yield [ 'June 16th, 2021', new ExtDate( 2021, 6, 16 ) ];
		yield [ 'July 17th, 2021', new ExtDate( 2021, 7, 17 ) ];
		yield [ 'August 18th, 2021', new ExtDate( 2021, 8, 18 ) ];
		yield [ 'September 19th, 2021', new ExtDate( 2021, 9, 19 ) ];
		yield [ 'October 20th, 2021', new ExtDate( 2021, 10, 20 ) ];
		yield [ 'November 21st, 2021', new ExtDate( 2021, 11, 21 ) ];
		yield [ 'December 30th, 2021', new ExtDate( 2021, 12, 30 ) ];

		yield [ 'January 2021', new ExtDate( 2021, 1 ) ];
		yield [ 'February 2021', new ExtDate( 2021, 2 ) ];

		yield [ '2021', new ExtDate( 2021 ) ];
		yield [ 'Year 0', new ExtDate( 0 ) ];
		yield [ 'Year 1 BC', new ExtDate( -1 ) ];

		yield [ 'August', new ExtDate( null, 8 ) ];
		yield [ 'January', new ExtDate( null, 1 ) ];

		yield [ 'August 22nd', new ExtDate( null, 8, 22 ) ];
		yield [ 'January 3rd', new ExtDate( null, 1, 3 ) ];

		yield [ '3rd of unknown month, 2021', new ExtDate( 2021, null, 3 ) ];
		yield [ '22nd of unknown month, 2021', new ExtDate( 2021, null, 22 ) ];
	}

	public function testSimpleDate(): void {
		$date = new ExtDate( 2021, 4, 3 );
		$this->humanizer->humanize( $date );
		$this->assertBuilderWasCalledWith( 'edtf-full-date' );
		$this->assertBuilderWasCalledWith( 'edtf-april' );
	}

	public function testNormalInterval(): void {
		$interval = new Interval(
			IntervalSide::newFromDate( new ExtDate( 1987 ) ),
			IntervalSide::newFromDate( new ExtDate( 2020 ) )
		);

		$this->humanizer->humanize( $interval );
		$this->assertBuilderCalledOnceWith( 'edtf-interval-normal', [ '1987', '2020' ] );
	}

	public function testIntervalOpenEnd(): void {
		$interval = new Interval(
			IntervalSide::newFromDate( new ExtDate( 1987 ) ),
			IntervalSide::newOpenInterval()
		);

		$this->humanizer->humanize( $interval );
		$this->assertBuilderCalledOnceWith( 'edtf-interval-open-end', [ '1987' ] );
	}

	public function testIntervalOpenStart(): void {
		$interval = new Interval(
			IntervalSide::newOpenInterval(),
			IntervalSide::newFromDate( new ExtDate( 2020 ) )
		);

		$this->humanizer->humanize( $interval );
		$this->assertBuilderCalledOnceWith( 'edtf-interval-open-start', [ '2020' ] );
	}

	public function testIntervalUnknownEnd(): void {
		$interval = new Interval(
			IntervalSide::newFromDate( new ExtDate( 1987 ) ),
			IntervalSide::newUnknownInterval()
		);

		$this->humanizer->humanize( $interval );
		$this->assertBuilderCalledOnceWith( 'edtf-interval-unknown-end', [ '1987' ] );
	}

	public function testIntervalUnknownStart(): void {
		$interval = new Interval(
			IntervalSide::newUnknownInterval(),
			IntervalSide::newFromDate( new ExtDate( 2001 ) )
		);

		$this->humanizer->humanize( $interval );
		$this->assertBuilderCalledOnceWith( 'edtf-interval-unknown-start', [ '2001' ] );
	}

	public function testTimezoneLocalTime(): void {
		$dateTime = new ExtDateTime( new ExtDate( 1987, 8, 12 ), 12, 24, 45 );

		$this->humanizer->humanize( $dateTime );
		$this->assertBuilderWasCalledWith( 'edtf-local-time' );
		$this->assertBuilderWasCalledWith( 'edtf-august' );
	}

	public function testBC(): void {
		$yearBC = new ExtDate( -800 );
		$this->humanizer->humanize( $yearBC );
		$this->assertBuilderCalledOnceWith( 'edtf-bc-year-short', [ '800' ] );
	}

	public function testYearCirca(): void {
		$yearCirca = new ExtDate( 1987, null, null, new Qualification( Qualification::APPROXIMATE, Qualification::UNDEFINED, Qualification::UNDEFINED ) );
		$this->humanizer->humanize( $yearCirca );
		$this->assertBuilderWasCalledWith( 'edtf-circa' );
	}

	public function testFullDateCirca(): void {
		$dateCirca = new ExtDate( 1654, 12, 12, new Qualification( Qualification::UNDEFINED, Qualification::UNDEFINED, Qualification::APPROXIMATE ) );
		$this->humanizer->humanize( $dateCirca );
		$this->assertBuilderWasCalledWith( 'edtf-december' );
		$this->assertBuilderWasCalledWith( 'edtf-day' );
		$this->assertBuilderWasCalledWith( 'edtf-parts-approximate' );
		$this->assertBuilderWasCalledWith( 'edtf-full-date' );
	}

	public function testUncertain(): void {
		$uncertainDate = new ExtDate( 1800, 5, 29, new Qualification( Qualification::UNDEFINED, Qualification::UNDEFINED, Qualification::UNCERTAIN ) );
		$this->humanizer->humanize( $uncertainDate );
		$this->assertBuilderWasCalledWith( 'edtf-parts-uncertain' );
		$this->assertBuilderWasCalledWith( 'edtf-day' );
		$this->assertBuilderWasCalledWith( 'edtf-may' );
		$this->assertBuilderWasCalledWith( 'edtf-full-date' );
	}

	public function testUncertainAndApproximate(): void {
		$uncertainDate = new ExtDate(
			1700, 4, 29, new Qualification( Qualification::UNDEFINED, Qualification::UNDEFINED, Qualification::UNCERTAIN_AND_APPROXIMATE )
		);
		$this->humanizer->humanize( $uncertainDate );
		$this->assertBuilderWasCalledWith( 'edtf-day' );
		$this->assertBuilderWasCalledWith( 'edtf-parts-uncertain-and-approximate' );
		$this->assertBuilderWasCalledWith( 'edtf-april' );
		$this->assertBuilderWasCalledWith( 'edtf-full-date' );
	}

	private function assertBuilderCalledOnceWith( string $messageKey, ?array $expectedArguments = null ): void {
		$this->assertCount( 1, $this->messageBuilderSpy->getBuildMessageCalls() );
		$this->assertEquals(
			array_merge( [ $messageKey ], $expectedArguments ?? [] ),
			$this->messageBuilderSpy->getBuildMessageCalls()[0]
		);
	}

	private function assertBuilderWasCalledWith( string $messageKey ): void {
		$allMessageKeys = array_merge( ...$this->messageBuilderSpy->getBuildMessageCalls() );

		$this->assertContainsEquals(
			$messageKey,
			$allMessageKeys,
			"Message builder was called with " . implode( ', ', $allMessageKeys )
		);
	}

}
