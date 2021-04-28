<?php

declare( strict_types = 1 );

namespace EDTF\Model;

use DateInterval;
use DateTime;
use DateTimeZone;
use EDTF\EdtfValue;
use EDTF\PackagePrivate\CoversTrait;

class ExtDateTime implements EdtfValue {
	use CoversTrait;

	private ExtDate $date;
	private int $hour;
	private int $minute;
	private int $second;
	private ?string $tzSign;
	private ?int $tzMinute;
	private ?int $tzHour;
	private ?int $timezoneOffset = null;

	// TODO: replace 3 time zone fields with 1 required one: int offset or null for local time
	public function __construct(
		ExtDate $date,
		int $hour,
		int $minute,
		int $second,
		?string $tzSign = null,
		?int $tzHour = null,
		?int $tzMinute = null
	) {
		$this->date = $date;
		$this->hour = $hour;
		$this->minute = $minute;
		$this->second = $second;
		$this->tzSign = $tzSign;
		$this->tzMinute = $tzMinute;
		$this->tzHour = $tzHour;

		$this->timezoneOffset = $this->calculateTimeZoneOffset( $tzSign, $tzHour, $tzMinute );
	}

	private function calculateTimeZoneOffset( ?string $tzSign, ?int $tzHour, ?int $tzMinute ): ?int {
		if ( $tzSign === null ) {
			return null;
		}

		if ( $tzSign === 'Z' ) {
			return 0;
		}

		$offset = ( ( $tzHour ?? 0 ) * 60 ) + ( $tzMinute ?? 0 );
		$sign = "+" === $tzSign ? 1 : -1;

		return $offset * $sign;
	}

	public function getHour(): int {
		return $this->hour;
	}

	public function getMinute(): int {
		return $this->minute;
	}

	public function getSecond(): int {
		return $this->second;
	}

	public function getTzSign(): ?string {
		return $this->tzSign;
	}

	public function getTzMinute(): ?int {
		return $this->tzMinute;
	}

	public function getTzHour(): ?int {
		return $this->tzHour;
	}

	/**
	 * Returns time timezone offset in minutes.
	 * 0 for UTC
	 * -60 for UTC-1
	 * null for local time
	 */
	public function getTimezoneOffset(): ?int {
		return $this->timezoneOffset;
	}

	public function getMax(): int {
		// FIXME: this is not correct
		return $this->date->getMax();
	}

	public function getMin(): int {
		// FIXME: this is not correct
		return $this->date->getMin();
	}

	public function getYear(): int {
		return $this->date->getYear();
	}

	public function getMonth(): int {
		return $this->date->getMonth();
	}

	public function getDay(): int {
		return $this->date->getDay();
	}

	public function getDate(): ExtDate {
		return $this->date;
	}

	public function iso8601(): string {
		$date = new DateTime();
		$date->setDate( $this->getYear(), $this->getMonth(), $this->getDay() );
		$date->setTime( $this->getHour(), $this->getMinute(), $this->getSecond() );

		// The hour, minute, and second are stored as given, but adding the timezone
		// on to the DateTime will shift the time, so we need to pull it back off.
		if ( !is_null( $this->timezoneOffset ) ) {
			if ( $this->timezoneOffset > 0 ) {
				$date->sub( new DateInterval( 'PT' . $this->timezoneOffset . 'M' ) );
			} else {
				$date->add( new DateInterval( 'PT' . abs( $this->timezoneOffset ) . 'M' ) );
			}
			$date->setTimezone(
				new DateTimeZone(
					$this->getTzSign() . sprintf( "%02s:%02s", $this->getTzHour(), $this->getTzMinute() )
				)
			);
			return $date->format( 'c' );
		}

		return $date->format( 'Y-m-d\Th:i:s' );
	}
}