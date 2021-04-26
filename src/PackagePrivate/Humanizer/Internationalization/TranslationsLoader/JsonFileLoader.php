<?php

declare( strict_types = 1 );

namespace EDTF\PackagePrivate\Humanizer\Internationalization\TranslationsLoader;

class JsonFileLoader implements Loader {

	private string $i18nPath;

	public function __construct( string $i18nPath ) {
		$this->i18nPath = $i18nPath;
	}

	/**
	 * @throws LoaderException
	 */
	public function load( string $languageCode ): array {
		$file = $this->i18nPath . "/$languageCode.json";

		if ( !file_exists( $file ) ) {
			$file = $this->i18nPath . "/en.json";
		}

		$json = file_get_contents( $file );
		if ( $json === false ) {
			throw new LoaderException( "Failed to load translations from file $file" );
		}

		/** @var array<string, string> $translations */
		$translations = json_decode( $json, true );

		return $translations;
	}
}