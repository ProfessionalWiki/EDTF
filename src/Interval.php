<?php

declare(strict_types=1);

namespace EDTF;

use EDTF\Contracts\DateTimeInterface;

class Interval implements DateTimeInterface
{
    private ?ExtDateTime $lower = null;
    private ?ExtDateTime $upper = null;

    public function getLower(): ?ExtDateTime
    {
        return $this->lower;
    }

    public function setLower(ExtDateTime $lower): Interval
    {
        $this->lower = $lower;
        return $this;
    }

    public function getUpper(): ?ExtDateTime
    {
        return $this->upper;
    }

    public function setUpper(ExtDateTime $upper): Interval
    {
        $this->upper = $upper;
        return $this;
    }
}