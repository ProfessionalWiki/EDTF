<?php

declare(strict_types=1);

namespace EDTF;


class UnspecifiedDigit
{
    const SPECIFIED = 0;
    const UNSPECIFIED = 1;

    private int $year;
    private int $month;
    private int $day;

    public function __construct(int $year=0, int $month=0, int $day=0)
    {
        $this->year = $year;
        $this->month = $month;
        $this->day = $day;
    }

    public function specified(?string $part = null): bool
    {
        return false === $this->unspecified($part);
    }

    public function unspecified(?string $part = null): bool
    {
        if(!is_null($part)){
            $this->validatePartName($part);
            return self::UNSPECIFIED === $this->$part;
        }
        return $this->unspecified('year') || $this->unspecified('month') || $this->unspecified('day');
    }

    private function validatePartName(string $part): void
    {
        $validPartName = ['year', 'month', 'day'];

        if(!in_array($part, $validPartName)){
            throw new \InvalidArgumentException(
                sprintf('Invalid date part value: "%s". Accepted value is year,month, or day', $part)
            );
        }
    }
}