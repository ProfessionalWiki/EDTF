<?php

declare( strict_types = 1 );

namespace EDTF\PackagePrivate\Parser;

use EDTF\EdtfValue;
use EDTF\Model\ExtDate;
use EDTF\Model\ExtDateTime;
use EDTF\Model\Interval;
use EDTF\Model\IntervalSide;
use EDTF\Model\Qualification;
use EDTF\Model\Season;
use EDTF\Model\Set;
use EDTF\Model\SetElement;
use EDTF\Model\SetElement\OpenSetElement;
use EDTF\Model\SetElement\RangeSetElement;
use EDTF\Model\SetElement\SingleDateSetElement;
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

		if ( $input === '' || $input === '?' ) {
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

		if ( $matches === [] ) {
			throw new InvalidArgumentException(
				sprintf(
					"Can't create Set from '%s' input",
					$input
				)
			);
		}

		return new Set(
			array_map(
				fn ( string $value ) =>  $this->setValueToElement( $value ),
				explode( ',', $matches['value'] )
			),
			$matches['openFlag'] === '{'
		);
	}

	private function setValueToElement( string $value ): SetElement {
		if ( !str_contains( $value, '..' ) ) {
			return new SingleDateSetElement( $this->parseSetValue( $value ) );
		}

		// ..1760-12-03
		if ( preg_match( '/^\.\.(.+)/', $value, $matches ) ) {
			return new OpenSetElement( $this->parseSetValue( $matches[1] ), false );
		}

		// 1760-12-03..
		if ( preg_match( '/(.+)\.\.$/', $value, $matches ) ) {
			return new OpenSetElement( $this->parseSetValue( $matches[1] ), true );
		}

		// 2000..2021
		if ( preg_match( '/(.+)\.\.(.+)/', $value, $matches ) ) {
			return new RangeSetElement(
				$this->parseSetRangeValue( $matches[1] ),
				$this->parseSetRangeValue( $matches[2] )
			);
		}

		throw new InvalidArgumentException( 'Invalid set element' );
	}

	private function parseSetRangeValue( string $setValue ): ExtDate {
		$date = ( new Parser() )->createEdtf( $setValue);

		if ( $date instanceof ExtDate ) {
			if ( $date->uncertain() ) {
				throw new InvalidArgumentException( 'Dates in set ranges cannot be uncertain' );
			}

			if ( $date->approximate() ) {
				throw new InvalidArgumentException( 'Dates in set ranges cannot be approximate' );
			}

			return $date;
		}

		throw new InvalidArgumentException( 'Ranges in sets can only contain dates' );
	}

	/**
	 * @return ExtDate|Season
	 */
	private function parseSetValue( string $setValue ) {
		$date = ( new Parser() )->createEdtf( $setValue );

		if ( $date instanceof ExtDate || $date instanceof Season ) {
			return $date;
		}

		throw new InvalidArgumentException( 'Sets can only contain dates (without time) and seasons' );
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