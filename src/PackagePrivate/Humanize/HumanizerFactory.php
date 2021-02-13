<?php

declare( strict_types = 1 );

namespace EDTF\PackagePrivate\Humanize;

use EDTF\Humanizer;
use EDTF\PackagePrivate\SaneParser;

class HumanizerFactory {

	public static function newForLanguage( string $languageCode ): Humanizer {
		if ( $languageCode === 'fr' ) {
			return new FrenchHumanizer();
		}

		return new EnglishHumanizer();
	}

	public static function newStringHumanizerForLanguage( string $languageCode ): PrivateStringHumanizer {
		return new PrivateStringHumanizer(
			self::newForLanguage( $languageCode ),
			new SaneParser()
		);
	}

}
