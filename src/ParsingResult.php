<?php

declare( strict_types = 1 );

namespace EDTF;

class ParsingResult {

	private string $inputValue;
	private ?ExtDateInterface $edtf = null;

	private function __construct( string $inputValue ) {
		$this->inputValue = $inputValue;
	}

	public static function newError( string $inputValue ): self {
		return new self( $inputValue );
	}

	public static function newValid( string $inputValue, ExtDateInterface $edtf ): self {
		$instance = new self( $inputValue );
		$instance->edtf = $edtf;
		return $instance;
	}

	public function isValid(): bool {
		return $this->edtf !== null;
	}

	/**
	 * @psalm-suppress InvalidNullableReturnType
	 */
	public function getDateTime(): ExtDateInterface {
		/**
		 * @psalm-suppress NullableReturnStatement
		 */
		return $this->edtf;
	}

	public function getInput(): string {
		return $this->inputValue;
	}

}
