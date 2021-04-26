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

	public function __construct(
		IntervalSide $start,
		IntervalSide $end,
		?int $significantDigit = null,
		?int $estimated = null
	) {
		if ( !$start->isNormalInterval() && !$end->isNormalInterval() ) {
			throw new InvalidArgumentException( 'Interval needs to have one normal side' );
		}

		$this->start = $start;
		$this->end = $end;
		$this->significantDigit = $significantDigit;
		$this->estimated = $estimated;
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