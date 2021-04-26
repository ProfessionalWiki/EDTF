<?php

namespace EDTF\PackagePrivate\Parser;

use Carbon\Carbon;
use InvalidArgumentException;

class Date {

	private ?int $yearNum;
	private ?int $monthNum;
	private ?int $dayNum;

	private ?string $year;
	private ?string $month;
	private ?string $day;

	private int $season = 0;

	private ?int $yearSignificantDigit;

	public function __construct(
		?string $year,
		?string $month,
		?string $day,
		?int $yearNum,
		?int $monthNum,
		?int $dayNum,
		?int $yearSignificantDigit
	) {
		$this->year = $year;
		$this->month = $month;
		$this->day = $day;

		$this->yearNum = $yearNum;
		$this->monthNum = $monthNum;
		$this->dayNum = $dayNum;

		$this->yearSignificantDigit = $yearSignificantDigit;

		$this->validateLeapYearCase();

		if ( $this->monthNum > 12 ) {
			$this->season = $this->monthNum;
			$this->monthNum = null;
		}
	}

	public function getYearNum(): ?int {
		return $this->yearNum;
	}

	public function getMonthNum(): ?int {
		return $this->monthNum;
	}

	public function getDayNum(): ?int {
		return $this->dayNum;
	}

	public function getRawYear(): ?string {
		return $this->year;
	}

	public function getRawMonth(): ?string {
		return $this->month;
	}

	public function getRawDay(): ?string {
		return $this->day;
	}

	public function getSeason(): int {
		return $this->season;
	}

	public function getYearSignificantDigit(): ?int {
		return $this->yearSignificantDigit;
	}

	/**
	 * @throws InvalidArgumentException
	 */
	private function validateLeapYearCase(): void {
		if ( $this->monthNum == 2 && $this->dayNum > 28 ) {
			$c = Carbon::create( $this->yearNum );
			if ( $c === false || !$c->isLeapYear() ) {
				throw new InvalidArgumentException(
					"$this->yearNum is not a leap year. Maximum 28 days is possible in February"
				);
			}
		}
	}
}
