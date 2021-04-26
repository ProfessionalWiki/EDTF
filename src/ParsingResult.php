<?php

declare( strict_types = 1 );

namespace EDTF;

class ParsingResult {

	private string $inputValue;
	private ?EdtfValue $edtf = null;
	private string $errorMessage = '';

	private function __construct( string $inputValue ) {
		$this->inputValue = $inputValue;
	}

	public static function newError( string $inputValue, string $errorMessage ): self {
		$instance = new self( $inputValue );
		$instance->errorMessage = $errorMessage;
		return $instance;
	}

	public static function newValid( string $inputValue, EdtfValue $edtf ): self {
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
	public function getEdtfValue(): EdtfValue {
		/**
		 * @psalm-suppress NullableReturnStatement
		 */
		return $this->edtf;
	}

	public function getInput(): string {
		return $this->inputValue;
	}

	/**
	 * Returns a non-internationalized reason of why parsing failed.
	 * This message can be quite technical and can change between releases.
	 */
	public function getErrorMessage(): string {
		return $this->errorMessage;
	}

}
