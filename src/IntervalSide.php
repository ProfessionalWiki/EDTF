<?php

declare( strict_types = 1 );

namespace EDTF;

class IntervalSide {

	private ?ExtDate $date;
	private int $type;

	private function __construct() {
	}

	public static function newFromDate( ExtDate $date ): self {
		$instance = new self();
		$instance->type = Interval::NORMAL;
		$instance->date = $date;
		return $instance;
	}

	public static function newOpenInterval(): self {
		$instance = new self();
		$instance->type = Interval::OPEN;
		return $instance;
	}

	public static function newUnknownInterval(): self {
		$instance = new self();
		$instance->type = Interval::UNKNOWN;
		return $instance;
	}

	public function isNormalInterval(): bool {
		return $this->type === Interval::NORMAL;
	}

	public function isOpenInterval(): bool
	{
		return Interval::OPEN === $this->type;
	}

	public function isUnknownInterval(): bool
	{
		return Interval::UNKNOWN === $this->type;
	}

	public function getDate(): ExtDate {
		return $this->date;
	}

}
