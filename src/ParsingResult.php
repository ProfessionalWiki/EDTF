<?php

declare( strict_types = 1 );

namespace EDTF;

class ParsingResult {

	private string $inputValue;

	private function __construct() {
	}

	public static function newError( string $inputValue, string $errorId ): self {
		$instance = new self();
		$instance->inputValue = $inputValue;
		return $instance;
	}

	public static function newValid( string $inputValue, ExtDateTime $edtf ): self {

	}

	public function isValid(): bool {

	}

	public function getErrorIds(): array {

	}

	public function getDateTime(): ExtDateTime {

	}

	public function getInput(): string {

	}

}
