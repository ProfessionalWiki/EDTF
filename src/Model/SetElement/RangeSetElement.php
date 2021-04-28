<?php

declare( strict_types = 1 );

namespace EDTF\Model\SetElement;

use Carbon\Carbon;
use EDTF\Model\ExtDate;
use EDTF\Model\SetElement;
use InvalidArgumentException;

/**
 * 2011..2042
 */
class RangeSetElement implements SetElement {

	private ExtDate $start;
	private ExtDate $end;

	public function __construct( ExtDate $start, ExtDate $end ) {
		if ( $start->precision() !== $end->precision() ) {
			throw new InvalidArgumentException( 'The precision of dates in a set range needs to be the same' );
		}

		// TODO: refactor to earlierThan/laterThan methods on ExtDate
		if ( $start->getMax() >= $end->getMin() ) {
			throw new InvalidArgumentException( 'The precision of dates in a set range needs to be the same' );
		}

		$this->start = $start;
		$this->end = $end;
	}

	public function getStart(): ExtDate {
		return $this->start;
	}

	public function getEnd(): ExtDate {
		return $this->end;
	}

	/**
	 * @return array<int, ExtDate>
	 */
	public function getAllDates(): array {
		switch ( $this->start->precision() ) {
			case ExtDate::PRECISION_YEAR:
				return $this->resolveSetValuesForYearPrecision();
			case ExtDate::PRECISION_MONTH:
				return $this->resolveSetValuesForMonthPrecision();
			case ExtDate::PRECISION_DAY:
				return $this->resolveSetValuesForDayPrecision();
			default:
				throw new \LogicException();
		}
	}

	/**
	 * @return array<int, ExtDate>
	 */
	private function resolveSetValuesForYearPrecision(): array {
		$values = [];

		for ( $i = $this->start->getYear(); $i <= $this->end->getYear(); $i++ ) {
			$values[] = new ExtDate( $i );
		}

		return $values;
	}

	/**
	 * @return array<int, ExtDate>
	 */
	private function resolveSetValuesForMonthPrecision(): array {
		$yearTurnsLeft = $this->end->getYear() - $this->start->getYear();
		return $this->monthRecursion( $yearTurnsLeft, $this->start, $this->end );
	}

	/**
	 * @return array<int, ExtDate>
	 */
	private function resolveSetValuesForDayPrecision(): array {
		$progressionStart = $this->start;
		$progressionEnd = $this->end;

		$monthTurnsLeft = $progressionEnd->getMonth() - $progressionStart->getMonth();
		$yearTurnsLeft = $progressionEnd->getYear() - $progressionStart->getYear();

		$values = [];
		while ( $yearTurnsLeft > 0 ) {
			$currentYear = $progressionStart->getYear();
			$values = array_merge(
				$values,
				$this->resolveSetValuesForDayPrecisionWithinAYear(
					$progressionStart,
					new ExtDate( $currentYear, 12, 31 )
				)
			);
			$progressionStart = new ExtDate( ++$currentYear, 1, 1 );
			$yearTurnsLeft--;
		}

		return array_merge( $values, $this->dayRecursion( $monthTurnsLeft, $progressionStart, $progressionEnd ) );
	}

	private function resolveSetValuesForDayPrecisionWithinAYear( ExtDate $progressionStart, ExtDate $progressionEnd ) {
		$monthTurnsLeft = $progressionEnd->getMonth() - $progressionStart->getMonth();
		return $this->dayRecursion( $monthTurnsLeft, $progressionStart, $progressionEnd );
	}

	private function dayRecursion( int $monthTurnsLeft, ExtDate $progressionStart, ExtDate $progressionEnd ): array {
		$values = [];

		$currentYear = $progressionStart->getYear();
		$currentMonth = $progressionStart->getMonth();

		if ( $monthTurnsLeft === 0 ) {
			$limit = $progressionEnd->getDay();
		} else {
			$limit = Carbon::create( $currentYear, $currentMonth )->lastOfMonth()->day;
		}

		for ( $i = $progressionStart->getDay(); $i <= $limit; $i++ ) {
			$values[] = new ExtDate( $currentYear, $currentMonth, $i );
		}

		if ( $monthTurnsLeft === 0 ) {
			return $values;
		}

		$monthTurnsLeft--;

		return array_merge(
			$values,
			$this->dayRecursion( $monthTurnsLeft, new ExtDate( $currentYear, ++$currentMonth, 1 ), $progressionEnd )
		);
	}

	private function monthRecursion( int $yearTurnsLeft, ExtDate $progressionStart, ExtDate $progressionEnd ): array {
		$values = [];
		$currentYear = $progressionStart->getYear();
		if ( $yearTurnsLeft === 0 ) {
			$limit = $progressionEnd->getMonth();
		} else {
			$limit = 12;
		}

		for ( $i = $progressionStart->getMonth(); $i <= $limit; $i++ ) {
			$values[] = new ExtDate( $currentYear, $i );
		}

		if ( $yearTurnsLeft === 0 ) {
			return $values;
		}
		$yearTurnsLeft--;

		return array_merge(
			$values,
			$this->monthRecursion( $yearTurnsLeft, new ExtDate( ++$currentYear, 1 ), $progressionEnd )
		);
	}

	public function getMinAsUnixTimestamp(): int {
		return $this->start->getMin();
	}

	public function getMaxAsUnixTimestamp(): int {
		return $this->end->getMax();
	}
}
