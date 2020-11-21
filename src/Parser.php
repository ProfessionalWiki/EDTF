<?php

declare(strict_types = 1);

namespace EDTF;


use EDTF\Contracts\ExtDateInterface;

class Parser
{
    private string $regexPattern = "/(?x) # Turns on free spacing mode for easier readability

					# Year start
						(?<year>
						    (?<yearOpenFlag>[~?%]{0,2})
							(?<yearNum>
							    [+-]?               # optional sign
							    (?:
							        \d+E\d+         # exponential form, no 'X' allowed
							        |[0-9UX]* # decimal form, 'X' allowed
							    )
							)
							(?>S # Literal S letter. It is for the significant digit indicator
							(?<yearSignificantDigit>\d+))?
							(?<yearCloseFlag>\)?[~%?]{0,2})
						)
					# Year end

					(?>- # Literal - (hyphen)

					# Month start
                        (?<month>
                            (?<monthOpenFlag>[~?%]{0,2})
                            (?<monthOpenParents>\(+)?
							(?<monthNum>
								(?>1[0-9UX]|[0UX][0-9UX]|[0-9][0-9])
							)
							(?>\^
								(?<seasonQualifier>[\P{L}\P{N}\P{M}:.-]+)
                            )?
                            (?<monthCloseFlag>[~?%]{0,2})
						)
					# Month end

					(?>- # Literal - (hyphen)

					# Day start
						(?<day>
						(?<dayOpenFlag>[~?%]{0,2})
						(?<dayOpenParents>\(+)?
						(?<dayNum>(?>[012UX][0-9UX]|3[01UX])))
						(?<dayCloseFlag>[~?%]{0,2})
                        (?<dayEnd>[)~%?]*)
                    # Day end

					# Time Start #
					(?>T # Literal T
						(?<hour>2[0-3]|[01][0-9]):
						(?<minute>[0-5][0-9]):
						(?<second>[0-5][0-9])
                        (?>
                            (?<tzUtc>Z)|
						    (?<tzSign>[+-])
                            (?<tzHour>[01][0-9])
                            (?>: #optional minute
                                (?<tzMinute>[0-5][0-9])
                            )? # end optional minute
                        )? # end optional timezone
                    )? # end time

                    )?
                )?$ # Others end #
			/";

    private string $setRegexPattern = "/(?x)
        (?<openFlag>[\[|\{])
        (?<value>.*)
        (?<closeFlag>[\]|\}])
    /";

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

    private int $yearQualification = 0;
    private int $monthQualification = 0;
    private int $dayQualification = 0;

    private int $yearUnspecified = 0;
    private int $monthUnspecified = 0;
    private int $dayUnspecified = 0;

    private int $intervalType = 0;

    private ?int $yearSignificantDigit = null;

    private ?array $sets = null;

    private function configureQualification(array $matches): void
    {
        if("" != $matches['yearCloseFlag']
            || (isset($matches['monthCloseFlag']) && "" != $matches['monthCloseFlag'])
            || (isset($matches['dayCloseFlag']) && "" != $matches['dayCloseFlag'])
        ){
            $includeYear = false;
            $includeMonth = false;
            $includeDay = false;
            $q = Qualification::UNDEFINED;

            if("" != $matches['yearCloseFlag']){
                // applied only to year
                $includeYear = true;
                $q = $this->getQualificationValue((string)$matches['yearCloseFlag']);
            }elseif("" != $matches['monthCloseFlag']){
                // applied only to year, and month
                $includeYear = true;
                $includeMonth = true;
                $q = $this->getQualificationValue((string)$matches['monthCloseFlag']);
            }elseif("" != $matches['dayCloseFlag']){
                // applied to year, month, and day
                $includeYear = true;
                $includeMonth = true;
                $includeDay = true;
                $q = $this->getQualificationValue((string)$matches['dayCloseFlag']);
            }

            $this->yearQualification = $includeYear ? $q:Qualification::UNDEFINED;
            $this->monthQualification = $includeMonth ? $q:Qualification::UNDEFINED;
            $this->dayQualification = $includeDay ? $q:Qualification::UNDEFINED;
        }

        // handle level 2 qualification
        if(isset($matches['yearOpenFlag']) && "" != $matches['yearOpenFlag']){
            $this->yearQualification = $this->getQualificationValue((string)$matches['yearOpenFlag']);
        }
        if(isset($matches['monthOpenFlag']) && "" != $matches['monthOpenFlag']){
            $this->monthQualification = $this->getQualificationValue((string)$matches['monthOpenFlag']);
        }
        if(isset($matches['dayOpenFlag']) && "" != $matches['dayOpenFlag']){
            $this->dayQualification = $this->getQualificationValue((string)$matches['dayOpenFlag']);
        }
    }

    private function getQualificationValue(string $value): int
    {
        if('?' === $value){
            return Qualification::UNCERTAIN;
        }elseif('~' === $value){
            return Qualification::APPROXIMATE;
        }elseif('%' === $value){
            return Qualification::UNCERTAIN_AND_APPROXIMATE;
        }
        throw new \InvalidArgumentException(sprintf(
            'Invalid qualification flag "%s".', $value
        ));
    }

    private function createInterval(string $data): ExtDateInterface
    {
        $pos = strrpos($data, '/');

        if(false === $pos){
            throw new \InvalidArgumentException(
                sprintf("Can't create interval from %s",$data)
            );
        }
        $startDateStr = substr( $data, 0, $pos );
        $endDateStr   = substr( $data, $pos + 1 );

        $startDate = $this->createIntervalPair($startDateStr);
        $endDate = $this->createIntervalPair($endDateStr);

        return new Interval($startDate, $endDate);
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

    private function doParse(string $data, bool $intervalMode = false): object
    {
        $data = strtoupper($data);
        if($intervalMode && "" === $data){
            $this->intervalType = Interval::UNKNOWN;
            return $this;
        }

        if($intervalMode && ".." === $data) {
            $this->intervalType = Interval::OPEN;
            return $this;
        }

        $stringTypes = [
            'tzUtc',
            'tzSign',
        ];
        $unspecifiedParts = ['yearNum', 'monthNum', 'dayNum'];

        preg_match($this->regexPattern, $data, $matches);

        $this->validateInput($data, $matches);

        // @TODO: if possible refactor this loop to use pure function like configureYear, configureMonth, etc.
        foreach($matches as $name => $value){
            if(is_int($name) || $value === ""){
                continue;
            }

            if(in_array($name, $unspecifiedParts)){
                // convert unspecified digit into zero
                if(false !== strpos($value, 'X')){
                    $propName = str_replace('Num','Unspecified', $name);
                    $this->$propName = UnspecifiedDigit::UNSPECIFIED;
                    $value = str_replace('X', '0', $value);
                    $value = (int)$value;

                    // convert zero value into null
                    if(0 === $value){
                        $value = null;
                    }
                }
            }
            if(!in_array($name, $stringTypes) && !is_null($value)){
                $value = (int) $value;
            }

            $this->$name = $value;
        }

        // convert month into season
        if($this->monthNum > 12){
            $this->monthNum = null;
            $this->season = (int)$matches['monthNum'];
        }

        $this->configureQualification($matches);
        $this->validateSeason();

        return $this;
    }

    /**
     * @psalm-suppress LessSpecificReturnStatement
     * @psalm-suppress MoreSpecificReturnType
     */
    private function createIntervalPair(string $data): ExtDate
    {
        $parser = new Parser();
        return $parser->parse($data, true);
    }

    private function createExtDate(): ExtDate
    {
        $q = new Qualification(
            $this->yearQualification,
            $this->monthQualification,
            $this->dayQualification
        );
        $u = new UnspecifiedDigit(
            $this->yearUnspecified,
            $this->monthUnspecified,
            $this->dayUnspecified
        );

        return new ExtDate(
            $this->yearNum,
            $this->monthNum,
            $this->dayNum,
            $q,
            $u,
            $this->intervalType
        );
    }

    private function createSet(array $matches): Set
    {
        $openFlag = (string)$matches['openFlag'];
        $values = explode(",",(string)$matches['value']);
        $allMembers = '[' === $openFlag ? false:true;
        $earlier = false;
        $later = false;

        $sets = [];
        foreach($values as $value){
            if(false === strpos($value, '..')){
                $sets[] = $this->parse($value);
            }
            elseif(false != preg_match('/^\.\.(.+)/', $value, $matches)){
                // earlier date like ..1760-12-03
                $earlier = true;
                $sets[] = $this->parse($matches[1]);
            }
            elseif(false != preg_match('/(.+)\.\.$/', $value, $matches)){
                // later date like 1760-12..
                $later = true;
                $sets[] = $this->parse($matches[1]);
            }
            elseif(false != preg_match('/(.+)\.\.(.+)/', $value, $matches)){
                $start = (int)$matches[1];
                $end = (int)$matches[2];
                for($i=$start;$i<=$end;$i++){
                    $sets[] = $this->parse((string)$i);
                }
            }
            continue;
        }

        $this->sets = $sets;
        return new Set($sets, $allMembers, $earlier, $later);
    }

    /**
     * @psalm-suppress PossiblyNullArgument
     * @psalm-suppress PossiblyNullOperand
     */
    private function createSignificantDigitInterval(): Interval
    {
        $estimated = $this->yearNum;
        $strEstimated = (string)$estimated;
        $significantDigit = $this->yearSignificantDigit;

        $year = substr($strEstimated,0, strlen($strEstimated) - $significantDigit);
        $startYear = $year.(str_repeat("0", $significantDigit));
        $endYear = $year.(str_repeat("9", $significantDigit));

        $start = new ExtDate((int)$startYear);
        $end = new ExtDate((int)$endYear);
        return new Interval($start, $end, $significantDigit, $estimated);
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

    public function parse(string $data, bool $intervalMode=false): ExtDateInterface
    {
        if(false === $intervalMode && "" === $data){
            throw new \InvalidArgumentException("Can't create EDTF from empty string.");
        }
        if (false !== strpos($data, '/')) {
            return $this->createInterval($data);
        }

        preg_match($this->setRegexPattern, $data, $matches);
        if(count($matches) > 0){
            return $this->createSet($matches);
        }

        $this->doParse($data, $intervalMode);

        if(!is_null($this->getHour())){
            return $this->createExtDateTime();
        }
        elseif(null !== $this->yearSignificantDigit){
            return $this->createSignificantDigitInterval();
        }
        elseif($this->season > 0){
            return new Season($this->yearNum, $this->season);
        }
        return $this->createExtDate();
    }

    public function createExtDateTime(): ExtDateTime
    {
        $tzSign = "Z" == $this->tzUtc ? "Z":$this->tzSign;

        return new ExtDateTime(
            $this->yearNum,
            $this->monthNum,
            $this->dayNum,
            $this->hour,
            $this->minute,
            $this->second,
            $tzSign,
            $this->tzHour,
            $this->tzMinute
        );
    }

    public function getSets(): ?array
    {
        return $this->sets;
    }

    public function getYearSignificantDigit(): ?int
    {
        return $this->yearSignificantDigit;
    }

    public function getYearQualification(): int
    {
        return $this->yearQualification;
    }

    public function getMonthQualification(): int
    {
        return $this->monthQualification;
    }

    public function getDayQualification(): int
    {
        return $this->dayQualification;
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