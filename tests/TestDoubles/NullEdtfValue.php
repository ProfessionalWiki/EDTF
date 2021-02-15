<?php

declare( strict_types = 1 );

namespace EDTF\Tests\TestDoubles;

use EDTF\EdtfValue;

class NullEdtfValue implements EdtfValue {

	public function getMax(): int {
		return 0;
	}

	public function getMin(): int {
		return 0;
	}

	public function covers( EdtfValue $edtf ): bool {
		return false;
	}

}
