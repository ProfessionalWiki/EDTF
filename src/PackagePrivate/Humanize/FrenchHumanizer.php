<?php

declare( strict_types = 1 );

namespace EDTF\PackagePrivate\Humanize;

use EDTF\EdtfValue;
use EDTF\Humanizer;
use EDTF\Season;

class FrenchHumanizer implements Humanizer {

	private const SEASON_MAP = [
		21 => 'Printemps',
		22 => 'Été',
		23 => 'Automne',
		24 => 'Hiver',
		25 => 'Printemps (Hémisphère nord)',
		26 => 'Été (Hémisphère nord)',
		27 => 'Automne (Hémisphère nord)',
		28 => 'Hiver (Hémisphère nord)',
		29 => 'Printemps (Hémisphère sud)',
		30 => 'Été (Hémisphère sud)',
		31 => 'Automne (Hémisphère sud)',
		32 => 'Hiver (Hémisphère sud)',
		33 => 'Trimestre 1',
		34 => 'Trimestre 2',
		35 => 'Trimestre 3',
		36 => 'Trimestre 4',
		37 => 'Quadrimester 1',
		38 => 'Quadrimestre 2',
		39 => 'Quadrimestre 3',
		40 => 'Semestre 1',
		41 => 'Semestre 2',
	];

	public function humanize( EdtfValue $edtf ): string {
		if ( $edtf instanceof Season ) {
			return $this->humanizeSeason( $edtf );
		}

		return '';
	}

	private function humanizeSeason( Season $season ): string {
		return self::SEASON_MAP[$season->getSeason()] . ' ' . $season->getYear();
	}

}
