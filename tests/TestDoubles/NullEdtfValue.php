<?php

declare( strict_types = 1 );

namespace EDTF\Tests\TestDoubles;

use EDTF\Contracts\Coverable;
use EDTF\Model\EdtfValue;

class NullEdtfValue extends EdtfValue implements Coverable {

	public function getMax(): int {
		return 0;
	}

	public function getMin(): int {
		return 0;
	}

	public function covers( Coverable $edtf ): bool {
		return false;
	}

}
