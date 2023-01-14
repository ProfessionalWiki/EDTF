<?php

declare( strict_types = 1 );

namespace EDTF\Model;

class Qualification {

	public const KNOWN = 0;

	/**
	 * Determines if a date part qualification is uncertain,
	 * specified by using "?" sign
	 */
	public const UNCERTAIN = 1;

	/**
	 * Determines if a date part qualification is approximate
	 * specified by using "~" sign
	 */
	public const APPROXIMATE = 2;

	/**
	 * Determines if a date part qualification is uncertain and approximate
	 * specified by using "%" flag
	 */
	public const UNCERTAIN_AND_APPROXIMATE = 3;

	private int $year;
	private int $month;
	private int $day;

	private array $uncertainParts = [];
	private array $approximateParts = [];
	private array $uncertainAndApproximateParts = [];

	public function __construct( int $year = 0, int $month = 0, int $day = 0 ) {
		// TODO: Does it make sense these constructor params are optional?
		$this->year = $year;
		$this->month = $month;
		$this->day = $day;

		$this->setUncertainty();
	}

	/**
	 * TODO: remove
	 */
	private function setUncertainty(): void {
		foreach ( [ 'year' => $this->year, 'month' => $this->month, 'day' => $this->day ]
			as $part => $value ) {

			switch( $value ) {
				case self::KNOWN:
					break;
				case self::UNCERTAIN :
					$this->uncertainParts[] = $part;
					break;
				case self::APPROXIMATE :
					$this->approximateParts[] = $part;
					break;
				case self::UNCERTAIN_AND_APPROXIMATE :
					$this->uncertainAndApproximateParts[] = $part;
					break;
			}
		}
	}

	/**
	 * TODO: remove
	 * @return string[]
	 */
	public function getUncertainParts(): array {
		return $this->uncertainParts;
	}

	/**
	 * TODO: remove
	 * @return string[]
	 */
	public function getApproximateParts(): array {
		return $this->approximateParts;
	}

	/**
	 * TODO: remove
	 * @return string[]
	 */
	public function getUncertainAndApproximateParts(): array {
		return $this->uncertainAndApproximateParts;
	}

	public function isFullyKnown(): bool {
		return $this->year === self::KNOWN
			&& $this->month === self::KNOWN
			&& $this->day === self::KNOWN;
	}

	/**
	 * Returns if ANY part is uncertain
	 */
	public function isUncertain(): bool {
		return $this->dayIsUncertain()
			|| $this->monthIsUncertain()
			|| $this->yearIsUncertain();
	}

	public function dayIsUncertain(): bool {
		return $this->day === self::UNCERTAIN
			|| $this->day === self::UNCERTAIN_AND_APPROXIMATE;
	}

	public function monthIsUncertain(): bool {
		return $this->month === self::UNCERTAIN
			|| $this->month === self::UNCERTAIN_AND_APPROXIMATE;
	}

	public function yearIsUncertain(): bool {
		return $this->year === self::UNCERTAIN
			|| $this->year === self::UNCERTAIN_AND_APPROXIMATE;
	}

	/**
	 * Returns if ANY part is approximate
	 */
	public function isApproximate(): bool {
		return $this->dayIsApproximate()
			|| $this->monthIsApproximate()
			|| $this->yearIsApproximate();
	}

	public function dayIsApproximate(): bool {
		return $this->day === self::APPROXIMATE
			|| $this->day === self::UNCERTAIN_AND_APPROXIMATE;
	}

	public function monthIsApproximate(): bool {
		return $this->month === self::APPROXIMATE
			|| $this->month === self::UNCERTAIN_AND_APPROXIMATE;
	}

	public function yearIsApproximate(): bool {
		return $this->year === self::APPROXIMATE
			|| $this->year === self::UNCERTAIN_AND_APPROXIMATE;
	}

}
