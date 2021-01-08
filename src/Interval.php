<?php

declare(strict_types=1);

namespace EDTF;

use EDTF\Contracts\CoversTrait;
use EDTF\Contracts\ExtDateInterface;
use EDTF\PackagePrivate\Parser;

class Interval implements ExtDateInterface
{
    use CoversTrait;

    const NORMAL    = 0;
    const OPEN      = 1;
    const UNKNOWN   = 2;

    private string $input;
    private ExtDate $start;
    private ExtDate $end;
    private ?int $significantDigit;
    private ?int $estimated;

    public function __construct(
        string $input,
        ExtDate $start,
        ExtDate $end,
        ?int $significantDigit = null,
        ?int $estimated = null
    )
    {
        $this->input = $input;
        $this->start = $start;
        $this->end = $end;
        $this->significantDigit = $significantDigit;
        $this->estimated = $estimated;

        $this->min = $start->getMin();
        $this->max = $end->getMax();
    }

    public function getMin(): int
    {
        return $this->min;
    }

    public function getMax(): int
    {
        return $this->max;
    }

    public function getType(): string
    {
        return "interval";
    }

    public static function from(string $input): ExtDateInterface
    {
        $pos = strrpos($input, '/');

        if(false === $pos){
            throw new \InvalidArgumentException(
                sprintf("Can't create interval from %s",$input)
            );
        }
        $startDateStr = substr( $input, 0, $pos );
        $endDateStr   = substr( $input, $pos + 1 );

        $startDate = ExtDate::from((new Parser())->parse($startDateStr, true));
        $endDate = ExtDate::from((new Parser)->parse($endDateStr, true));

        return new Interval($input, $startDate, $endDate);
    }

    public static function createSignificantDigitInterval(Parser $parser): Interval
    {
        $estimated = $parser->getYearNum();
        $strEstimated = (string)$estimated;
        $significantDigit = $parser->getYearSignificantDigit();
        assert(is_int($significantDigit));
        $year = substr($strEstimated,0, strlen($strEstimated) - $significantDigit);
        $startYear = $year.(str_repeat("0", $significantDigit));
        $endYear = $year.(str_repeat("9", $significantDigit));

        $start = new ExtDate($parser->getInput(),(int)$startYear);
        $end = new ExtDate($parser->getInput(), (int)$endYear);
        return new self($parser->getInput(), $start, $end, $significantDigit, $estimated);
    }

    public function getInput(): string
    {
        return $this->input;
    }

    public function getStart(): ExtDate
    {
        return $this->start;
    }

    public function getEnd(): ExtDate
    {
        return $this->end;
    }

    public function getSignificantDigit(): ?int
    {
        return $this->significantDigit;
    }

    public function getEstimated(): ?int
    {
        return $this->estimated;
    }
}