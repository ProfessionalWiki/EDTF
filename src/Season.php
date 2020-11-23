<?php

declare(strict_types=1);

namespace EDTF;

use EDTF\Contracts\CoversTrait;
use EDTF\Contracts\ExtDateInterface;

class Season implements ExtDateInterface
{
    use CoversTrait;

    private int $year;
    private int $season;

    private ExtDateInterface $start;
    private ExtDateInterface $end;

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
    private string $input;

    public function __construct(string $input, int $year, int $season)
    {
        $this->input = $input;
        $this->year = $year;
        $this->season = $season;

        $this->configure();
    }

    public static function from(Parser $parser): Season
    {
        $year = $parser->getYearNum();
        $season = $parser->getSeason();

        assert(!is_null($year));
        assert(!is_null($season));
        return new Season($parser->getInput(), $year, $season);
    }

    private function configure(): void
    {
        $season = $this->season;
        $year = (string)$this->year;
        $startMonth = 0;
        $endMonth = 0;

        switch($season){
            // quarter - 3 month duration
            case 33:
                $startMonth = "01";
                $endMonth = "03";
                break;
            case 34:
                $startMonth = "04";
                $endMonth = "06";
                break;
            case 35:
                $startMonth = "07";
                $endMonth = "09";
                break;
            case 36:
                $startMonth = "10";
                $endMonth = "12";
                break;
            // quadrimester - 4 month duration
            case 37:
                $startMonth = "01";
                $endMonth = "04";
                break;
            case 38:
                $startMonth = "05";
                $endMonth = "08";
                break;
            case 39:
                $startMonth = "09";
                $endMonth = "12";
                break;
            // semestral - 6 month duration
            case 40:
                $startMonth = "01";
                $endMonth = "06";
                break;
            case 41:
                $startMonth = "07";
                $endMonth = "12";
                break;
        }

        $start = (new Parser())->createEdtf($year.'-'.$startMonth);
        $end = (new Parser())->createEdtf($year.'-'.$endMonth);


        $this->min = $start->getMin();
        $this->start = $start;
        $this->max = $end->getMax();
        $this->end = $end;
    }

    public function getInput(): string
    {
        return $this->input;
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