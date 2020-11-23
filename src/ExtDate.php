<?php

declare(strict_types=1);

namespace EDTF;


use Carbon\Carbon;
use EDTF\Contracts\CoversTrait;
use EDTF\Contracts\ExtDateInterface;
use function PHPUnit\Framework\isNull;

class ExtDate implements ExtDateInterface
{
    use CoversTrait;

    private ?int $year;
    private ?int $month;
    private ?int $day;

    // level 1 props
    private Qualification $qualification;
    private UnspecifiedDigit $unspecifiedDigit;
    private int $intervalType;

    private string $input;

    public function __construct(string $input,
                                ?int $year = null,
                                ?int $month = null,
                                ?int $day = null,
                                ?Qualification $qualification = null,
                                ?UnspecifiedDigit  $unspecified = null,
                                int $intervalType = 0
    ){
        $this->input = $input;
        $this->year = $year;
        $this->month = $month;
        $this->day = $day;
        $this->qualification = is_null($qualification) ? new Qualification():$qualification;
        $this->unspecifiedDigit = is_null($unspecified) ? new UnspecifiedDigit():$unspecified;
        $this->intervalType = $intervalType;

        $this->calcMinAndMax();
    }

    public static function from(Parser $parser): self
    {
        $q = Qualification::from($parser);
        $u = UnspecifiedDigit::from($parser);
        return new self(
            $parser->getInput(),
            $parser->getYearNum(),
            $parser->getMonthNum(),
            $parser->getDayNum(),
            $q,
            $u,
            $parser->getIntervalType()
        );
    }

    private function calcMinAndMax(): void
    {
        $year = $this->year;
        $month = $this->month;
        $day = $this->day;

        if(is_null($year)){
            // Infinity year
            $this->min = 0;
            $this->max = 0;
            return;
        }

        // generates minimum timestamp
        $minMonth = is_null($month) ? 1:$month;
        $minDay = is_null($day) ? 1:$day;
        $c = Carbon::create($year, $minMonth, $minDay);
        if(false !== $c){
            $c->setTime(0,0,0,0);
            $this->min = $c->getTimestamp();
        }else{
            throw new \InvalidArgumentException('Can\'t generate minimum date.');
        }

        // generates max timestamp
        $maxMonth = is_null($month) ? 12:$month;
        $maxDay = $day;

        if(is_null($maxDay)){
            $c = Carbon::create($year, $maxMonth);
            if(false !== $c){
                $maxDay = $c->lastOfMonth()->day;
            }
        }

        // handle unspecified digit type
        $maxYear = $this->generateMaxUnspecifiedDigit('year', (int)$year);
        $maxMonth = $this->generateMaxUnspecifiedDigit('month', (int)$maxMonth);
        $maxDay = $this->generateMaxUnspecifiedDigit('day', (int)$maxDay);
        $c = Carbon::create($maxYear, $maxMonth, $maxDay);
        if(false !== $c){
            $c->setTime(23,59,59);
            $this->max = $c->getTimestamp();
        }else{
            throw new \InvalidArgumentException(sprintf(
                "Can't generate max value from '%s'", $this->input
            ));
        }
    }

    private function generateMaxUnspecifiedDigit(string $part, int $value): int
    {
        $unspecified = $this->unspecifiedDigit;
        if($unspecified->unspecified($part)){
            $method = 'get'.$part;
            $uLen = (int)call_user_func([$unspecified, $method]);
            $vLen = strlen((string)$value);
            $value = substr((string)$value,0,$vLen-1);
            $value .= str_repeat("9", $uLen);
            $value = (int)$value;
        }

        if('month' == $part && $value > 12){
            $value = 12;
        }
        if('day' == $part && $value > 31){
            $value = 31;
        }

        return $value;
    }

    public function getInput(): string
    {
        return $this->input;
    }

    public function getType(): string
    {
        return 'ExtDate';
    }

    public function getMin(): int
    {
        return $this->min;
    }

    public function getMax(): int
    {
        return $this->max;
    }

    public function uncertain(?string $part = null): bool
    {
        return $this->qualification->uncertain($part);
    }

    public function approximate(?string $part = null): bool
    {
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