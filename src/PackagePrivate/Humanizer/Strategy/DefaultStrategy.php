<?php

namespace EDTF\PackagePrivate\Humanizer\Strategy;

class DefaultStrategy implements LanguageStrategy {

	public function applyOrdinalEnding( int $number ): string {
		return (string)$number;
	}

	public function monthUppercaseFirst(): bool {
		return false;
	}
}
