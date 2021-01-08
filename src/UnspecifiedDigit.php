<?php

declare(strict_types=1);

namespace EDTF;


class UnspecifiedDigit
{
    private int $year;
    private int $month;
    private int $day;

    public function __construct(?string $year = null, ?string $month = null, ?string $day = null)
    {
        $this->year = $this->sumUnspecifiedDigit($year);
        $this->month = $this->sumUnspecifiedDigit($month);
        $this->day = $this->sumUnspecifiedDigit($day);
    }

    private function sumUnspecifiedDigit(?string $data = null): int
    {
        $data = is_null($data) ? "":$data;
        $count = 0;
        $lists = count_chars($data,1);
        if(is_array($lists)){
            foreach($lists as $i => $v){
                // 88 is X char
                if("X" === chr($i)){
                    $count = $v;
                }
            }
        }
        return $count;
    }

    public static function from(Parser $parser): self
    {
        return new self(
            $parser->getYear(),
            $parser->getMonth(),
            $parser->getDay()
        );
    }

    public function specified(?string $part = null): bool
    {
        return false === $this->unspecified($part);
    }

    public function unspecified(?string $part = null): bool
    {
        if(!is_null($part)){
            $this->validatePartName($part);
            return $this->$part > 0;
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

    public function getYear(): int
    {
        return $this->year;
    }

    public function getMonth(): int
    {
        return $this->month;
    }

    public function getDay(): int
    {
        return $this->day;
    }
}