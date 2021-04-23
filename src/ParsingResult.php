<?php

declare( strict_types = 1 );

namespace EDTF;

use EDTF\Contracts\Coverable;

class ParsingResult {

	private string $inputValue;
	private ?Coverable $edtf = null;

	private function __construct( string $inputValue ) {
		$this->inputValue = $inputValue;
	}

	public static function newError( string $inputValue ): self {
		return new self( $inputValue );
	}

	public static function newValid( string $inputValue, Coverable $edtf ): self {
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
	public function getEdtfValue(): Coverable {
		/**
		 * @psalm-suppress NullableReturnStatement
		 */
		return $this->edtf;
	}

	public function getInput(): string {
		return $this->inputValue;
	}

}
