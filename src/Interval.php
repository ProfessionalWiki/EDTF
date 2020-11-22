<?php

declare(strict_types=1);

namespace EDTF;

use EDTF\Contracts\ExtDateInterface;

class Interval implements ExtDateInterface
{
    const NORMAL    = 0;
    const OPEN      = 1;
    const UNKNOWN   = 2;

    private ExtDateInterface $start;
    private ExtDateInterface $end;
    private ?int $significantDigit;
    private ?int $estimated;


    public function __construct(
        ExtDateInterface $start,
        ExtDateInterface $end,
        ?int $significantDigit = null,
        ?int $estimated = null
    )
    {
        $this->start = $start;
        $this->end = $end;
        $this->significantDigit = $significantDigit;
        $this->estimated = $estimated;
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

        $startDate = (new Parser())->parse($startDateStr, true);
        $endDate = (new Parser())->parse($endDateStr, true);

        return new Interval($startDate, $endDate);
    }

        /**
     * @psalm-suppress PossiblyNullArgument
     * @psalm-suppress PossiblyNullOperand
     */
    public static function createSignificantDigitInterval(Parser $parser): Interval
    {
        $estimated = $parser->getYearNum();
        $strEstimated = (string)$estimated;
        $significantDigit = $parser->getYearSignificantDigit();

        $year = substr($strEstimated,0, strlen($strEstimated) - $significantDigit);
        $startYear = $year.(str_repeat("0", $significantDigit));
        $endYear = $year.(str_repeat("9", $significantDigit));

        $start = new ExtDate((int)$startYear);
        $end = new ExtDate((int)$endYear);
        return new self($start, $end, $significantDigit, $estimated);
    }

    public function getStart(): ExtDateInterface
    {
        return $this->start;
    }

    public function getEnd(): ExtDateInterface
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