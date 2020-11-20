<?php

declare(strict_types=1);

namespace EDTF;

use EDTF\Contracts\ExtDateInterface;

class Interval implements ExtDateInterface
{
    private ExtDate $start;
    private ExtDate $end;

    /**
     * @psalm-suppress PropertyTypeCoercion
     */
    public function __construct(object $start, object $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function getStart(): ExtDate
    {
        return $this->start;
    }

    public function getEnd(): ExtDate
    {
        return $this->end;
    }
}