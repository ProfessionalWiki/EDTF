<?php

declare( strict_types = 1 );

namespace EDTF\Model;

use InvalidArgumentException;

class UnspecifiedDigit {

	private int $year;
	private int $month;
	private int $day;

	private ?string $rawYear;
	private ?string $rawMonth;
	private ?string $rawDay;

	public function __construct( ?string $year = null, ?string $month = null, ?string $day = null ) {
		$this->rawYear = $year;
		$this->rawMonth = $month;
		$this->rawDay = $day;

		// TODO: this looks like parsing code - probably constructor params should be required and parsing code outside of the instance
		$this->year = $this->sumUnspecifiedDigit( $year );
		$this->month = $this->sumUnspecifiedDigit( $month );
		$this->day = $this->sumUnspecifiedDigit( $day );
	}

	private function sumUnspecifiedDigit( ?string $data = null ): int {
		$data = is_null( $data ) ? "" : $data;
		$count = 0;
		$lists = count_chars( $data, 1 );
		if ( is_array( $lists ) ) {
			foreach ( $lists as $i => $v ) {
				// 88 is X char
				if ( "X" === chr( $i ) ) {
					$count = $v;
				}
			}
		}
		return $count;
	}

	public function specified( ?string $part = null ): bool {
		return false === $this->unspecified( $part );
	}

	public function unspecified( ?string $part = null ): bool {
		if ( !is_null( $part ) ) {
			$this->validatePartName( $part );
			return $this->$part > 0;
		}
		return $this->unspecified( 'year' ) || $this->unspecified( 'month' ) || $this->unspecified( 'day' );
	}

	private function validatePartName( string $part ): void {
		$validPartName = [ 'year', 'month', 'day' ];

		if ( !in_array( $part, $validPartName ) ) {
			throw new InvalidArgumentException(
				sprintf( 'Invalid date part value: "%s". Accepted value is year,month, or day', $part )
			);
		}
	}

	public function getYear(): int {
		return $this->year;
	}

	public function getMonth(): int {
		return $this->month;
	}

	public function getDay(): int {
		return $this->day;
	}

	public function century(): bool {
		if ( $this->year == 2 && substr( $this->rawYear, -2 ) == "XX" ) {
			return true;
		}

		return false;
	}

	public function decade(): bool {
		if ( $this->year == 1 && substr( $this->rawYear, -1 ) == "X" ) {
			return true;
		}

		return false;
	}
}