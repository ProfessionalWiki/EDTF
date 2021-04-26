<?php

declare( strict_types = 1 );

namespace EDTF\PackagePrivate\Parser;

use Carbon\Carbon;
use EDTF\EdtfValue;
use EDTF\Model\ExtDate;
use EDTF\Model\ExtDateTime;
use EDTF\Model\Interval;
use EDTF\Model\IntervalSide;
use EDTF\Model\Qualification;
use EDTF\Model\Season;
use EDTF\Model\Set;
use EDTF\Model\UnspecifiedDigit;
use InvalidArgumentException;

/**
 * TODO: there might be cohesive sets of code to extract, for instance QualificationParser
 * TODO: remove public getters if they are not needed (likely most are not)
 *
 * @internal
 */
class Parser {

	private static string $regexPattern = '';

	private string $input = "";
	private array $matches = [];
	private RegexMatchesMapper $mapper;
	private ParsedData $parsedData;

	public function __construct() {
		$this->mapper = new RegexMatchesMapper();
	}

	public function parse( string $input ): self {
		$input = $this->removeExtraSpaces( $input );

		if ( "" === $input ) {
			throw new InvalidArgumentException( "Can't create EDTF from empty string" );
		}

		$input = strtoupper( $input );
		$this->input = $input;

		preg_match( $this->getRegexPattern(), $input, $matches );

		$this->parsedData = $this->mapper->mapMatchesToObject( $matches );

		$this->matches = $matches;

		$validator = new ParserValidator( $this );
		if ( !$validator->isValid() ) {
			throw new InvalidArgumentException( $validator->getMessages() );
		}
		return $this;
	}

	private function getRegexPattern(): string {
		if ( self::$regexPattern === '' ) {
			self::$regexPattern = '/' . file_get_contents( __DIR__ . '/../../../config/regex.txt' ) . '/';
		}

		return self::$regexPattern;
	}

	public function getParsedData(): ParsedData {
		return $this->parsedData;
	}

	/**
	 * @throws InvalidArgumentException
	 */
	public function createEdtf( string $input ): EdtfValue {
		if ( false !== strpos( $input, '/' ) ) {
			return $this->buildInterval( $input );
		} elseif ( false !== strpos( $input, '{' ) || false !== strpos( $input, '[' ) ) {
			return $this->buildSet( $input );
		}

		$this->parse( $input );

		$date = $this->parsedData->getDate();
		$time = $this->parsedData->getTime();

		if ( $time->getHour() !== null ) {
			return $this->buildDateTime();
		} elseif ( $date->getYearSignificantDigit() !== null ) {
			return $this->createSignificantDigitInterval();
		} elseif ( $date->getSeason() !== 0 ) {
			return $this->buildSeason();
		}
		return $this->buildDate();
	}

	private function buildDate(): ExtDate {
		$date = $this->parsedData->getDate();

		return new ExtDate(
			$date->getYearNum(),
			$date->getMonthNum(),
			$date->getDayNum(),
			$this->buildQualification(),
			$this->buildUnspecifiedDigit()
		);
	}

	private function buildUnspecifiedDigit(): UnspecifiedDigit {
		$date = $this->parsedData->getDate();

		return new UnspecifiedDigit(
			$date->getRawYear(),
			$date->getRawMonth(),
			$date->getRawDay()
		);
	}

	private function buildDateTime(): ExtDateTime {
		$timezone = $this->parsedData->getTimezone();
		$date = $this->parsedData->getDate();
		$time = $this->parsedData->getTime();

		$tzSign = "Z" == $timezone->getTzUtc() ? "Z" : $timezone->getTzSign();

		return new ExtDateTime(
			new ExtDate(
				$date->getYearNum(),
				$date->getMonthNum(),
				$date->getDayNum()
			),
			$time->getHour(),
			$time->getMinute(),
			$time->getSecond(),
			$tzSign,
			$timezone->getTzHour(),
			$timezone->getTzMinute()
		);
	}

	private function buildSeason(): Season {
		$date = $this->parsedData->getDate();
		return new Season( $date->getYearNum(), $date->getSeason() );
	}

