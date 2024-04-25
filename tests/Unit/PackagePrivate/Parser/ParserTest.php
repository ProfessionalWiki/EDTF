<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Unit\PackagePrivate\Parser;

use EDTF\Model\ExtDate;
use EDTF\Model\Interval;
use EDTF\PackagePrivate\Parser\Parser;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\PackagePrivate\Parser\Parser
 * @covers \EDTF\PackagePrivate\Parser\RegexMatchesMapper
 * @covers \EDTF\PackagePrivate\Parser\Date
 * @covers \EDTF\PackagePrivate\Parser\ParsedData
 * @covers \EDTF\PackagePrivate\Parser\Qualification
 * @covers \EDTF\PackagePrivate\Parser\Time
 * @covers \EDTF\PackagePrivate\Parser\Timezone
 * @package EDTF\Tests\Unit
 */
class ParserTest extends TestCase {

	private function createParser( $data ): Parser {
		$parser = new Parser();
		$parser->parse( $data );
		return $parser;
	}

	public function testCreatingEdtfObjects() {
		$parser = new Parser();
		$this->assertInstanceOf( ExtDate::class, $parser->createEdtf( '2016-03-01' ) );
		$this->assertInstanceOf( Interval::class, $parser->createEdtf( '2016/2019' ) );
	}

	public function testThrowExceptionWhenCreatingEdtfFromEmptyString() {
		$this->expectException( InvalidArgumentException::class );
		$this->createParser( "" );
	}

	public function testThrowExceptionOnInvalidDataFormat() {
		$this->expectException( InvalidArgumentException::class );

		$parser = new Parser();
		$parser->createEdtf( 'foo' );
	}

	public function testShouldStoreInput() {
		$parser = $this->createParser( '1984' );
		$this->assertSame( '1984', $parser->getInput() );
	}

	public function testShouldParseCompleteDate() {
		$parser = new Parser();
		$parser->createEdtf( '2004-01-02' );

		$data = $parser->getParsedData();
		$this->assertSame( 2004, $data->getDate()->getYearNum() );
		$this->assertSame( 1, $data->getDate()->getMonthNum() );
		$this->assertSame( 2, $data->getDate()->getDayNum() );
	}

	public function testShouldParseCompleteDateTime() {
		$parser = new Parser();
		$parser->createEdtf( '2004-01-02T23:59:59Z' );

		$data = $parser->getParsedData();
		$this->assertSame( 2004, $data->getDate()->getYearNum() );
		$this->assertSame( 1, $data->getDate()->getMonthNum() );
		$this->assertSame( 2, $data->getDate()->getDayNum() );
		$this->assertSame( 23, $data->getTime()->getHour() );
		$this->assertSame( 59, $data->getTime()->getMinute() );
		$this->assertSame( 59, $data->getTime()->getSecond() );
	}

	public function testShouldParseUTCTimezone() {
		$parser = new Parser();
		$parser->createEdtf( '2004-01-01T10:10:10Z' );

		$data = $parser->getParsedData();
		$this->assertSame( 2004, $data->getDate()->getYearNum() );
		$this->assertSame( 1, $data->getDate()->getMonthNum() );
		$this->assertSame( 1, $data->getDate()->getDayNum() );
		$this->assertSame( 10, $data->getTime()->getHour() );
		$this->assertSame( 10, $data->getTime()->getMinute() );
		$this->assertSame( 10, $data->getTime()->getSecond() );
		$this->assertSame( "Z", $data->getTimezone()->getTzUtc() );
	}

	public function testShouldParseTimezoneValue() {
		$parser = new Parser();
		$parser->createEdtf( '2004-01-01T10:10:10+05:30' );

		$data = $parser->getParsedData();
		$this->assertSame( 2004, $data->getDate()->getYearNum() );
		$this->assertSame( 1, $data->getDate()->getMonthNum() );
		$this->assertSame( 1, $data->getDate()->getDayNum() );
		$this->assertSame( 10, $data->getTime()->getHour() );
		$this->assertSame( 10, $data->getTime()->getMinute() );
		$this->assertSame( 10, $data->getTime()->getSecond() );
		$this->assertSame( 5, $data->getTimezone()->getTzHour() );
		$this->assertSame( 30, $data->getTimezone()->getTzMinute() );
		$this->assertSame( '+', $data->getTimezone()->getTzSign() );
	}

