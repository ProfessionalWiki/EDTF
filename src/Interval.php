<?php

declare(strict_types=1);

namespace EDTF;

class Interval
{
    private ?ExtDate $start = null;
    private ?ExtDate $end = null;

    public function getStart(): ?ExtDate
    {
        return $this->start;
    }

    public function setStart(ExtDate $start): Interval
    {
        $this->start = $start;
        return $this;
    }

    public function getEnd(): ?ExtDate
    {
        return $this->end;
    }

    public function setEnd(ExtDate $end): Interval
    {
        $this->end = $end;
        return $this;
    }
}