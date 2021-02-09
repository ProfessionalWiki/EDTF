<?php

declare( strict_types = 1 );

namespace EDTF\Humanize;

use EDTF\EdtfParser;

class StringHumanizer {

	private Humanizer $humanizer;
	private EdtfParser $parser;

	public function __construct( Humanizer $humanizer, EdtfParser $parser ) {
		$this->humanizer = $humanizer;
		$this->parser = $parser;
	}

	public function humanize( string $edtf ): string {
		return $this->humanizer->humanize( $this->parser->parse( $edtf )->getDateTime() );
	}

}
