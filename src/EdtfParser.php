<?php

declare( strict_types = 1 );

namespace EDTF;

interface EdtfParser {

	public function parse( string $edtfString ): ParsingResult;

}
