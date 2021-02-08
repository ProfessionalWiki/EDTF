<?php

declare(strict_types = 1);

namespace EDTF\PackagePrivate;

use EDTF\ExtDate;
use EDTF\ExtDateInterface;
use EDTF\ExtDateTime;
use EDTF\Interval;
use EDTF\Season;
use EDTF\Set;

/**
 * @internal
 */
class Parser
{
    private string $regexPattern;

    private string $input = "";

    private ?string $year = null;
    private ?string $month = null;
    private ?string $day = null;

    private ?int $yearNum = null;
    private ?int $monthNum = null;
    private ?int $dayNum = null;

    private ?int $hour = null;
    private ?int $minute = null;
    private ?int $second = null;
    private int $season = 0;

    private ?string $tzSign = null;
    private ?int $tzMinute = null;
    private ?int $tzHour = null;
    private ?string $tzUtc = null;

    // qualification level 1 and 2 qualification props
    private ?string $yearOpenFlag = null;
    private ?string $monthOpenFlag = null;
    private ?string $dayOpenFlag = null;
    private ?string $yearCloseFlag = null;
    private ?string $monthCloseFlag = null;
    private ?string $dayCloseFlag = null;

    private int $intervalType = 0;

    private ?int $yearSignificantDigit = null;

    private array $matches = [];

    public function __construct()
    {
        $patterns = file_get_contents(__DIR__.'/../../config/regex.txt');
        $this->regexPattern = '/'.$patterns.'/';
    }

    public function parse(string $input, bool $intervalMode = false): self
    {
        $input = $this->removeExtraSpaces($input);

        if(false === $intervalMode && "" === $input){
            throw new \InvalidArgumentException("Can't create EDTF from empty string.");
        }

        $input = strtoupper($input);
        $this->input = $input;
        if($intervalMode && "" === $input){
            $this->intervalType = Interval::UNKNOWN;
            return $this;
        }

        if($intervalMode && ".." === $input) {
            $this->intervalType = Interval::OPEN;
            return $this;
        }
        $unspecifiedParts = [
            'yearNum', 'monthNum', 'dayNum'
        ];

        preg_match($this->regexPattern, $input, $matches);

        foreach($matches as $name => $value){
            if(is_int($name) || "" == $value || !property_exists(__CLASS__, $name)){
                continue;
            }

            if(in_array($name, $unspecifiedParts)){
                // convert unspecified digit into zero
                if(false !== strpos($value, 'X')){
                    $value = str_replace('X', '0', $value);
                }
            }

            $r = new \ReflectionProperty(__CLASS__, $name);
            $type = $r->getType();
            if($type instanceof \ReflectionNamedType && 'int' === $type->getName()){
                $value = (int) $value;
                // convert zero value into null
                if(0 === $value){
                    $value = null;
                }
            }
            $this->$name = $value;
        }

        // convert month into season
        if($this->monthNum > 12){
            $this->monthNum = null;
            $this->season = (int)$matches['monthNum'];
        }

        $this->matches = $matches;

        $validator = new ParserValidator($this);
        if(!$validator->isValid()){
            throw new \InvalidArgumentException($validator->getMessages());
        }
        return $this;
    }

	/**
	 * @param string $input
	 * @param bool $intervalMode
	 *
	 * @return ExtDateInterface
	 * @throws \InvalidArgumentException
	 */
    public function createEdtf(string $input, bool $intervalMode=false): ExtDateInterface
    {
        if (false !== strpos($input, '/')) {
            return Interval::from($input);
        }elseif(false !== strpos($input, '{') || false !== strpos($input, '[')){
            return Set::from($input);
        }

        $this->parse($input, $intervalMode);

        if(!is_null($this->getHour())){
            return ExtDateTime::from($this);
        }
        elseif(null !== $this->yearSignificantDigit){
            return Interval::createSignificantDigitInterval($this);
        }
        elseif($this->season){
            return Season::from($this);
        }
        return ExtDate::from($this);
    }

    public function getMatches(): array
    {
        return $this->matches;
    }

    public function getInput(): string
    {
        return $this->input;
    }

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function getMonth(): ?string
    {
        return $this->month;
    }

    public function getDay(): ?string
    {
        return $this->day;
    }

    public function getYearOpenFlag(): ?string
    {
        return $this->yearOpenFlag;
    }

    public function getMonthOpenFlag(): ?string
    {
        return $this->monthOpenFlag;
    }

    public function getDayOpenFlag(): ?string
    {
        return $this->dayOpenFlag;
    }

    public function getYearCloseFlag(): ?string
    {
        return $this->yearCloseFlag;
    }

    public function getMonthCloseFlag(): ?string
    {
        return $this->monthCloseFlag;
    }

    public function getDayCloseFlag(): ?string
    {
        return $this->dayCloseFlag;
    }

    public function getYearSignificantDigit(): ?int
    {
        return $this->yearSignificantDigit;
    }

    public function getIntervalType(): int
    {
        return $this->intervalType;
    }

    public function getSeason(): ?int
    {
        return $this->season;
    }

    public function getTzUtc(): ?string
    {
        return $this->tzUtc;
    }

    public function getYearNum(): ?int
    {
        return $this->yearNum;
    }

    public function getMonthNum(): ?int
    {
        return $this->monthNum;
    }

    public function getDayNum(): ?int
    {
        return $this->dayNum;
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

    private function removeExtraSpaces(string $input): string
    {
        return str_replace(" ", "", $input);
    }
}