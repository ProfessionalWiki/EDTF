<?php

namespace EDTF\PackagePrivate\Parser;

class Time {

	private ?int $hour;
	private ?int $minute;
	private ?int $second;

	public function __construct( ?int $hour, ?int $minute, ?int $second ) {
		$this->hour = $hour;
		$this->minute = $minute;
		$this->second = $second;
	}

	public function getHour(): ?int {
		return $this->hour;
	}

	public function getMinute(): ?int {
		return $this->minute;
	}

	public function getSecond(): ?int {
		return $this->second;
	}
}
