<?php

declare( strict_types = 1 );

namespace EDTF\Model;

class Qualification {

	/**
	 * Means there is no qualification. So either the part
	 * of the date is provided and KNOWN, or it is not provided.
	 */
	public const UNDEFINED = 0;

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

	public static function newFullyKnown(): self {
		return new self(
			self::UNDEFINED,
			self::UNDEFINED,
			self::UNDEFINED
		);
	}

	public function __construct( int $year, int $month, int $day ) {
		$this->assertIsQualification( $year );
		$this->assertIsQualification( $month );
		$this->assertIsQualification( $day );

		$this->year = $year;
		$this->month = $month;
		$this->day = $day;
	}

	private function assertIsQualification( int $i ): void {
		if ( !in_array( $i, [ self::UNDEFINED, self::UNCERTAIN, self::APPROXIMATE, self::UNCERTAIN_AND_APPROXIMATE ] ) ) {
			throw new \InvalidArgumentException( 'Invalid qualification' );
		}
	}

	public function isFullyKnown(): bool {
		return $this->year === self::UNDEFINED
			&& $this->month === self::UNDEFINED
			&& $this->day === self::UNDEFINED;
	}

	public function yearIsKnown(): bool {
		return $this->year === self::UNDEFINED;
	}

	public function monthIsKnown(): bool {
		return $this->month === self::UNDEFINED;
	}

	public function dayIsKnown(): bool {
		return $this->day === self::UNDEFINED;
	}

	public function isUniform(): bool {
		return $this->year === $this->month
			&& $this->year === $this->day;
	}

	public function monthAndYearHaveTheSameQualification(): bool {
		return $this->year === $this->month;
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
