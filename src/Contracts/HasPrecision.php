<?php

namespace EDTF\Contracts;

interface HasPrecision {

	const PRECISION_YEAR = 0;
	const PRECISION_MONTH = 1;
	const PRECISION_DAY = 2;
	const PRECISION_SEASON = 3;

	public function precisionAsString(): string;

	public function precision(): ?int;
}