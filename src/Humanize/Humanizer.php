<?php

declare( strict_types = 1 );

namespace EDTF\Humanize;

use EDTF\EdtfValue;

interface Humanizer {

	/**
	 * Returns a natural language version of the EDTF value,
	 * or an empty string if the EDTF value is not supported.
	 */
	public function humanize( EdtfValue $edtf ): string;

}
