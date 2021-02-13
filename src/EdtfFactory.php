<?php

declare( strict_types = 1 );

namespace EDTF;

use EDTF\PackagePrivate\Humanize\HumanizerFactory;
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
		return HumanizerFactory::newForLanguage( $languageCode );
	}

	public static function newStringHumanizerForLanguage( string $languageCode ): StringHumanizer {
		return HumanizerFactory::newStringHumanizerForLanguage( $languageCode );
	}

}
