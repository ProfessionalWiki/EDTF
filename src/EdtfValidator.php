<?php

declare( strict_types = 1 );

namespace EDTF;

class EdtfValidator {

	public static function newInstance(): self {
		return new self( new EdtfParser() );
	}

	private EdtfParser $parser;

	private function __construct( EdtfParser $parser ) {
		$this->parser = $parser;
	}

	public function isValidEdtf( string $string ): bool {
		return $this->parser->parse( $string )->isValid();
	}

}
