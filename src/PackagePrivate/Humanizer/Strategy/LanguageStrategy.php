<?php

namespace EDTF\PackagePrivate\Humanizer\Strategy;

interface LanguageStrategy {

	public function applyOrdinalEnding( int $number ): string;

	public function monthUppercaseFirst(): bool;
}
