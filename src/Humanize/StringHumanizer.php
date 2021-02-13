<?php

declare( strict_types = 1 );

namespace EDTF\Humanize;

use EDTF\PackagePrivate\SaneParser;

class StringHumanizer {

	private Humanizer $humanizer;
	private SaneParser $parser;

	public function __construct( Humanizer $humanizer, SaneParser $parser ) {
		$this->humanizer = $humanizer;
		$this->parser = $parser;
	}

	public function humanize( string $edtf ): string {
		$parsingResult = $this->parser->parse( $edtf );

		if ( !$parsingResult->isValid() ) {
			return $edtf;
		}

		$humanizedEdtf = $this->humanizer->humanize( $parsingResult->getEdtfValue() );

		return $humanizedEdtf === '' ? $parsingResult->getInput() : $humanizedEdtf;
	}

}
