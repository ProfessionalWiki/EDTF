<?php

declare( strict_types = 1 );

namespace EDTF\Model;

class IntervalSide {

	/**
	 * @var ExtDate|Season|null
	 */
	private $date;

	private int $type;

	private function __construct( int $type ) {
		$this->type = $type;
	}

	/**
	 * @param ExtDate|Season $date
	 */
	public static function newFromDate( $date ): self {
		$instance = new self( Interval::NORMAL );
		$instance->date = $date;
		return $instance;
	}

	public static function newOpenInterval(): self {
		return new self( Interval::OPEN );
	}

	public static function newUnknownInterval(): self {
		return new self( Interval::UNKNOWN );
	}

	public function isNormalInterval(): bool {
		return $this->type === Interval::NORMAL;
	}

	public function isOpenInterval(): bool {
		return Interval::OPEN === $this->type;
	}

	public function isUnknownInterval(): bool {
		return Interval::UNKNOWN === $this->type;
	}

	/**
	 * @return ExtDate|Season|null
	 */
	public function getDate() {
		return $this->date;
	}

}
