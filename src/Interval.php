<?php

declare(strict_types=1);

namespace EDTF;

use EDTF\Contracts\CoversTrait;

class Interval implements EdtfValue
{
    use CoversTrait;

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