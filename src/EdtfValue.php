<?php

declare( strict_types = 1 );

namespace EDTF;

interface EdtfValue {

	/**
	 * @return int unix timestamp
	 */
	public function getMax(): int;

	/**
	 * @return int unix timestamp
	 */
	public function getMin(): int;

	public function covers( EdtfValue $edtf ): bool;

}