<?php

declare( strict_types = 1 );

namespace EDTF;

/**
 * Humanizer that returns a single string. Does not support sets.
 * If you want set support, use the more complex  @see StructuredHumanizer.
 */
interface Humanizer {

	/**
	 * Returns a natural language version of the EDTF value,
	 * or an empty string if the EDTF value is not supported.
	 */
	public function humanize( EdtfValue $edtf ): string;

}
