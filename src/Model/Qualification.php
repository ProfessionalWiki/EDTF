<?php

declare( strict_types = 1 );

namespace EDTF\Model;

use InvalidArgumentException;

class Qualification {

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

	private array $undefinedParts = [];
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

	private function setUncertainty(): void {
		foreach ( [ 'year' => $this->year, 'month' => $this->month, 'day' => $this->day ]
			as $part => $value ) {

			switch( $value ) {
				case self::UNDEFINED:
					$this->undefinedParts[] = $part;
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
	 * @return string[]
	 */
	public function getUndefinedParts(): array {
		return $this->undefinedParts;
	}

	/**
	 * @return string[]
	 */
	public function getUncertainParts(): array {
		return $this->uncertainParts;
	}

	/**
	 * @return string[]
	 */
	public function getApproximateParts(): array {
		return $this->approximateParts;
	}

	/**
	 * @return string[]
	 */
	public function getUncertainAndApproximateParts(): array {
		return $this->uncertainAndApproximateParts;
	}

	public function undefined( string $part ): bool {
		$this->validatePartName( $part );
		return self::UNDEFINED === $this->$part;
	}

	public function isUncertain(): bool {
		$approximate = [ self::UNCERTAIN, self::APPROXIMATE, self::UNCERTAIN_AND_APPROXIMATE ];
		return in_array( $this->year,  $approximate)
			|| in_array( $this->month, $approximate )
			|| in_array( $this->day, $approximate );
	}

	public function uncertain( ?string $part = null ): bool {
		if ( !is_null( $part ) ) {
			$this->validatePartName( $part );
			return self::UNCERTAIN === $this->$part || self::UNCERTAIN_AND_APPROXIMATE === $this->$part;
		}

		return $this->uncertain( 'year' ) || $this->uncertain( 'month' ) || $this->uncertain( 'day' );
	}

	public function approximate( ?string $part = null ): bool {
		if ( !is_null( $part ) ) {
			$this->validatePartName( $part );
			return self::APPROXIMATE === $this->$part || self::UNCERTAIN_AND_APPROXIMATE === $this->$part;
		}
		return $this->approximate( 'year' ) || $this->approximate( 'month' ) || $this->approximate( 'day' );
	}

	private function validatePartName( string $part ): void {
		$validParts = [ 'year', 'month', 'day' ];

		if ( !in_array( $part, $validParts ) ) {
			throw new InvalidArgumentException(
				sprintf( 'Invalid date part value: "%s". Accepted value is year, month, or day', $part )
			);
		}
	}

}
