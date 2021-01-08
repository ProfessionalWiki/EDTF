<?php

declare(strict_types=1);

namespace EDTF;

use EDTF\PackagePrivate\Parser;

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

    private static array $map = [
        '%' => self::UNCERTAIN_AND_APPROXIMATE,
        '?' => self::UNCERTAIN,
        '~' => self::APPROXIMATE,
    ];

    public function __construct(int $year = 0, int $month = 0, int $day=0)
    {
        $this->year = $year;
        $this->month = $month;
        $this->day = $day;
    }

    public static function from(Parser $parser): self
    {
        $year = self::UNDEFINED;
        $month = self::UNDEFINED;
        $day = self::UNDEFINED;


        if(!is_null($parser->getYearCloseFlag())
            || !is_null($parser->getMonthCloseFlag())
            || !is_null($parser->getDayCloseFlag())
        ){
            $includeYear = false;
            $includeMonth = false;
            $includeDay = false;
            $q = Qualification::UNDEFINED;

            if(!is_null($parser->getYearCloseFlag())){
                // applied only to year
                $includeYear = true;
                $q = self::genQualificationValue($parser->getYearCloseFlag());
            }elseif(!is_null($parser->getMonthCloseFlag())){
                // applied only to year, and month
                $includeYear = true;
                $includeMonth = true;
                $q = self::genQualificationValue($parser->getMonthCloseFlag());
            }elseif(!is_null($parser->getDayCloseFlag())){
                // applied to year, month, and day
                $includeYear = true;
                $includeMonth = true;
                $includeDay = true;
                $q = self::genQualificationValue($parser->getDayCloseFlag());
            }

            $year = $includeYear ? $q:$year;
            $month = $includeMonth ? $q:$month;
            $day = $includeDay ? $q:$day;
        }

        // handle level 2 qualification
        if(!is_null($parser->getYearOpenFlag())){
            $year = self::genQualificationValue($parser->getYearOpenFlag());
        }
        if(!is_null($parser->getMonthOpenFlag())){
            $month = self::genQualificationValue($parser->getMonthOpenFlag());
        }
        if(!is_null($parser->getDayOpenFlag())){
            $day = self::genQualificationValue($parser->getDayOpenFlag());;
        }
        return new self($year, $month, $day);
    }

    private static function genQualificationValue(?string $flag = null): int
    {
        assert(is_string($flag));
        return (int)self::$map[$flag];
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