	private function buildQualification(): Qualification {
		// TODO: use fields directly

		$qualification = $this->parsedData->getQualification();

		$year = Qualification::UNDEFINED;
		$month = Qualification::UNDEFINED;
		$day = Qualification::UNDEFINED;

		if ( !is_null( $qualification->getYearCloseFlag() )
			|| !is_null( $qualification->getMonthCloseFlag() )
			|| !is_null( $qualification->getDayCloseFlag() )
		) {
			$includeYear = false;
			$includeMonth = false;
			$includeDay = false;
			$q = Qualification::UNDEFINED;

			if ( !is_null( $qualification->getYearCloseFlag() ) ) {
				// applied only to year
				$includeYear = true;
				$q = self::genQualificationValue( $qualification->getYearCloseFlag() );
			} elseif ( !is_null( $qualification->getMonthCloseFlag() ) ) {
				// applied only to year, and month
				$includeYear = true;
				$includeMonth = true;
				$q = self::genQualificationValue( $qualification->getMonthCloseFlag() );
			} elseif ( !is_null( $qualification->getDayCloseFlag() ) ) {
				// applied to year, month, and day
				$includeYear = true;
				$includeMonth = true;
				$includeDay = true;
				$q = self::genQualificationValue( $qualification->getDayCloseFlag() );
			}

			$year = $includeYear ? $q : $year;
			$month = $includeMonth ? $q : $month;
			$day = $includeDay ? $q : $day;
		}

		// handle level 2 qualification
		if ( !is_null( $qualification->getYearOpenFlag() ) ) {
			$year = self::genQualificationValue( $qualification->getYearOpenFlag() );
		}
		if ( !is_null( $qualification->getMonthOpenFlag() ) ) {
			$month = self::genQualificationValue( $qualification->getMonthOpenFlag() );
		}
		if ( !is_null( $qualification->getDayOpenFlag() ) ) {
			$day = self::genQualificationValue( $qualification->getDayOpenFlag() );
		}
		return new Qualification( $year, $month, $day );
	}

	// TODO
	private static array $map = [
		'%' => Qualification::UNCERTAIN_AND_APPROXIMATE,
		'?' => Qualification::UNCERTAIN,
		'~' => Qualification::APPROXIMATE,
	];

	// TODO
	private static function genQualificationValue( ?string $flag = null ): int {
		assert( is_string( $flag ) );
		return (int)self::$map[$flag];
	}

	private function buildSet( string $input ): Set {
		$input = $this->removeExtraSpaces( $input );

		preg_match(
			"/(?x)
					 ^(?<openFlag>[\[|\{])
					 (?<value>.*)
					 (?<closeFlag>[\]|\}])$
					/",
			$input,
			$matches
		);
		if ( 0 === count( $matches ) ) {
			throw new InvalidArgumentException(
				sprintf(
					"Can't create Set from '%s' input",
					$input
				)
			);
		}

		$openFlag = $matches['openFlag'];
		$values = explode( ",", $matches['value'] );
		$allMembers = '[' === $openFlag ? false : true;
		$earlier = false;
		$later = false;

		$sets = [];
		foreach ( $values as $value ) {
			if ( false === strpos( $value, '..' ) ) {
				$sets[] = ( new Parser() )->createEdtf( $value );
			} elseif ( false != preg_match( '/^\.\.(.+)/', $value, $matches ) ) {
				// earlier date like ..1760-12-03
				$earlier = true;
				$sets[] = ( new Parser() )->createEdtf( $matches[1] );
			} elseif ( false != preg_match( '/(.+)\.\.$/', $value, $matches ) ) {
				// later date like 1760-12..
				$later = true;
				$sets[] = ( new Parser() )->createEdtf( $matches[1] );
			} elseif ( false != preg_match( '/(.+)\.\.(.+)/', $value, $matches ) ) {
				/** @var ExtDate $fromExtDate */
				$fromExtDate = ( new Parser() )->createEdtf( $matches[1] );
				if ( $this->isInvalidOpenMiddleSetPart( $fromExtDate ) ) {
					throw new InvalidArgumentException( "String $matches[1] is not valid to build a set" );
				}

				/** @var ExtDate $toExtDate */
				$toExtDate = ( new Parser() )->createEdtf( $matches[2] );
				if ( $this->isInvalidOpenMiddleSetPart( $toExtDate ) ) {
					throw new InvalidArgumentException( "String $matches[2] is not valid to build a set" );
				}

				if ( $fromExtDate->precision() !== $toExtDate->precision() ) {
					throw new InvalidArgumentException(
						"Unable to build a set. All input elements should have the same precision"
					);
				}

				$precision = $fromExtDate->precision();

				if ( $precision === ExtDate::PRECISION_MONTH ) {
					$sets = array_merge( $sets, $this->resolveSetValuesForMonthPrecision( $fromExtDate, $toExtDate ) );
				} elseif ( $precision === ExtDate::PRECISION_YEAR ) {
					$sets = array_merge( $sets, $this->resolveSetValuesForYearPrecision( $fromExtDate, $toExtDate ) );
				} elseif ( $precision === ExtDate::PRECISION_DAY ) {
					$sets = array_merge( $sets, $this->resolveSetValuesForDayPrecision( $fromExtDate, $toExtDate ) );
				}
			}
			continue;
		}

		return new Set( $sets, $allMembers, $earlier, $later );
	}

	private function isInvalidOpenMiddleSetPart( $part ): bool {
		return !$part instanceof ExtDate
			|| $part->uncertain()
			|| $part->approximate();
	}

	private function resolveSetValuesForYearPrecision( ExtDate $progressionStart, ExtDate $progressionEnd ): array {
		$values = [];
		for ( $i = $progressionStart->getYear(); $i <= $progressionEnd->getYear(); $i++ ) {
			$values[] = new ExtDate( $i );
		}

		return $values;
	}

	private function resolveSetValuesForMonthPrecision( ExtDate $progressionStart, ExtDate $progressionEnd ): array {
		$yearTurnsLeft = $progressionEnd->getYear() - $progressionStart->getYear();
		return $this->monthRecursion( $yearTurnsLeft, $progressionStart, $progressionEnd );
	}

