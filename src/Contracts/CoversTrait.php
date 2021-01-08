<?php

declare(strict_types = 1);

namespace EDTF\Contracts;

use Carbon\Carbon;
use EDTF\ExtDateInterface;

trait CoversTrait
{
    protected int $min = 0;
    protected int $max = 0;

    public function covers(ExtDateInterface $edtf): bool
    {
        $min = Carbon::createFromTimestamp($this->min);
        $max = Carbon::createFromTimestamp($this->max);

        $edtfMin = Carbon::createFromTimestamp($edtf->getMin());
        $edtfMax = Carbon::createFromTimestamp($edtf->getMax());
        return $edtfMin->isBetween($min,$max,true) || $edtfMax->isBetween($min, $max, true);
    }
}