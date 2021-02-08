<?php

declare( strict_types = 1 );

namespace EDTF\Humanize\Languages;

use EDTF\EdtfValue;
use EDTF\Humanize\Humanizer;
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

	public function humanize( EdtfValue $edtf ): string {
		if ( $edtf instanceof Season ) {
			return $this->humanizeSeason( $edtf );
		}

		return 'TODO';
	}

	private function humanizeSeason( Season $season ): string {
		return self::SEASON_MAP[$season->getSeason()] . ' ' . $season->getYear();
	}

}
