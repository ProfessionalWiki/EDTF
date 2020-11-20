<?php

declare(strict_types = 1);

namespace EDTF;

class ExtDateTime extends ExtDate
{
    private ?int $hour;
    private ?int $minute;
    private ?int $second;
    private ?string $tzSign;
    private ?int $tzMinute;
    private ?int $tzHour;
    private int $timezoneOffset = 0;

    public function __construct(
        ?int $year,
        ?int $month,
        ?int $day,
        ?int $hour,
        ?int $minute,
        ?int $second,
        ?string $tzSign,
        ?int $tzHour,
        ?int $tzMinute
    )
    {
        parent::__construct($year, $month, $day);

        $this->hour = $hour;
        $this->minute = $minute;
        $this->second = $second;
        $this->tzSign = $tzSign;
        $this->tzMinute = $tzMinute;
        $this->tzHour = $tzHour;

        if(!is_null($this->tzSign)){
            $sign = "+" === $this->tzSign ? 1:-1;
            $tzHour = !is_null($this->tzHour) ? $this->tzHour:0;
            $tzMinute = !is_null($this->tzMinute) ? $this->tzMinute:0;
            $this->timezoneOffset = (int) ($sign * ($tzHour * 60) + $tzMinute);
        }
    }

    public function getHour(): ?int
    {
        return $this->hour;
    }

    public function getMinute(): ?int
    {
        return $this->minute;
    }

    public function getSecond(): ?int
    {
        return $this->second;
    }

    public function getTzSign(): ?string
    {
        return $this->tzSign;
    }

    public function getTzMinute(): ?int
    {
        return $this->tzMinute;
    }

    public function getTzHour(): ?int
    {
        return $this->tzHour;
    }

    public function getTimezoneOffset(): ?int
    {
        return $this->timezoneOffset;
    }
}