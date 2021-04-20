<?php

declare( strict_types = 1 );

namespace EDTF;

class ParsingResult {

	private string $inputValue;
	private ?Model\EdtfValue $edtf = null;

	private function __construct( string $inputValue ) {
		$this->inputValue = $inputValue;
	}

	public static function newError( string $inputValue ): self {
		return new self( $inputValue );
	}

	public static function newValid( string $inputValue, Model\EdtfValue $edtf ): self {
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
	public function getEdtfValue(): Model\EdtfValue {
		/**
		 * @psalm-suppress NullableReturnStatement
		 */
		return $this->edtf;
	}

	public function getInput(): string {
		return $this->inputValue;
	}

}
