<?php

declare( strict_types = 1 );

namespace EDTF\Humanize\Languages;

use EDTF\EdtfValue;
use EDTF\Humanize\Humanizer;
use EDTF\Season;

class EnglishHumanizer implements Humanizer {

	public function humanize( EdtfValue $edtf ): string {
		if ( $edtf instanceof Season ) {
			return $edtf->getName() . ' ' . $edtf->getYear();
		}

		return 'TODO';
	}

}
