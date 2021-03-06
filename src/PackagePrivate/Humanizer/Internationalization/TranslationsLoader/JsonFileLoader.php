<?php

declare( strict_types = 1 );

namespace EDTF\PackagePrivate\Humanizer\Internationalization\TranslationsLoader;

class JsonFileLoader implements Loader
{
    private const I18N_DIR = __DIR__ . "/../../../../../i18n";

    /**
     * @throws LoaderException
     */
    public function load(string $languageCode): array
    {
        $file = self::I18N_DIR . "/$languageCode.json";

        if (!file_exists($file)) {
            $file = self::I18N_DIR . "/en.json";
        }

        $json = file_get_contents($file);
        if ($json === false) {
            throw new LoaderException("Failed to load translations from file $file");
        }

        return json_decode($json, true);
    }
}