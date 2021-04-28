<?php

declare( strict_types = 1 );

namespace EDTF;

use EDTF\PackagePrivate\Humanizer\Internationalization\ArrayMessageBuilder;
use EDTF\PackagePrivate\Humanizer\Internationalization\FallbackMessageBuilder;
use EDTF\PackagePrivate\Humanizer\Internationalization\MessageBuilder;
use EDTF\PackagePrivate\Humanizer\Internationalization\TranslationsLoader\JsonFileLoader;
use EDTF\PackagePrivate\Humanizer\Internationalization\TranslationsLoader\LoaderException;
use EDTF\PackagePrivate\Humanizer\InternationalizedHumanizer;
use EDTF\PackagePrivate\Humanizer\PrivateStructuredHumanizer;
use EDTF\PackagePrivate\Humanizer\Strategy\EnglishStrategy;
use EDTF\PackagePrivate\Humanizer\Strategy\FrenchStrategy;
use EDTF\PackagePrivate\Humanizer\Strategy\LanguageStrategy;
use EDTF\PackagePrivate\SaneParser;
use EDTF\PackagePrivate\Validator;

class EdtfFactory {

	public static function newParser(): EdtfParser {
		return new SaneParser();
	}

	public static function newValidator(): EdtfValidator {
		return Validator::newInstance();
	}

	/**
	 * Humanizer that returns a single string. Does not support sets.
	 * If you want set support, use the more complex StructuredHumanizer returned by @see newStructuredHumanizerForLanguage.
	 */
	public static function newHumanizerForLanguage(
		string $languageCode,
		string $fallbackLanguageCode = 'en'
	): Humanizer {
		return new InternationalizedHumanizer(
			self::newMessageBuilder( $languageCode, $fallbackLanguageCode ),
			self::getLanguageStrategy( $languageCode )
		);
	}

	public static function newStructuredHumanizerForLanguage(
		string $languageCode,
		string $fallbackLanguageCode = 'en'
	): StructuredHumanizer {
		return new PrivateStructuredHumanizer(
			self::newHumanizerForLanguage( $languageCode, $fallbackLanguageCode ),
			self::newMessageBuilder( $languageCode, $fallbackLanguageCode )
		);
	}

	/**
	 * FIXME: catch LoaderException and fall back
	 * @throws LoaderException
	 */
	private static function newMessageBuilder(
		string $languageCode,
		string $fallbackLanguageCode
	): MessageBuilder {
		$loader = new JsonFileLoader( __DIR__ . '/../i18n' );

		if ( $languageCode === $fallbackLanguageCode ) {
			return new ArrayMessageBuilder( $loader->load( $languageCode ) );
		}

		return new FallbackMessageBuilder(
			new ArrayMessageBuilder( $loader->load( $languageCode ) ),
			new ArrayMessageBuilder( $loader->load( $fallbackLanguageCode ) )
		);
	}

	private static function getLanguageStrategy( string $languageCode ): LanguageStrategy {
		switch ( $languageCode ) {
			case "fr":
				return new FrenchStrategy();
		}

		return new EnglishStrategy();
	}
}
