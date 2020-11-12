<?php

declare(strict_types=1);

namespace EDTF;

use EDTF\Contracts\DateTimeInterface;

class Interval implements DateTimeInterface
{
    private ?ExtDateTime $start = null;
    private ?ExtDateTime $end = null;

    public function getStart(): ?ExtDateTime
    {
        return $this->start;
    }

    public function setStart(ExtDateTime $start): Interval
    {
        $this->start = $start;
        return $this;
    }

    public function getEnd(): ?ExtDateTime
    {
        return $this->end;
    }

    public function setEnd(ExtDateTime $end): Interval
    {
        $this->end = $end;
        return $this;
    }
}