	public function testShouldParseDateTimeWithZeroTime() {
		$parser = new Parser();
		$parser->createEdtf( '2004-01-01T00:00:00Z' );

		$data = $parser->getParsedData();

		$this->assertSame( 2004, $data->getDate()->getYearNum() );
		$this->assertSame( 1, $data->getDate()->getMonthNum() );
		$this->assertSame( 1, $data->getDate()->getDayNum() );
		$this->assertSame( 0, $data->getTime()->getHour() );
		$this->assertSame( 0, $data->getTime()->getMinute() );
		$this->assertSame( 0, $data->getTime()->getSecond() );
		$this->assertSame( 'Z', $data->getTimezone()->getTzUtc() );
	}

	public function testShouldParseDateTimeWithZeroMinutes() {
		$parser = new Parser();
		$parser->createEdtf( '2004-01-01T01:00:20Z' );

		$data = $parser->getParsedData();

		$this->assertSame( 2004, $data->getDate()->getYearNum() );
		$this->assertSame( 1, $data->getDate()->getMonthNum() );
		$this->assertSame( 1, $data->getDate()->getDayNum() );
		$this->assertSame( 1, $data->getTime()->getHour() );
		$this->assertSame( 0, $data->getTime()->getMinute() );
		$this->assertSame( 20, $data->getTime()->getSecond() );
		$this->assertSame( 'Z', $data->getTimezone()->getTzUtc() );
	}

	public function testShouldParseDateTimeWithZeroSeconds() {
		$parser = new Parser();
		$parser->createEdtf( '2004-01-01T01:21:00Z' );

		$data = $parser->getParsedData();

		$this->assertSame( 2004, $data->getDate()->getYearNum() );
		$this->assertSame( 1, $data->getDate()->getMonthNum() );
		$this->assertSame( 1, $data->getDate()->getDayNum() );
		$this->assertSame( 1, $data->getTime()->getHour() );
		$this->assertSame( 21, $data->getTime()->getMinute() );
		$this->assertSame( 0, $data->getTime()->getSecond() );
		$this->assertSame( 'Z', $data->getTimezone()->getTzUtc() );
	}

	public function testShouldParseLetterPrefixedCalendarYear() {
		$parser = $this->createParser( 'Y170000002' );
		$data = $parser->getParsedData();
		$this->assertSame( 170000002, $data->getDate()->getYearNum() );

		$parser = $this->createParser( 'Y-170000002' );
		$data = $parser->getParsedData();
		$this->assertSame( -170000002, $data->getDate()->getYearNum() );
	}

	public function testShouldParseSeason() {
		$parser = $this->createParser( '2001-21' );
		$data = $parser->getParsedData();
		$this->assertNull( $data->getDate()->getMonthNum() );
		$this->assertSame( 21, $data->getDate()->getSeason() );
	}

	public function testThrowExceptionOnInvalidSeasonNumber() {
		$this->expectException( InvalidArgumentException::class );
		$this->createParser( '2001-99' );
	}

	public function testShouldParseQualificationWithinYear() {
		$parser = $this->createParser( '?1984' );
		$data = $parser->getParsedData();
		$this->assertSame( 1984, $data->getDate()->getYearNum() );
		$this->assertSame( "?", $data->getQualification()->getYearOpenFlag() );

		$parser = $this->createParser( '1984?' );
		$data = $parser->getParsedData();
		$this->assertSame( 1984, $data->getDate()->getYearNum() );
		$this->assertSame( "?", $data->getQualification()->getYearCloseFlag() );
	}

	public function testShouldParseQualificationWithinMonth() {
		$parser = $this->createParser( "1984-02%" );
		$data = $parser->getParsedData();
		$this->assertSame( 2, $data->getDate()->getMonthNum() );
		$this->assertSame( "%", $data->getQualification()->getMonthCloseFlag() );

		$parser = $this->createParser( "1984-02~" );
		$data = $parser->getParsedData();
		$this->assertSame( 2, $data->getDate()->getMonthNum() );
		$this->assertSame( "~", $data->getQualification()->getMonthCloseFlag() );
	}

	public function testShouldParseQualificationWithinDay() {
		$parser = $this->createParser( "1984-02-01~" );
		$data = $parser->getParsedData();
		$this->assertSame( 2, $data->getDate()->getMonthNum() );
		$this->assertSame( 1, $data->getDate()->getDayNum() );
		$this->assertSame( "~", $data->getQualification()->getDayCloseFlag() );

		$parser = $this->createParser( "1984-02-01%" );
		$data = $parser->getParsedData();
		$this->assertSame( 2, $data->getDate()->getMonthNum() );
		$this->assertSame( 1, $data->getDate()->getDayNum() );
		$this->assertSame( "%", $data->getQualification()->getDayCloseFlag() );
	}

