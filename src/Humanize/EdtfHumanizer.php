<?php

declare( strict_types = 1 );

namespace EDTF\Humanize;

use EDTF\EdtfValue;
use EDTF\Season;

class EdtfHumanizer {

	public function humanize( EdtfValue $edtf ): string {
		if ( $edtf instanceof Season ) {
			return $edtf->getName() . ' ' . $edtf->getYear();
		}

		return 'TODO';
	}

}
