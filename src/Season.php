<?php

declare(strict_types=1);

namespace EDTF;

use EDTF\Contracts\ExtDateInterface;

class Season implements ExtDateInterface
{
    private ?int $year;

    private ?int $season;

    public function __construct(?int $year = null, ?int $season = null)
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
}