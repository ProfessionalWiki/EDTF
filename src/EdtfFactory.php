<?php

declare( strict_types = 1 );

namespace EDTF;

use EDTF\PackagePrivate\Humanizer\EnglishHumanizer;
use EDTF\PackagePrivate\Humanizer\FrenchHumanizer;
use EDTF\PackagePrivate\Humanizer\PrivateStructuredHumanizer;
use EDTF\PackagePrivate\SaneParser;
use EDTF\PackagePrivate\Validator;

class EdtfFactory {

	public static function newParser(): EdtfParser {
		return new SaneParser();
	}

	public static function newValidator(): EdtfValidator {
		return Validator::newInstance();
	}

	public static function newHumanizerForLanguage( string $languageCode ): Humanizer {
		if ( $languageCode === 'fr' ) {
			return new FrenchHumanizer();
		}

		return new EnglishHumanizer();
	}

	public static function newStructuredHumanizerForLanguage( string $languageCode ): StructuredHumanizer {
		return new PrivateStructuredHumanizer(
			self::newHumanizerForLanguage( $languageCode )
		);
	}

}
