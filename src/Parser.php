<?php

declare(strict_types = 1);

namespace EDTF;


use EDTF\Contracts\ExtDateInterface;

class Parser
{
    private string $regexPattern;

    private string $input = "";

    private ?int $yearNum = null;
    private ?int $monthNum = null;
    private ?int $dayNum = null;

    private ?int $hour = null;
    private ?int $minute = null;
    private ?int $second = null;
    private ?int $season = null;

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

    private int $yearUnspecified = 0;
    private int $monthUnspecified = 0;
    private int $dayUnspecified = 0;

    private int $intervalType = 0;

    private ?int $yearSignificantDigit = null;

    private ?array $matches = null;

    public function __construct()
    {
        $patterns = file_get_contents(__DIR__.'/../config/regex.txt');
        $this->regexPattern = '/'.$patterns.'/';
    }

    /**
     * @psalm-suppress MixedAssignment
     */
    private function validateInput(string $input, array $matches): void
    {
        $hasValue = false;
        foreach($matches as $k => $v){
            if("" != $v){
                $hasValue = true;
            }
        }

        if(!$hasValue){
            throw new \InvalidArgumentException(sprintf(
                'Invalid edtf format "%s".',$input
            ));
        }
    }

    private function validateSeason(): void
    {
        if(!(null === $this->season || ($this->season >= 21 && $this->season <= 41))){
            throw new \InvalidArgumentException(sprintf(
                'Season number "%s" out of range. Accepted season number is between 21-41',
                $this->season
            ));
        }
    }

    /**
     * @psalm-suppress PossiblyNullReference
     */
    private function doParse(string $input, bool $intervalMode = false): self
    {
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

        $this->validateInput($input, $matches);

        foreach($matches as $name => $value){
            if(is_int($name) || "" == $value || !property_exists(__CLASS__, $name)){
                continue;
            }

            if(in_array($name, $unspecifiedParts)){
                // convert unspecified digit into zero
                if(false !== strpos($value, 'X')){
                    $propName = str_replace('Num','Unspecified', $name);
                    $this->$propName = UnspecifiedDigit::UNSPECIFIED;
                    $value = str_replace('X', '0', $value);
                }
            }

            $r = new \ReflectionProperty(__CLASS__, $name);
            $type = $r->getType()->getName();
            if('int' === $type){
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

        $this->validateSeason();

        $this->matches = $matches;

        return $this;
    }

    public function parse(string $data, bool $intervalMode=false): ExtDateInterface
    {
        if(false === $intervalMode && "" === $data){
            throw new \InvalidArgumentException("Can't create EDTF from empty string.");
        }
        if (false !== strpos($data, '/')) {
            return Interval::from($data);
        }

        $setRegexPattern = "/(?x)
                             (?<openFlag>[\[|\{])
                             (?<value>.*)
                             (?<closeFlag>[\]|\}])
                            /";

        preg_match($setRegexPattern, $data, $matches);

        if(count($matches) > 0){
            return Set::from($matches);
        }

        $this->doParse($data, $intervalMode);

        if(!is_null($this->getHour())){
            return ExtDateTime::from($this);
        }
        elseif(null !== $this->yearSignificantDigit){
            return Interval::createSignificantDigitInterval($this);
        }
        elseif($this->season > 0){
            /**
             * @psalm-suppress PossiblyNullArgument
             */
            return new Season($this->yearNum, $this->season);
        }
        return ExtDate::from($this);
    }

    public function getInput(): string
    {
        return $this->input;
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

    public function getYearUnspecified(): int
    {
        return $this->yearUnspecified;
    }

    public function getMonthUnspecified(): int
    {
        return $this->monthUnspecified;
    }

    public function getDayUnspecified(): int
    {
        return $this->dayUnspecified;
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
}