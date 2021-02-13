<?php

declare( strict_types = 1 );

namespace EDTF;

interface Humanizer {

	/**
	 * Returns a natural language version of the EDTF value,
	 * or an empty string if the EDTF value is not supported.
	 */
	public function humanize( EdtfValue $edtf ): string;

}
