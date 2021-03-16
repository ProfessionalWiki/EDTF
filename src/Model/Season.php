<?php

declare(strict_types=1);

namespace EDTF\Model;

use EDTF\EdtfValue;
use EDTF\PackagePrivate\CoversTrait;
use EDTF\PackagePrivate\Parser\Parser;

class Season implements EdtfValue
{
    use CoversTrait;

    private int $year;
    private int $season;

    private EdtfValue $start;
    private EdtfValue $end;

    public function __construct(int $year, int $season)
    {
        $this->year = $year;
        $this->season = $season;

        // FIXME: do not do work in the constructor
		$year = (string)$this->year;

		$this->start = (new Parser())->createEdtf($year.'-'.$this->generateStartMonth());
		$this->end = (new Parser())->createEdtf($year.'-'.$this->generateEndMonth());
    }

    private function generateStartMonth(): string
    {
        switch($this->season){
            case 21:
            case 25:
            case 29:
                return '03';
            case 22:
            case 26:
            case 30:
                return '06';
            case 23:
            case 27:
            case 31:
            case 39;
                return '09';
            case 24:
            case 28:
            case 32:
                return '12';
            case 36:
                return '10';
            case 34:
                return '04';
            case 35:
            case 41:
                return '07';
            case 38:
                return '05';
            default:
                return '01';
        }
    }

    private function generateEndMonth(): string
    {
        switch($this->season){
            case 21:
            case 25:
            case 29:
                return '05';
            case 22:
            case 26:
            case 30:
            case 38:
                return '08';
            case 23:
            case 27:
            case 31:
                return '11';
            case 24:
            case 28:
            case 32:
                return '02';
            case 33:
                return '03';
            case 34:
            case 40:
                return '06';
            case 35:
                return '09';
            case 37:
                return '04';
            default:
                return '12';
        }
    }

    public function getMax(): int
    {
        return $this->end->getMax();
    }

    public function getMin(): int
    {
        return $this->start->getMin();
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function getSeason(): int
    {
        return $this->season;
    }

    public function getMonths(): array
    {
        switch ($this->season) {
            case 21:
            case 25:
            case 29:
                return [3, 4, 5];

            case 22:
            case 26:
            case 30:
                return [6, 7, 8];

            case 23:
            case 27:
            case 31:
                return [9, 10, 11];

            case 24:
            case 28:
            case 32:
                return [12, 1, 2];

            case 33:
                return [1, 2, 3];

            case 34:
                return [4, 5, 6];

            case 35:
                return [7, 8, 9];

            case 36:
                return [10, 11, 12];

            case 37:
                return [1, 2, 3, 4];

            case 38:
                return [5, 6, 7, 8];

            case 39:
                return [9, 10, 11, 12];

            case 40:
                return [1, 2, 3, 4, 5, 6];

            case 41:
                return [7, 8, 9, 10, 11, 12];

            default:
                return [];
        }
    }
}