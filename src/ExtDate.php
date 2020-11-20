<?php

declare(strict_types=1);

namespace EDTF;


class ExtDate
{
    protected ?int $year;
    protected ?int $month;
    protected ?int $day;

    public function __construct(?int $year, ?int $month, ?int $day)
    {
        $this->year = $year;
        $this->month = $month;
        $this->day = $day;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function getMonth(): ?int
    {
        return $this->month;
    }

    public function getDay(): ?int
    {
        return $this->day;
    }
}