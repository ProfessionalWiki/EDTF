<?php

declare( strict_types = 1 );

namespace EDTF\PackagePrivate;

use EDTF\EdtfValidator;

class Validator implements EdtfValidator {

	public static function newInstance(): self {
		return new self( new SaneParser() );
	}

	private SaneParser $parser;

	private function __construct( SaneParser $parser ) {
		$this->parser = $parser;
	}

	public function isValidEdtf( string $string ): bool {
		return $this->parser->parse( $string )->isValid();
	}

}
