<?php

declare( strict_types = 1 );

namespace EDTF\Model;

use EDTF\EdtfValue;
use EDTF\PackagePrivate\CoversTrait;
use InvalidArgumentException;

class Interval implements EdtfValue {
	use CoversTrait;

	public const NORMAL = 0;
	public const OPEN = 1;
	public const UNKNOWN = 2;

	private IntervalSide $start;
	private IntervalSide $end;
	private ?int $significantDigit;
	private ?int $estimated;

	/**
	 * @throws InvalidArgumentException
	 */
	public function __construct(
		IntervalSide $start,
		IntervalSide $end,
		?int $significantDigit = null,
		?int $estimated = null
	) {
		$this->start = $start;
		$this->end = $end;
		$this->significantDigit = $significantDigit;
		$this->estimated = $estimated;

		$this->assertAtLeastOneSideIsNormal();
		$this->assertEndIsLaterThanStart();
	}

	private function assertAtLeastOneSideIsNormal(): void {
		if ( !$this->start->isNormalInterval() && !$this->end->isNormalInterval() ) {
			throw new InvalidArgumentException( 'Interval needs to have one normal side' );
		}
	}

	private function assertEndIsLaterThanStart(): void {
		if ( $this->hasStartDate() && $this->hasEndDate() ) {
			// TODO: refactor to earlierThan/laterThan methods on ExtDate
			if ( $this->getStartDate()->getMax() >= $this->getEndDate()->getMin() ) {
				throw new InvalidArgumentException( 'The precision of dates in a set range needs to be the same' );
			}
		}
	}

	public function getMin(): int {
		// TODO: handle in IntervalSide
		return $this->start->getDate()->getMin();
	}

	public function getMax(): int {
		// TODO: handle in IntervalSide
		return $this->end->getDate()->getMax();
	}

	/**
	 * @return ExtDate|Season
	 */
	public function getStartDate() {
		$date = $this->start->getDate();

		if ( $date === null ) {
			throw new \RuntimeException( 'There is no start date' );
		}

		return $date;
	}

	public function hasStartDate(): bool {
		return $this->start->isNormalInterval();
	}

	/**
	 * @return ExtDate|Season
	 */
	public function getEndDate() {
		$date = $this->end->getDate();

		if ( $date === null ) {
			throw new \RuntimeException( 'There is no end date' );
		}

		return $date;
	}

	public function hasEndDate(): bool {
		return $this->end->isNormalInterval();
	}

	public function getSignificantDigit(): ?int {
		return $this->significantDigit;
	}

	public function getEstimated(): ?int {
		// TODO: looks like this is calculated in the parser rather than on demand here - probably should change
		return $this->estimated;
	}

	public function isNormalInterval(): bool {
		return $this->start->isNormalInterval() && $this->end->isNormalInterval();
	}

	public function isOpenInterval(): bool {
		return $this->start->isOpenInterval() || $this->end->isOpenInterval();
	}

	public function hasOpenEnd(): bool {
		return $this->end->isOpenInterval();
	}

	public function hasOpenStart(): bool {
		return $this->start->isOpenInterval();
	}

	public function isUnknownInterval(): bool {
		return $this->start->isUnknownInterval() || $this->end->isUnknownInterval();
	}

	public function hasUnknownEnd(): bool {
		return $this->end->isUnknownInterval();
	}

	public function hasUnknownStart(): bool {
		return $this->start->isUnknownInterval();
	}
}