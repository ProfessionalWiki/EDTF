<?php

declare(strict_types=1);

namespace EDTF;

use EDTF\Contracts\ExtDateInterface;

class Season implements ExtDateInterface
{
    private int $year;

    private int $season;

    public const MAP = [
        21 => 'Spring',
        22 => 'Summer',
        23 => 'Autumn',
        24 => 'Winter',
        25 => 'Spring - Northern Hemisphere',
        26 => 'Summer - Northern Hemisphere',
        27 => 'Autumn - Northern Hemisphere',
        28 => 'Winter - Northern Hemisphere',
        29 => 'Spring - Southern Hemisphere',
        30 => 'Summer - Southern Hemisphere',
        31 => 'Autumn - Southern Hemisphere',
        32 => 'Winter - Southern Hemisphere',
        33 => 'Quarter 1',
        34 => 'Quarter 2',
        35 => 'Quarter 3',
        36 => 'Quarter 4',
        37 => 'Quadrimester 1',
        38 => 'Quadrimester 2',
        39 => 'Quadrimester 3',
        40 => 'Semester 1',
        41 => 'Semester 2',
    ];

    public function __construct(int $year, int $season)
    {
        $this->year = $year;
        $this->season = $season;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function getSeason(): int
    {
        return $this->season;
    }

    public function getName(): string
    {
        return self::MAP[$this->season];
    }
}