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

	public function __construct( int $year = 0, int $month = 0, int $day = 0 ) {
		// TODO: Does it make sense these constructor params are optional?
		$this->year = $year;
		$this->month = $month;
		$this->day = $day;
	}

	public function undefined( string $part ): bool {
		$this->validatePartName( $part );
		return self::UNDEFINED === $this->$part;
	}

	private function getApproximate( int $match, ?string $requested_part, ?array &$info = [] ): bool {
		if ( !is_null( $requested_part ) ) {
			$this->validatePartName( $requested_part );
		}

		$info = [ 'year' => $this->year, 'month' => $this->month, 'day' => $this->day ];

		foreach ( $info as $part => $value ) {
			$info[$part] = ( $match === $value || self::UNCERTAIN_AND_APPROXIMATE === $value );
		}

		return !is_null( $requested_part ) ? $info[$requested_part] : (bool)count( array_filter( $info ) );
	}

	public function approximate( ?string $requested_part = null, ?array &$info = [] ): bool {
		return $this->getApproximate( self::APPROXIMATE, $requested_part, $info );
	}

	public function uncertain( ?string $requested_part = null, ?array &$info = [] ): bool {
		return $this->getApproximate( self::UNCERTAIN, $requested_part, $info );
	}

	public function isApproximate(): bool {
		return in_array( $this->year, [ self::APPROXIMATE, self::UNCERTAIN_AND_APPROXIMATE ] )
			|| in_array( $this->month, [ self::APPROXIMATE, self::UNCERTAIN_AND_APPROXIMATE ] )
			|| in_array( $this->day, [ self::APPROXIMATE, self::UNCERTAIN_AND_APPROXIMATE ] );
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
