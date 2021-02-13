<?php

namespace EDTF\PackagePrivate\ValueObjects\Composites;

class Date
{
    private ?int $yearNum;
    private ?int $monthNum;
    private ?int $dayNum;

    private ?string $year;
    private ?string $month;
    private ?string $day;

    private int $season = 0;

    private ?int $yearSignificantDigit;

    public function __construct(
        ?string $year,
        ?string $month,
        ?string $day,
        ?int $yearNum,
        ?int $monthNum,
        ?int $dayNum,
        ?int $yearSignificantDigit
    ) {
        $this->year = $year;
        $this->month = $month;
        $this->day = $day;

        $this->yearNum = $yearNum;
        $this->monthNum = $monthNum;
        $this->dayNum = $dayNum;

        $this->yearSignificantDigit = $yearSignificantDigit;

        if ($this->monthNum > 12) {
            $this->season = (int) $this->monthNum;
            $this->monthNum = null;
        }
    }

    public function getYearNum(): ?int
    {
        return $this->yearNum;
    }

    public function getMonthNum(): ?int
    {
        return $this->monthNum;
    }

    public function getDayNum(): ?int
    {
        return $this->dayNum;
    }

    public function getRawYear(): ?string
    {
        return $this->year;
    }

    public function getRawMonth(): ?string
    {
        return $this->month;
    }

    public function getRawDay(): ?string
    {
        return $this->day;
    }

    public function getSeason(): int
    {
        return $this->season;
    }

    public function getYearSignificantDigit(): ?int
    {
        return $this->yearSignificantDigit;
    }
}
