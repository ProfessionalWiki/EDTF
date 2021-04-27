<?php

declare( strict_types = 1 );

namespace EDTF\Model;

use EDTF\EdtfValue;
use EDTF\Model\SetElement\SingleDateSetElement;
use EDTF\PackagePrivate\CoversTrait;

class Set implements EdtfValue {
	use CoversTrait;

	/**
	 * @var array<int, SetElement>
	 */
	private array $elements = [];
	private bool $isAllMembers;

	/**
	 * @param array<int, SetElement|ExtDate|Season> $setElements
	 */
	public static function newAllMembersSet( array $setElements ): self {
		return new self( $setElements, true );
	}

	/**
	 * @param array<int, SetElement|ExtDate|Season> $setElements
	 */
	public static function newOneMemberSet( array $setElements ): self {
		return new self( $setElements, false );
	}

	/**
	 * @param array<int, SetElement|ExtDate|Season> $setElements
	 * @param bool $isAllMembers
	 */
	public function __construct(
		array $setElements,
		bool $isAllMembers
	) {
		foreach ( $setElements as $element ) {
			$this->elements[] = $this->datesToSetElements( $element );
		}

		$this->isAllMembers = $isAllMembers;
	}

	/**
	 * @param SetElement|ExtDate|Season $elementOrDate
	 */
	private function datesToSetElements( $elementOrDate ): SetElement {
		if ( $elementOrDate instanceof SetElement ) {
			return $elementOrDate;
		}

		return new SingleDateSetElement( $elementOrDate );
	}

	public function isAllMembers(): bool {
		return $this->isAllMembers;
	}

	/**
	 * @return array<int, SetElement>
	 */
	public function getElements(): array {
		return $this->elements;
	}

	public function isEmpty(): bool {
		return $this->elements === [];
	}

	/**
	 * Computes all dates contained in the set. Ranges are expanded according to their precision.
	 * This list can be very big, for instance when the set contains 1000-01-01..2000-12-30.
	 * @return array<int, ExtDate|Season>
	 */
	public function getDates(): array {
		$dates = [];

		foreach ( $this->elements as $element ) {
			foreach ( $element->getAllDates() as $date ) {
				$dates[] = $date;
			}
		}

		return $dates;
	}

	public function getMax(): int {
		return max(
			array_map(
				fn ( SetElement $element ) => $element->getMaxAsUnixTimestamp(),
				$this->elements
			)
		);
	}

	public function getMin(): int {
		return min(
			array_map(
				fn ( SetElement $element ) => $element->getMinAsUnixTimestamp(),
				$this->elements
			)
		);
	}

}