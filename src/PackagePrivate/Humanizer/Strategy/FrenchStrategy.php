<?php

namespace EDTF\PackagePrivate\Humanizer\Strategy;

class FrenchStrategy implements LanguageStrategy
{
    public function applyOrdinalEnding(int $number): string
    {
        return $number === 1 ? $number . 'er' : (string) $number;
    }

    public function composeFullDateString(string $year, string $month, string $day): string
    {
        return implode(' ', [$day, $month, $year]);
    }
}
