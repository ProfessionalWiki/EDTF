<?php

declare( strict_types = 1 );

namespace EDTF;

use EDTF\PackagePrivate\Humanizer\Internationalization\ArrayMessageBuilder;
use EDTF\PackagePrivate\Humanizer\Internationalization\FallbackMessageBuilder;
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
     * @throws LoaderException
     */
	public static function newHumanizerForLanguage( string $languageCode ): Humanizer
    {
        $loader = new JsonFileLoader();
        $messages = $loader->load($languageCode);

        if ($languageCode !== 'en') {
            $fallbackMessages = $loader->load('en');
            $messageBuilder = new FallbackMessageBuilder(new ArrayMessageBuilder($messages), $fallbackMessages);
        } else {
            $messageBuilder = new ArrayMessageBuilder($messages);
        }

        return new InternationalizedHumanizer($messageBuilder, self::getLanguageStrategy($languageCode));
	}

    /**
     * @throws LoaderException
     */
	public static function newStructuredHumanizerForLanguage( string $languageCode ): StructuredHumanizer {
		return new PrivateStructuredHumanizer(
			self::newHumanizerForLanguage( $languageCode )
		);
	}

	private static function getLanguageStrategy(string $languageCode): LanguageStrategy
    {
        switch ($languageCode) {
            case "fr":
                $strategy = new FrenchStrategy();
                break;
            case "en":
            default:
                $strategy = new EnglishStrategy();
                break;
        }

        return $strategy;
    }
}