	public function testShouldParseQualificationInDatePart() {
		$parser = $this->createParser( '~1984-%02-?01' );
		$data = $parser->getParsedData();
		$this->assertSame( "~", $data->getQualification()->getYearOpenFlag() );
		$this->assertSame( "%", $data->getQualification()->getMonthOpenFlag() );
		$this->assertSame( "?", $data->getQualification()->getDayOpenFlag() );
	}

	public function testParseUnspecifiedDigitWithYearPrecision() {
		$parser = $this->createParser( '201X' );
		$data = $parser->getParsedData();
		$this->assertSame( 2010, $data->getDate()->getYearNum() );
		$this->assertSame( "201X", $data->getDate()->getRawYear() );

		$parser = $this->createParser( '20XX' );
		$data = $parser->getParsedData();
		$this->assertSame( 2000, $data->getDate()->getYearNum() );
		$this->assertSame( "20XX", $data->getDate()->getRawYear() );
	}

	public function testParseUnspecifiedDigitWithMonthPrecision() {
		$parser = $this->createParser( '2010-XX' );
		$data = $parser->getParsedData();
		$this->assertSame( 2010, $data->getDate()->getYearNum() );
		$this->assertNull( $data->getDate()->getMonthNum() );
	}

	public function testParseUnspecifiedDigitWithDayPrecision() {
		$parser = $this->createParser( '2010-12-XX' );
		$data = $parser->getParsedData();
		$this->assertSame( 2010, $data->getDate()->getYearNum() );
		$this->assertSame( 12, $data->getDate()->getMonthNum() );
		$this->assertNull( $data->getDate()->getDayNum() );
	}

	public function testParseUnspecifiedDigitWithMixedPrecision() {
		$parser = $this->createParser( "20XX-XX-XX" );
		$data = $parser->getParsedData();
		$this->assertSame( 2000, $data->getDate()->getYearNum() );
		$this->assertNull( $data->getDate()->getMonthNum() );
		$this->assertNull( $data->getDate()->getDayNum() );
	}

	/**
	 * @dataProvider setOpenMiddlePrecisionMismatch
	 */
	public function testThrowExceptionWhenCreatingOpenMiddleSetWithPrecisionMismatch( string $invalidOpenMiddleSet ): void {
		$parser = new Parser();
		$this->expectException( InvalidArgumentException::class );
		$this->expectExceptionMessage( 'The precision of dates in a set range needs to be the same' );
		$parser->createEdtf( $invalidOpenMiddleSet );
	}

	public function setOpenMiddlePrecisionMismatch(): array {
		return [
			[ '{1990..1991-02}' ],
			[ '{1990..1991-02-25}' ],
			[ '{1990-01..1991}' ],
			[ '{1990-01..1991-02-10}' ],
			[ '{1990-01-02..2005}' ],
			[ '{1990-01-20..2000-02}' ]
		];
	}

	/**
	 * @dataProvider setOpenMiddleNonExtDateValues
	 */
	public function testThrowExceptionWhenCreatingOpenMiddleSetWithNonExtDateValues( string $invalidOpenMiddleSet, string $exceptionMessage ): void {
		$parser = new Parser();
		$this->expectException( InvalidArgumentException::class );
		$this->expectExceptionMessage( $exceptionMessage );
		$parser->createEdtf( $invalidOpenMiddleSet );
	}

	public function setOpenMiddleNonExtDateValues(): array {
		return [
			[ "{1980-25..1981}", "Ranges in sets can only contain dates" ],
			[ "{2000..2001-27}", "Ranges in sets can only contain dates" ],
			[ "{2000?..2001-27}", "Dates in set ranges cannot be uncertain" ],
			[ "{2000~..2001-02}", "Dates in set ranges cannot be approximate" ],
			[ "{2000%..2000-02}", "Dates in set ranges cannot be uncertain" ],
		];
	}

	/**
	 * @dataProvider provideCombinedUncertainAndApproximateQualifiers
	 */
	public function testThrowExceptionWhenUsedCombinedUncertainAndApproximateQualifiers( string $combinedUncertainAndApproximate ): void {
		$parser = new Parser();
		$this->expectException( InvalidArgumentException::class );
		$this->expectExceptionMessage( 'Invalid edtf format ' . $combinedUncertainAndApproximate );
		$parser->createEdtf( $combinedUncertainAndApproximate );
	}

	public function provideCombinedUncertainAndApproximateQualifiers(): array {
		return [
			[ '1990?~' ],
			[ '1990~?' ],
			[ '?~1990}' ],
			[ '~?1990' ],
			[ '1990-02~?' ],
			[ '1990-?~02' ],
			[ '1990-?~02~?-03' ],
			[ '1990-02-~?03' ],
		];
	}
}