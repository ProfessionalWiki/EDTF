<?php

namespace EDTF\Contracts;

interface Coverable
{
	public function getMax(): int;

	public function getMin(): int;

	public function covers(Coverable $edtf): bool;
}