<?php

declare(strict_types=1);

namespace EDTF;

class Interval
{
    private ?ExtDate $start = null;
    private ?ExtDate $end = null;

    public function __construct(ExtDate $start, ExtDate $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function getStart(): ?ExtDate
    {
        return $this->start;
    }

    public function getEnd(): ?ExtDate
    {
        return $this->end;
    }
}