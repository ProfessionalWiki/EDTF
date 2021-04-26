<?php

namespace EDTF\PackagePrivate\Parser;

class Qualification {

	private ?string $yearOpenFlag;
	private ?string $monthOpenFlag;
	private ?string $dayOpenFlag;
	private ?string $yearCloseFlag;
	private ?string $monthCloseFlag;
	private ?string $dayCloseFlag;

	public function __construct(
		?string $yearOpenFlag,
		?string $monthOpenFlag,
		?string $dayOpenFlag,
		?string $yearCloseFlag,
		?string $monthCloseFlag,
		?string $dayCloseFlag
	) {
		$this->yearOpenFlag = $yearOpenFlag;
		$this->monthOpenFlag = $monthOpenFlag;
		$this->dayOpenFlag = $dayOpenFlag;
		$this->yearCloseFlag = $yearCloseFlag;
		$this->monthCloseFlag = $monthCloseFlag;
		$this->dayCloseFlag = $dayCloseFlag;
	}

	public function getYearOpenFlag(): ?string {
		return $this->yearOpenFlag;
	}

	public function getMonthOpenFlag(): ?string {
		return $this->monthOpenFlag;
	}

	public function getDayOpenFlag(): ?string {
		return $this->dayOpenFlag;
	}

	public function getYearCloseFlag(): ?string {
		return $this->yearCloseFlag;
	}

	public function getMonthCloseFlag(): ?string {
		return $this->monthCloseFlag;
	}

	public function getDayCloseFlag(): ?string {
		return $this->dayCloseFlag;
	}
}