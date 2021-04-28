<?php

declare( strict_types = 1 );

namespace EDTF\Model;

use EDTF\Contracts\HasPrecision;
use EDTF\EdtfValue;
use EDTF\PackagePrivate\CoversTrait;

class Season implements EdtfValue, HasPrecision {
	use CoversTrait;

	private int $year;
	private int $season;

	private ExtDate $start;
	private ExtDate $end;

	public function __construct( int $year, int $season ) {
		$this->year = $year;
		$this->season = $season;

		$this->start = new ExtDate( $year, $this->generateStartMonth() );
		$this->end = new ExtDate( $year, $this->generateEndMonth() );
	}

	private function generateStartMonth(): int {
		switch ( $this->season ) {
			case 21:
			case 25:
			case 29:
				return 3;
			case 22:
			case 26:
			case 30:
				return 6;
			case 23:
			case 27:
			case 31:
			case 39;
				return 9;
			case 24:
			case 28:
			case 32:
				return 12;
			case 36:
				return 10;
			case 34:
				return 4;
			case 35:
			case 41:
				return 7;
			case 38:
				return 5;
			default:
				return 1;
		}
	}

	private function generateEndMonth(): int {
		switch ( $this->season ) {
			case 21:
			case 25:
			case 29:
				return 5;
			case 22:
			case 26:
			case 30:
			case 38:
				return 8;
			case 23:
			case 27:
			case 31:
				return 11;
			case 24:
			case 28:
			case 32:
				return 2;
			case 33:
				return 3;
			case 34:
			case 40:
				return 6;
			case 35:
				return 9;
			case 37:
				return 4;
			default:
				return 12;
		}
	}

	public function getMax(): int {
		return $this->end->getMax();
	}

	public function getMin(): int {
		return $this->start->getMin();
	}

	public function getYear(): int {
		return $this->year;
	}

	public function getSeason(): int {
		return $this->season;
	}

	public function getMonths(): array {
		switch ( $this->season ) {
			case 21:
			case 25:
			case 29:
				return [ 3, 4, 5 ];

			case 22:
			case 26:
			case 30:
				return [ 6, 7, 8 ];

			case 23:
			case 27:
			case 31:
				return [ 9, 10, 11 ];

			case 24:
			case 28:
			case 32:
				return [ 12, 1, 2 ];

			case 33:
				return [ 1, 2, 3 ];

			case 34:
				return [ 4, 5, 6 ];

			case 35:
				return [ 7, 8, 9 ];

			case 36:
				return [ 10, 11, 12 ];

			case 37:
				return [ 1, 2, 3, 4 ];

			case 38:
				return [ 5, 6, 7, 8 ];

			case 39:
				return [ 9, 10, 11, 12 ];

			case 40:
				return [ 1, 2, 3, 4, 5, 6 ];

			case 41:
				return [ 7, 8, 9, 10, 11, 12 ];

			default:
				return [];
		}
	}

	public function getStartMonth(): int {
		return $this->start->getMonth();
	}

	public function getEndMonth(): int {
		return $this->end->getMonth();
	}

	public function precision(): int {
		return self::PRECISION_SEASON;
	}

	public function precisionAsString(): string {
		return 'season';
	}
}