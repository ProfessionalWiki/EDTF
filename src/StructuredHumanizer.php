<?php

declare( strict_types = 1 );

namespace EDTF;

interface StructuredHumanizer {

	public function humanize( EdtfValue $edtf ): HumanizationResult;

}
