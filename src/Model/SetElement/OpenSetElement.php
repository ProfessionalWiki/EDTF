<?php

declare( strict_types = 1 );

namespace EDTF\Model\SetElement;

use EDTF\Model\ExtDate;
use EDTF\Model\Season;
use EDTF\Model\SetElement;

/**
 * 2021.. and ..2021
 */
class OpenSetElement implements SetElement {

	/**
	 * @var ExtDate|Season
	 */
	private $edtf;
	private bool $isOpenEnd;

	/**
	 * @param ExtDate|Season $edtf
	 * @param bool $isOpenEnd
	 */
	public function __construct( $edtf, bool $isOpenEnd ) {
		$this->edtf = $edtf;
		$this->isOpenEnd = $isOpenEnd;
	}

	/**
	 * @return ExtDate|Season
	 */
	public function getDate() {
		return $this->edtf;
	}

	public function isOpenEnd(): bool {
		return $this->isOpenEnd;
	}

	/**
	 * @return array<int, ExtDate|Season>
	 */
	public function getAllDates(): array {
		return [ $this->edtf ];
	}

	public function getMinAsUnixTimestamp(): int {
		if ( $this->isOpenEnd ) {
			// 2020..
			// -1000..
			return $this->edtf->getMin();
		}

		// ..2020
		// ..-1000
		return 0; // FIXME: maybe return null
	}

	public function getMaxAsUnixTimestamp(): int {
		if ( $this->isOpenEnd ) {
			// 2020..
			// -1000..
			return 0; // FIXME: maybe return null
		}

		// ..2020
		// ..-1000
		return $this->edtf->getMax();
	}

}
