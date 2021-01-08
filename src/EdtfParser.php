<?php

declare( strict_types = 1 );

namespace EDTF;

use EDTF\PackagePrivate\Parser;

class EdtfParser {

	private ?Parser $internalParser = null;

	public function parse( string $edtfString ): ParsingResult {
		try {
			$edtf = $this->getInternalParser()->createEdtf( $edtfString );
		} catch ( \InvalidArgumentException $ex ) {
			return ParsingResult::newError( $edtfString );
		}

		return ParsingResult::newValid( $edtfString, $edtf );
	}

	private function getInternalParser(): Parser {
		if ( $this->internalParser === null ) {
			$this->internalParser = new Parser();
		}

		return $this->internalParser;
	}

}
