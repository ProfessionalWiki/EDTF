<?php

declare(strict_types = 1);

namespace EDTF;

use EDTF\Contracts\DateTimeInterface;

class ExtDateTime implements DateTimeInterface
{
    private ?int $year = null;

    private ?int $month = null;

    private ?int $day = null;

    private ?int $hour = null;

    private ?int $minute = null;

    private ?int $second = null;

    private ?int $tzSign = null;

    private ?int $tzMinute = null;

    private ?int $tzHour = null;

    private ?int $timezoneOffset = 0;

    public function fromRegexMatches(array $matches): void
    {
        $props = [
            'year', 'month', 'day', 'hour', 'minute', 'second'
        ];
        foreach ($props as $field) {
            if (isset($matches[$field])) {
                $setter = 'set' . $field;
                call_user_func_array([$this, $setter], [$matches[$field]]);
            }
        }

        if (isset($matches['tzUtc']) && $matches['tzUtc'] == 'Z') {
            $this->setTimezoneOffset(0);
        } elseif (isset($matches['tzSign'])) {
            $tzSign = $matches['tzSign'] == "-" ? -1:1;
            $tzHour = (int)$matches['tzHour'];
            $tzMinute = (int)$matches['tzMinute'];
            $tzOffset = $tzSign * ($tzHour * 60) + $tzMinute;

            $this
                ->setTzSign($tzSign)
                ->setTzHour($tzHour)
                ->setTzMinute($tzMinute)
                ->setTimezoneOffset($tzOffset);
        }
    }

    public function getTimezoneOffset(): ?int
    {
        return $this->timezoneOffset;
    }

    public function setTimezoneOffset(?int $timezoneOffset): self
    {
        $this->timezoneOffset = $timezoneOffset;
        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(?int $year): self
    {
        $this->year = $year;
        return $this;
    }

    public function getMonth(): ?int
    {
        return $this->month;
    }

    public function setMonth(?int $month): self
    {
        $this->month = $month;
        return $this;
    }

    public function getDay(): ?int
    {
        return $this->day;
    }

    public function setDay(?int $day): self
    {
        $this->day = $day;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getHour(): ?int
    {
        return $this->hour;
    }

    public function setHour(?int $hour): self
    {
        $this->hour = $hour;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMinute(): ?int
    {
        return $this->minute;
    }

    public function setMinute(?int $minute): self
    {
        $this->minute = $minute;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getSecond(): ?int
    {
        return $this->second;
    }

    public function setSecond(?int $second): self
    {
        $this->second = $second;
        return $this;
    }

    public function getTzSign(): ?int
    {
        return $this->tzSign;
    }

    public function setTzSign(?int $tzSign): self
    {
        $this->tzSign = $tzSign;
        return $this;
    }

    public function getTzMinute(): ?int
    {
        return $this->tzMinute;
    }

    public function setTzMinute(?int $tzMinute): self
    {
        $this->tzMinute = $tzMinute;
        return $this;
    }

    public function getTzHour(): ?int
    {
        return $this->tzHour;
    }

    public function setTzHour(?int $tzHour): self
    {
        $this->tzHour = $tzHour;
        return $this;
    }
}