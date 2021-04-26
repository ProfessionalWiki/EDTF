<?php

namespace EDTF\PackagePrivate\Parser;

class Timezone {

	private ?int $tzMinute;
	private ?int $tzHour;

	private ?string $tzUtc;
	private ?string $tzSign;

	public function __construct( ?int $tzHour, ?int $tzMinute, ?string $tzSign, ?string $tzUtc ) {
		$this->tzHour = $tzHour;
		$this->tzMinute = $tzMinute;
		$this->tzSign = $tzSign;
		$this->tzUtc = $tzUtc;
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

	public function getTzUtc(): ?string {
		return $this->tzUtc;
	}
}
