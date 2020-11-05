<?php

declare(strict_types = 1);

namespace EDTF;

class DateTime
{
    /**
     * @var int|null
     */
    private $year;

    /**
     * @var int|null
     */
    private $month;

    /**
     * @var int|null
     */
    private $day;

    /**
     * @var int|null
     */
    private $hour;

    /**
     * @var int|null
     */
    private $minute;

    /**
     * @var int|null
     */
    private $second;

    /**
     * @var string|null
     */
    private $timezone;

    /**
     * @var string|null
     */
    private $tzSign;

    /**
     * @var int|null
     */
    private $tzMinute;

    /**
     * @var int|null
     */
    private $tzHour;

    /**
     * @param string $data
     *
     * @return DateTime
     */
    public static function from(string $data)
    {
        $parser = Parser::from($data);
        return $parser->parseDateTime();
    }

    /**
     * @return int|null
     */
    public function getYear(): ?int
    {
        return $this->year;
    }

    /**
     * @param int|null $year
     * @return static
     */
    public function setYear(?int $year)
    {
        $this->year = $year;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMonth(): ?int
    {
        return $this->month;
    }

    /**
     * @param int|null $month
     * @return static
     */
    public function setMonth(?int $month)
    {
        $this->month = $month;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getDay(): ?int
    {
        return $this->day;
    }

    /**
     * @param int|null $day
     * @return static
     */
    public function setDay(?int $day)
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

    /**
     * @param int|null $hour
     * @return static
     */
    public function setHour(?int $hour)
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

    /**
     * @param int|null $minute
     * @return static
     */
    public function setMinute(?int $minute)
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

    /**
     * @param int|null $second
     * @return static
     */
    public function setSecond(?int $second)
    {
        $this->second = $second;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    /**
     * @param string|null $timezone
     * @return static
     */
    public function setTimezone(?string $timezone)
    {
        $this->timezone = $timezone;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTzSign(): ?string
    {
        return $this->tzSign;
    }

    /**
     * @param string|null $tzSign
     * @return static
     */
    public function setTzSign(?string $tzSign)
    {
        $this->tzSign = $tzSign;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getTzMinute(): ?int
    {
        return $this->tzMinute;
    }

    /**
     * @param int|null $tzMinute
     * @return static
     */
    public function setTzMinute(?int $tzMinute)
    {
        $this->tzMinute = $tzMinute;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getTzHour(): ?int
    {
        return $this->tzHour;
    }

    /**
     * @param int|null $tzHour
     * @return static
     */
    public function setTzHour(?int $tzHour)
    {
        $this->tzHour = $tzHour;
        return $this;
    }
}