<?php

declare( strict_types = 1 );

namespace EDTF\PackagePrivate;

use Carbon\Carbon;
use EDTF\EdtfValue;

/**
 * TODO: this could be an object or service used inside of SpecificEdtfType::covers()
 */
trait CoversTrait {

	public function covers( EdtfValue $edtf ): bool {
		$min = Carbon::createFromTimestamp( $this->getMin() );
		$max = Carbon::createFromTimestamp( $this->getMax() );

		$edtfMin = Carbon::createFromTimestamp( $edtf->getMin() );
		$edtfMax = Carbon::createFromTimestamp( $edtf->getMax() );
		return $edtfMin->isBetween( $min, $max, true ) || $edtfMax->isBetween( $min, $max, true );
	}
}