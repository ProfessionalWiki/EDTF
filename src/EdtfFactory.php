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

				'edtf-spring' => 'Spring',
				'edtf-summer' => 'Summer',
				'edtf-autumn' => 'Autumn',
				'edtf-winter' => 'Winter',
				'edtf-spring-north' => 'Spring (Northern Hemisphere)',
				'edtf-summer-north' => 'Summer (Northern Hemisphere)',
				'edtf-autumn-north' => 'Autumn (Northern Hemisphere)',
				'edtf-winter-north' => 'Winter (Northern Hemisphere)',
				'edtf-spring-south' => 'Spring (Southern Hemisphere)',
				'edtf-summer-south' => 'Summer (Southern Hemisphere)',
				'edtf-autumn-south' => 'Autumn (Southern Hemisphere)',
				'edtf-winter-south' => 'Winter (Southern Hemisphere)',
				'edtf-quarter-1' => 'First quarter',
				'edtf-quarter-2' => 'Second quarter',
				'edtf-quarter-3' => 'Third quarter',
				'edtf-quarter-4' => 'Fourth quarter',
				'edtf-quadrimester-1' => 'First quadrimester',
				'edtf-quadrimester-2' => 'Second quadrimester',
				'edtf-quadrimester-3' => 'Third quadrimester',
				'edtf-semester-1' => 'First semester',
				'edtf-semester-2' => 'Second semester',

				'edtf-season-and-year' => '$1 $2',

				'edtf-day-and-year' => '$1 of unknown month, $2',

			]
		) );
	}

	public static function newStructuredHumanizerForLanguage( string $languageCode ): StructuredHumanizer {
		return new PrivateStructuredHumanizer(
			self::newHumanizerForLanguage( $languageCode )
		);
	}

}
