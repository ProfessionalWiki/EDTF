<?php

declare( strict_types = 1 );

namespace EDTF;

interface EdtfValidator {

	public function isValidEdtf( string $string ): bool;

}
