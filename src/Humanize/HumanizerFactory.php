<?php

declare( strict_types = 1 );

namespace EDTF\Humanize;

use EDTF\PackagePrivate\SaneParser;
use EDTF\Humanize\Languages\EnglishHumanizer;
use EDTF\Humanize\Languages\FrenchHumanizer;

class HumanizerFactory {

	public static function newForLanguage( string $languageCode ): Humanizer {
		if ( $languageCode === 'fr' ) {
			return new FrenchHumanizer();
		}

		return new EnglishHumanizer();
	}

	public static function newStringHumanizerForLanguage( string $languageCode ): StringHumanizer {
		return new StringHumanizer(
			self::newForLanguage( $languageCode ),
			new SaneParser()
		);
	}

}