	private function resolveSetValuesForDayPrecisionWithinAYear( ExtDate $progressionStart, ExtDate $progressionEnd ) {
		$monthTurnsLeft = $progressionEnd->getMonth() - $progressionStart->getMonth();
		return $this->dayRecursion( $monthTurnsLeft, $progressionStart, $progressionEnd );
	}

	private function resolveSetValuesForDayPrecision( ExtDate $progressionStart, ExtDate $progressionEnd ): array {
		$monthTurnsLeft = $progressionEnd->getMonth() - $progressionStart->getMonth();
		$yearTurnsLeft = $progressionEnd->getYear() - $progressionStart->getYear();

		$values = [];
		while ( $yearTurnsLeft > 0 ) {
			$currentYear = $progressionStart->getYear();
			$values = array_merge(
				$values,
				$this->resolveSetValuesForDayPrecisionWithinAYear(
					$progressionStart,
					new ExtDate( $currentYear, 12, 31 )
				)
			);
			$progressionStart = new ExtDate( ++$currentYear, 1, 1 );
			$yearTurnsLeft--;
		}

		return array_merge( $values, $this->dayRecursion( $monthTurnsLeft, $progressionStart, $progressionEnd ) );
	}

	private function dayRecursion( int $monthTurnsLeft, ExtDate $progressionStart, ExtDate $progressionEnd ): array {
		$values = [];

		$currentYear = $progressionStart->getYear();
		$currentMonth = $progressionStart->getMonth();

		if ( $monthTurnsLeft === 0 ) {
			$limit = $progressionEnd->getDay();
		} else {
			$limit = Carbon::create( $currentYear, $currentMonth )->lastOfMonth()->day;
		}

		for ( $i = $progressionStart->getDay(); $i <= $limit; $i++ ) {
			$values[] = new ExtDate( $currentYear, $currentMonth, $i );
		}

		if ( $monthTurnsLeft === 0 ) {
			return $values;
		}

		$monthTurnsLeft--;

		return array_merge(
			$values,
			$this->dayRecursion( $monthTurnsLeft, new ExtDate( $currentYear, ++$currentMonth, 1 ), $progressionEnd )
		);
	}

	private function monthRecursion( int $yearTurnsLeft, ExtDate $progressionStart, ExtDate $progressionEnd ): array {
		$values = [];
		$currentYear = $progressionStart->getYear();
		if ( $yearTurnsLeft === 0 ) {
			$limit = $progressionEnd->getMonth();
		} else {
			$limit = 12;
		}

		for ( $i = $progressionStart->getMonth(); $i <= $limit; $i++ ) {
			$values[] = new ExtDate( $currentYear, $i );
		}

		if ( $yearTurnsLeft === 0 ) {
			return $values;
		}
		$yearTurnsLeft--;

		return array_merge(
			$values,
			$this->monthRecursion( $yearTurnsLeft, new ExtDate( ++$currentYear, 1 ), $progressionEnd )
		);
	}

	private function buildInterval( string $input ): Interval {
		$pos = strrpos( $input, '/' );

		if ( false === $pos ) {
			throw new InvalidArgumentException(
				sprintf( "Can't create interval from %s", $input )
			);
		}
		$startDateStr = substr( $input, 0, $pos );
		$endDateStr = substr( $input, $pos + 1 );

		return new Interval(
			$this->buildDateUsingIntervalMode( $startDateStr ),
			$this->buildDateUsingIntervalMode( $endDateStr )
		);
	}

	private function buildDateUsingIntervalMode( string $dateString ): IntervalSide {
		if ( $dateString === '..' ) {
			return IntervalSide::newOpenInterval();
		}

		if ( $dateString === '' ) {
			return IntervalSide::newUnknownInterval();
		}

		$parser = new Parser();
		$parser->parse( $dateString );

		$date = $parser->getParsedData()->getDate();

		if ( $date->getSeason() !== 0 ) {
			return IntervalSide::newFromDate( $parser->buildSeason() );
		}

		return IntervalSide::newFromDate( $parser->buildDate() );
	}

	public function createSignificantDigitInterval(): Interval {
		$date = $this->parsedData->getDate();
		$strEstimated = (string)$date->getYearNum();
		$significantDigit = $date->getYearSignificantDigit();
		assert( is_int( $significantDigit ) );
		$year = substr( $strEstimated, 0, strlen( $strEstimated ) - $significantDigit );
		$startYear = $year . ( str_repeat( "0", $significantDigit ) );
		$endYear = $year . ( str_repeat( "9", $significantDigit ) );

		return new Interval(
			IntervalSide::newFromDate( new ExtDate( (int)$startYear ) ),
			IntervalSide::newFromDate( new ExtDate( (int)$endYear ) ),
			$significantDigit,
			$date->getYearNum()
		);
	}

	public function getMatches(): array {
		return $this->matches;
	}

	public function getInput(): string {
		return $this->input;
	}

	private function removeExtraSpaces( string $input ): string {
		return str_replace( " ", "", $input );
	}
}