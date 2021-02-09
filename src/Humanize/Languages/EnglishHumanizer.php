<?php

declare( strict_types = 1 );

namespace EDTF\Humanize\Languages;

use EDTF\EdtfValue;
use EDTF\ExtDate;
use EDTF\Humanize\Humanizer;
use EDTF\Interval;
use EDTF\Season;

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
		33 => 'Quarter 1',
		34 => 'Quarter 2',
		35 => 'Quarter 3',
		36 => 'Quarter 4',
		37 => 'Quadrimester 1',
		38 => 'Quadrimester 2',
		39 => 'Quadrimester 3',
		40 => 'Semester 1',
		41 => 'Semester 2',
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
		$year = $date->getYear();
		$month = $date->getMonth();
		$day = $date->getDay();

		if ( $year !== null && $month !== null && $day !== null ) {
			return self::MONTH_MAP[$month] . ' ' . $this->inflectNumber( $day ) . ', ' . $year;
		}

		if ( $year !== null && $month === null && $day !== null ) {
			return $this->inflectNumber( $day ) . ' of unknown month, ' . $year;
		}

		$parts = [];

		if ( $month !== null ) {
			$parts[] = self::MONTH_MAP[$month];
		}

		if ( $day !== null ) {
			$parts[] = $this->inflectNumber( $day );
		}

		if ( $year !== null ) {
			$parts[] = (string)$year;
		}

		return implode( ' ', $parts );
	}

	private function inflectNumber(int $number): string {
		if ( $number % 100 >= 11 && $number % 100 <= 13 ) {
			return $number. 'th';
		}

		return $number . [ 'th','st','nd','rd','th','th','th','th','th','th' ][$number % 10];
	}

	private function humanizeInterval( Interval $interval ): string {
		// TODO
//		if ( !$interval->isNormalInterval() ) {
//			return '';
//		}

		return $this->humanize( $interval->getStart() ) . ' to ' . $this->humanize( $interval->getEnd() );
	}

}
