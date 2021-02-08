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

        // FIXME: do not do work in the constructor
        $this->configure();
    }

    private function configure(): void
    {
        $year = (string)$this->year;
        $startMonth = $this->generateStartMonth();
        $endMonth = $this->generateEndMonth();

        /*
        switch($season){
			// FIXME: cases 21 to 32 are not handled! (spec: https://www.loc.gov/standards/datetime/)
			// (possible inspiration: https://github.com/inukshuk/edtf.js/blob/master/src/season.js)

            // quarter - 3 month duration
            case 33:
                //$startMonth = "01";
                $endMonth = "03";
                break;
            case 34:
                //$startMonth = "04";
                $endMonth = "06";
                break;
            case 35:
                //$startMonth = "07";
                $endMonth = "09";
                break;
            case 36:
                //$startMonth = "10";
                $endMonth = "12";
                break;
            // quadrimester - 4 month duration
            case 37:
                //$startMonth = "01";
                $endMonth = "04";
                break;
            case 38:
                //$startMonth = "05";
                $endMonth = "08";
                break;
            case 39:
                //$startMonth = "09";
                $endMonth = "12";
                break;
            // semestral - 6 month duration
            case 40:
                //$startMonth = "01";
                $endMonth = "06";
                break;
            case 41:
                //$startMonth = "07";
                $endMonth = "12";
                break;
        }
        */

        $start = (new Parser())->createEdtf($year.'-'.$startMonth);
        $end = (new Parser())->createEdtf($year.'-'.$endMonth);


        $this->min = $start->getMin();
        $this->start = $start;
        $this->max = $end->getMax();
        $this->end = $end;
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
        return $this->max;
    }

    public function getMin(): int
    {
        return $this->min;
    }

    public function getType(): string
    {
        return 'Season';
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