<?php

declare( strict_types = 1 );

namespace EDTF\Model\SetElement;

use EDTF\Model\ExtDate;
use EDTF\Model\Season;
use EDTF\Model\SetElement;

class SingleDateSetElement implements SetElement {

	/**
	 * @var ExtDate|Season
	 */
	private $edtf;

	/**
	 * @param ExtDate|Season $edtf
	 */
	public function __construct( $edtf ) {
		$this->edtf = $edtf;
	}

	/**
	 * @return ExtDate|Season
	 */
	public function getDate() {
		return $this->edtf;
	}

	/**
	 * @return array<int, ExtDate|Season>
	 */
	public function getAllDates(): array {
		return [ $this->edtf ];
	}

	public function getMinAsUnixTimestamp(): int {
		return $this->edtf->getMin();
	}

	public function getMaxAsUnixTimestamp(): int {
		return $this->edtf->getMax();
	}

}
