<?php

declare(strict_types=1);

namespace EDTF;


use EDTF\Contracts\ExtDateInterface;

class ExtDate implements ExtDateInterface
{
    private ?int $year;
    private ?int $month;
    private ?int $day;

    // level 1 props
    private Qualification $qualification;
    private UnspecifiedDigit $unspecifiedDigit;
    private int $intervalType;

    public function __construct(?int $year = null,
                                ?int $month = null,
                                ?int $day = null,
                                ?Qualification $qualification = null,
                                ?UnspecifiedDigit  $unspecified = null,
                                int $intervalType = 0
    ){
        $this->year = $year;
        $this->month = $month;
        $this->day = $day;
        $this->qualification = is_null($qualification) ? new Qualification():$qualification;
        $this->unspecifiedDigit = is_null($unspecified) ? new UnspecifiedDigit():$unspecified;
        $this->intervalType = $intervalType;
    }

    public static function from(Parser $parser): self
    {
        $q = Qualification::from($parser);
        $u = UnspecifiedDigit::from($parser);
        return new self(
            $parser->getYearNum(),
            $parser->getMonthNum(),
            $parser->getDayNum(),
            $q,
            $u,
            $parser->getIntervalType()
        );
    }

    public function uncertain(?string $part = null): bool
    {
        /**
         * @psalm-suppress PossiblyNullReference
         */
        return $this->qualification->uncertain($part);
    }

    public function approximate(?string $part = null): bool
    {
        /**
         * @psalm-suppress PossiblyNullReference
         */
        return $this->qualification->approximate($part);
    }

    public function unspecified(?string $part = null): bool
    {
        return $this->unspecifiedDigit->unspecified($part);
    }

    public function isNormalInterval(): bool
    {
        return Interval::NORMAL === $this->intervalType;
    }

    public function isOpenInterval(): bool
    {
        return Interval::OPEN === $this->intervalType;
    }

    public function isUnknownInterval(): bool
    {
        return Interval::UNKNOWN === $this->intervalType;
    }

    public function getQualification(): Qualification
    {
        return $this->qualification;
    }

    public function getUnspecifiedDigit(): UnspecifiedDigit
    {
        return $this->unspecifiedDigit;
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