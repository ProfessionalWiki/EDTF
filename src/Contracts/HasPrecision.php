<?php

declare( strict_types = 1 );

namespace EDTF\Contracts;

interface HasPrecision {

	public const PRECISION_YEAR = 0;
	public const PRECISION_MONTH = 1;
	public const PRECISION_DAY = 2;
	public const PRECISION_SEASON = 3;

	public function precisionAsString(): string;

	public function precision(): int;

}