<?php

declare( strict_types = 1 );

namespace EDTF;

interface StringHumanizer {

	public function humanize( string $edtf ): string;

}
