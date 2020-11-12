<?php

declare(strict_types = 1);

namespace EDTF;

use EDTF\Contracts\DateTimeInterface;

class ExtDateTime implements DateTimeInterface
{
    /**
     * Normal date value
     */
    public const STATUS_NORMAL      = 1;

    /**
     * There is a value, but we don't know anything about it
     */
    public const STATUS_UNKNOWN     = 2;

    /**
     * The specified edtf date use value ".."
     */
    public const STATUS_OPEN        = 3;

    /**
     * For EndDate when the value is a single date
     */
    public const STATUS_UNUSED      = 4;

    private int $status            = self::STATUS_UNKNOWN;

    /**
     * EDTF date doesn't define the qualification.
     */
    public const QUALIFICATION_UNSPECIFIED  = 0;

    /**
     * EDTF date uncertain, determined by flag "?"
     */
    public const QUALIFICATION_UNCERTAIN    = 1;

    /**
     * EDTF date approximate,
     * determined by flag "~"
     */
    public const QUALIFICATION_APPROXIMATE  = 2;

    /**
     * EDTF date both uncertain and approximate,
     * determined by flag "%"
     */
    public const QUALIFICATION_BOTH = 3;

    private int $qualification     = self::QUALIFICATION_UNSPECIFIED;

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

    public function isStatusTypeNormal(): bool
    {
        return $this->status === self::STATUS_NORMAL;
    }

    public function isStatusTypeOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    public function isStatusTypeUnknown(): bool
    {
        return $this->status === self::STATUS_UNKNOWN;
    }

    public function isQualificationUncertain(): bool
    {
        return $this->qualification === self::QUALIFICATION_UNCERTAIN;
    }

    public function isQualificationApproximate(): bool
    {
        return $this->qualification === self::QUALIFICATION_APPROXIMATE;
    }

    public function isQualificationBoth(): bool
    {
        return $this->qualification === self::QUALIFICATION_BOTH;
    }

    public function isQualificationUnspecified(): bool
    {
        return $this->qualification === self::QUALIFICATION_UNSPECIFIED;
    }

    public function setQualification(int $qualification): self
    {
        $this->qualification = $qualification;

        return $this;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function fromRegexMatches(array $matches): void
    {
        $props = [
            'year', 'month', 'day', 'hour', 'minute', 'second'
        ];
        foreach ($props as $field) {
            $fieldName = $field.'Num';
            if (isset($matches[$fieldName])) {
                $setter = 'set' . $field;
                call_user_func_array([$this, $setter], [$matches[$fieldName]]);
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