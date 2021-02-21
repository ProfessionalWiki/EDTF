<?php

declare( strict_types = 1 );

namespace EDTF;

use EDTF\PackagePrivate\Humanizer\Internationalization\ArrayMessageBuilder;
use EDTF\PackagePrivate\Humanizer\InternationalizedHumanizer;
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

		return new InternationalizedHumanizer( new ArrayMessageBuilder(
			[
				'edtf-maybe-circa' => 'Maybe circa $1',
				'edtf-circa' => 'Circa $1',
				'edtf-maybe' => 'Maybe $1',
			]
		) );
	}

	public static function newStructuredHumanizerForLanguage( string $languageCode ): StructuredHumanizer {
		return new PrivateStructuredHumanizer(
			self::newHumanizerForLanguage( $languageCode )
		);
	}

}
