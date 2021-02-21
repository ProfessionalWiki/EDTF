<?php

declare( strict_types = 1 );

namespace EDTF\PackagePrivate\Humanizer;

use EDTF\EdtfValue;
use EDTF\Humanizer;
use EDTF\Model\ExtDate;
use EDTF\Model\ExtDateTime;
use EDTF\Model\Interval;
use EDTF\Model\Season;
use EDTF\Model\UnspecifiedDigit;

class EnglishHumanizer implements Humanizer {

	private const SEASON_MAP = [
		21 => 'Spring',
		22 => 'Summer',
		23 => 'Autumn',
		24 => 'Winter',
		25 => 'Spring (Northern Hemisphere)',
		26 => 'Summer (Northern Hemisphere)',
		27 => 'Autumn (Northern Hemisphere)',
		28 => 'Winter (Northern Hemisphere)',
		29 => 'Spring (Southern Hemisphere)',
		30 => 'Summer (Southern Hemisphere)',
		31 => 'Autumn (Southern Hemisphere)',
		32 => 'Winter (Southern Hemisphere)',
		33 => 'First quarter',
		34 => 'Second quarter',
		35 => 'Third quarter',
		36 => 'Fourth quarter',
		37 => 'First quadrimester',
		38 => 'Second quadrimester',
		39 => 'Third quadrimester',
		40 => 'First semester',
		41 => 'Second semester',
	];

	private const MONTH_MAP = [
		1 => 'January',
		2 => 'February',
		3 => 'March',
		4 => 'April',
		5 => 'May',
		6 => 'June',
		7 => 'July',
		8 => 'August',
		9 => 'September',
		10 => 'October',
		11 => 'November',
		12 => 'December',
	];

	public function humanize( EdtfValue $edtf ): string {
		if ( $edtf instanceof ExtDate ) {
			return $this->humanizeDate( $edtf );
		}

		if ( $edtf instanceof ExtDateTime ) {
			return $this->humanizeDateTime( $edtf );
		}

		if ( $edtf instanceof Season ) {
			return $this->humanizeSeason( $edtf );
		}

		if ( $edtf instanceof Interval ) {
			return $this->humanizeInterval( $edtf );
		}

		return '';
	}

	private function humanizeSeason( Season $season ): string {
		return self::SEASON_MAP[$season->getSeason()] . ' ' . $season->getYear();
	}

	private function humanizeDate( ExtDate $date ): string {
		$humanizedDate = $this->humanizeDateWithoutUncertainty( $date );

		if ( $date->getQualification()->isApproximate() && $date->getQualification()->uncertain() ) {
			return 'Maybe circa ' . $humanizedDate;
		}

		if ( $date->getQualification()->isApproximate() ) {
			return 'Circa ' . $humanizedDate;
		}

		if ( $date->getQualification()->uncertain() ) {
			return 'Maybe ' . $humanizedDate;
		}

		return $humanizedDate;
	}


	private function humanizeDateWithoutUncertainty( ExtDate $date ): string {
		$year = $date->getYear();
		$month = $date->getMonth();
		$day = $date->getDay();

		if ( $year !== null ) {
			$year = $this->humanizeYear(
				$year,
				$date->getUnspecifiedDigit()
			);
		}

		if ( $month !== null ) {
			$month = self::MONTH_MAP[$month];
		}

		if ( $day !== null ) {
			$day = $this->inflectNumber( $day );
		}

		return $this->humanizeYearMonthDay( $year, $month, $day );
	}

	private function humanizeYearMonthDay( ?string $year, ?string $month, ?string $day ): string {
	    if ( $year !== null && $month !== null && $day !== null ) {
			return $month . ' ' . $day . ', ' . $year;
		}

		if ( $year !== null && $month === null && $day !== null ) {
			return $day . ' of unknown month, ' . $year;
		}

		return implode(
			' ',
			array_filter( [ $month, $day, $year ], 'is_string' )
		);
	}

	private function humanizeYear( int $year, UnspecifiedDigit $unspecifiedDigit ): string
    {
	    $endingChar = $this->needsYearEndingChar($unspecifiedDigit) ? 's' : '';

		return $year >= 0 ? (string)$year . $endingChar : (string)(-$year) . ' BC';
	}

    /**
     * Check, do we need to add 's' char to humanized year representation
     * This can be applicable to unspecified years i.e. 197X or 19XX
     */
	private function needsYearEndingChar(UnspecifiedDigit $unspecifiedDigit): bool
    {
        return $unspecifiedDigit->century() || $unspecifiedDigit->decade();
    }

	private function inflectNumber(int $number): string {
		if ( $number % 100 >= 11 && $number % 100 <= 13 ) {
			return $number. 'th';
		}

		return $number . [ 'th','st','nd','rd','th','th','th','th','th','th' ][$number % 10];
	}

	private function humanizeInterval( Interval $interval ): string {
		if ( $interval->isNormalInterval() ) {
			return $this->humanize( $interval->getStartDate() ) . ' to ' . $this->humanize( $interval->getEndDate() );
		}

		if ( $interval->hasOpenEnd() ) {
			return $this->humanize( $interval->getStartDate() ) . ' or later';
		}

		if ( $interval->hasOpenStart() ) {
			return $this->humanize( $interval->getEndDate() ) . ' or earlier';
		}

		if ( $interval->hasUnknownEnd() ) {
			return 'From ' . $this->humanize( $interval->getStartDate() ) . ' to unknown';
		}

		if ( $interval->hasUnknownStart() ) {
			return 'From unknown to ' . $this->humanize( $interval->getEndDate() );
		}

		return '';
	}

	private function humanizeDateTime( ExtDateTime $dateTime ): string {
		return sprintf("%02d:%02d:%02d", $dateTime->getHour(), $dateTime->getMinute(), $dateTime->getSecond() )
			. ' ' . $this->humanizeTimeZoneOffset( $dateTime->getTimezoneOffset() )
			. ' ' . $this->humanizeDate( $dateTime->getDate() );
	}

	private function humanizeTimeZoneOffset( ?int $offsetInMinutes ): string {
		if ( $offsetInMinutes === null ) {
			return '(local time)';
		}

		if ( $offsetInMinutes === 0 ) {
			return 'UTC';
		}

		return 'UTC'
			. ( $offsetInMinutes < 0 ? '-' : '+' )
			. (string)floor( abs( $offsetInMinutes ) / 60 )
			. ( $offsetInMinutes % 60 === 0 ? '' : sprintf(":%02d", abs( $offsetInMinutes ) % 60 ) );
	}

}
