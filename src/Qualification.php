<?php

declare(strict_types=1);

namespace EDTF;

class Qualification
{
    const UNDEFINED = 0;

    /**
     * Determines if a date part qualification is uncertain,
     * specified by using "?" sign
     */
    const UNCERTAIN = 1;

    /**
     * Determines if a date part qualification is approximate
     * specified by using "~" sign
     */
    const APPROXIMATE = 2;

    /**
     * Determines if a date part qualification is uncertain and approximate
     * specified by using "%" flag
     */
    const UNCERTAIN_AND_APPROXIMATE = 3;

    private int $year;
    private int $month;
    private int $day;

    public function __construct(int $year = 0, int $month = 0, int $day=0)
    {
        $this->year = $year;
        $this->month = $month;
        $this->day = $day;
    }

    public function undefined(string $part): bool
    {
        $this->validatePartName($part);
        return self::UNDEFINED === $this->$part;
    }

    public function uncertain(?string $part = null): bool
    {
        if(!is_null($part)){
            $this->validatePartName($part);
            return self::UNCERTAIN === $this->$part || self::UNCERTAIN_AND_APPROXIMATE === $this->$part;
        }

        return $this->uncertain('year') || $this->uncertain('month') || $this->uncertain('day');
    }

    public function approximate(?string $part = null): bool
    {
        if(!is_null($part)){
            $this->validatePartName($part);
            return self::APPROXIMATE === $this->$part || self::UNCERTAIN_AND_APPROXIMATE === $this->$part;
        }
        return $this->approximate('year') || $this->approximate('month') || $this->approximate('day');
    }

    private function validatePartName(string $part): void
    {
        $validParts = ['year','month','day'];

        if(!in_array($part, $validParts)){
            throw new \InvalidArgumentException(
                sprintf('Invalid date part value: "%s". Accepted value is year,month, or day', $part)
            );
        }
    }

}