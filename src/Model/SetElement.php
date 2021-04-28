<?php

declare( strict_types = 1 );

namespace EDTF\Model;

interface SetElement {

	/**
	 * @return array<int, ExtDate|Season>
	 */
	public function getAllDates(): array;

	public function getMinAsUnixTimestamp(): int;
	public function getMaxAsUnixTimestamp(): int;

}
