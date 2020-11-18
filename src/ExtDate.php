<?php

declare(strict_types=1);

namespace EDTF;


class ExtDate
{
    protected ?int $year;
    protected ?int $month;
    protected ?int $day;

    private Parser $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
        $this->year = $parser->getYear();
        $this->month = $parser->getMonth();
        $this->day = $parser->getDay();
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