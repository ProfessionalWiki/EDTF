<?php

namespace EDTF\PackagePrivate\Humanizer\Strategy;

interface LanguageStrategy
{
    public function applyOrdinalEnding(int $number): string;

    public function composeFullDateString(string $year, string $month, string $day): string;
}
