<?php

declare( strict_types = 1 );

namespace EDTF\Humanize;

use EDTF\EdtfValue;

interface Humanizer {

	public function humanize( EdtfValue $edtf ): string;

}
