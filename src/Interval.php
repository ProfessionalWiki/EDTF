<?php

declare(strict_types=1);

namespace EDTF;

use EDTF\Contracts\ExtDateInterface;

class Interval implements ExtDateInterface
{
    const NORMAL    = 0;
    const OPEN      = 1;
    const UNKNOWN   = 2;

    private ExtDate $start;
    private ExtDate $end;
    private ?int $significantDigit;
    private ?int $estimated;


    public function __construct(
        ExtDate $start,
        ExtDate $end,
        ?int $significantDigit = null,
        ?int $estimated = null
    )
    {
        $this->start = $start;
        $this->end = $end;
        $this->significantDigit = $significantDigit;
        $this->estimated = $estimated;
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