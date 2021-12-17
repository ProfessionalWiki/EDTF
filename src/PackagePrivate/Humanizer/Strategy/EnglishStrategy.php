<?php

namespace EDTF\PackagePrivate\Humanizer\Strategy;

class EnglishStrategy implements LanguageStrategy {

	public function applyOrdinalEnding( int $number ): string {
		if ( $number % 100 >= 11 && $number % 100 <= 13 ) {
			return $number . 'th';
		}

		return $number . [ 'th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th' ][abs( $number ) % 10];
	}

	public function monthUppercaseFirst(): bool {
		return true;
	}
}
