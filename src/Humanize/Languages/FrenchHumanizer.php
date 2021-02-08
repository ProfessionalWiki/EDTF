<?php

declare( strict_types = 1 );

namespace EDTF\Humanize\Languages;

use EDTF\EdtfValue;
use EDTF\Humanize\Humanizer;
use EDTF\Season;

class FrenchHumanizer implements Humanizer {

	private const SEASON_MAP = [
		21 => 'Printemps',
		22 => 'Été',
		23 => 'Automne',
		24 => 'Hiver',
	];

	public function humanize( EdtfValue $edtf ): string {
		if ( $edtf instanceof Season ) {
			return self::SEASON_MAP[$edtf->getSeason()] . ' ' . $edtf->getYear();
		}

		return 'TODO';
	}

}
