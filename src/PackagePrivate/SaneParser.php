<?php

declare( strict_types = 1 );

namespace EDTF\PackagePrivate;

use EDTF\EdtfParser;
use EDTF\PackagePrivate\Parser\Parser;
use EDTF\ParsingResult;
use InvalidArgumentException;

class SaneParser implements EdtfParser {

	private ?Parser $internalParser = null;

	public function parse( string $edtfString ): ParsingResult {
		try {
			$edtf = $this->getInternalParser()->createEdtf( $edtfString );
		}
		catch ( InvalidArgumentException $ex ) {
			return ParsingResult::newError( $edtfString, $ex->getMessage() );
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
