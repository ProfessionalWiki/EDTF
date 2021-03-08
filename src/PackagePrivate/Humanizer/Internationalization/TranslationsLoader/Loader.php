<?php

declare( strict_types = 1 );

namespace EDTF\PackagePrivate\Humanizer\Internationalization\TranslationsLoader;

interface Loader
{
    public function load(string $languageCode): array;
}