<?php

declare(strict_types=1);

namespace EDTF;

use EDTF\Contracts\CoversTrait;
use EDTF\PackagePrivate\Parser;

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
            case 22:
            case 26:
            case 31:
            case 34:
                return '04';
            case 23:
            case 27:
            case 30:
            case 35:
            case 41:
                return '07';
            case 24:
            case 28:
            case 29:
            case 36:
                return '10';
            case 38:
                return '05';
            case 39:
                return '09';
            default:
                return '01';
        }
    }

    private function generateEndMonth(): string
    {
        switch($this->season){
            case 21:
            case 25:
            case 32:
            case 33:
                return '03';
            case 22:
            case 26:
            case 31:
            case 34:
            case 40:
                return '06';
            case 23:
            case 27:
            case 30:
            case 35:
                return '09';
            case 37:
                return '04';
            case 38:
                return '08';
            /*
            case 24:
            case 28:
            case 29:
            case 36:
            case 41:
            case 39:
                return '12';
            */
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
}