<?php

declare(strict_types = 1);

namespace EDTF\PackagePrivate;

use EDTF\EdtfValue;
use EDTF\ExtDate;
use EDTF\ExtDateTime;
use EDTF\Interval;
use EDTF\Qualification;
use EDTF\Season;
use EDTF\Set;
use EDTF\UnspecifiedDigit;

/**
 * TODO: there might be cohesive sets of code to extract, for instance QualificationParser
 * TODO: remove public getters if they are not needed (likely most are not)
 * TODO: make builder methods private where possible
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
    	// TODO: avoid file read every time an instance is created
        $patterns = file_get_contents(__DIR__.'/../../config/regex.txt');
        $this->regexPattern = '/'.$patterns.'/';
    }

    public function parse(string $input, bool $intervalMode = false): self
    {
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
	 * @return EdtfValue
	 * @throws \InvalidArgumentException
	 */
    public function createEdtf(string $input, bool $intervalMode=false): EdtfValue
    {
        if (false !== strpos($input, '/')) {
            return $this->buildInterval($input);
        }elseif(false !== strpos($input, '{') || false !== strpos($input, '[')){
            return $this->buildSet($input);
        }

        $this->parse($input, $intervalMode);

        if($this->hour !== null){
            return $this->buildDateTime();
        }
        elseif($this->yearSignificantDigit !== null){
            return $this->createSignificantDigitInterval();
        }
        elseif($this->season !== 0){
            return $this->buildSeason();
        }
        return $this->buildDate();
    }

	public function buildDate(): ExtDate
	{
		return new ExtDate(
			$this->yearNum,
			$this->monthNum,
			$this->dayNum,
			$this->buildQualification(),
			$this->buildUnspecifiedDigit(),
			$this->intervalType
		);
	}

	public function buildUnspecifiedDigit(): UnspecifiedDigit
	{
		return new UnspecifiedDigit(
			$this->year,
			$this->month,
			$this->day
		);
	}

	public function buildDateTime(): ExtDateTime
	{
		$tzSign = "Z" == $this->tzUtc ? "Z" : $this->tzSign;

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

	private function buildSeason(): Season
	{
		return new Season($this->yearNum, $this->season);
	}

	public function buildQualification(): Qualification
	{
		// TODO: use fields directly

		$year = Qualification::UNDEFINED;
		$month = Qualification::UNDEFINED;
		$day = Qualification::UNDEFINED;

		if(!is_null($this->getYearCloseFlag())
			|| !is_null($this->getMonthCloseFlag())
			|| !is_null($this->getDayCloseFlag())
		){
			$includeYear = false;
			$includeMonth = false;
			$includeDay = false;
			$q = Qualification::UNDEFINED;

			if(!is_null($this->getYearCloseFlag())){
				// applied only to year
				$includeYear = true;
				$q = self::genQualificationValue($this->getYearCloseFlag());
			}elseif(!is_null($this->getMonthCloseFlag())){
				// applied only to year, and month
				$includeYear = true;
				$includeMonth = true;
				$q = self::genQualificationValue($this->getMonthCloseFlag());
			}elseif(!is_null($this->getDayCloseFlag())){
				// applied to year, month, and day
				$includeYear = true;
				$includeMonth = true;
				$includeDay = true;
				$q = self::genQualificationValue($this->getDayCloseFlag());
			}

			$year = $includeYear ? $q:$year;
			$month = $includeMonth ? $q:$month;
			$day = $includeDay ? $q:$day;
		}

		// handle level 2 qualification
		if(!is_null($this->getYearOpenFlag())){
			$year = self::genQualificationValue($this->getYearOpenFlag());
		}
		if(!is_null($this->getMonthOpenFlag())){
			$month = self::genQualificationValue($this->getMonthOpenFlag());
		}
		if(!is_null($this->getDayOpenFlag())){
			$day = self::genQualificationValue($this->getDayOpenFlag());
		}
		return new Qualification($year, $month, $day);
	}

	// TODO
	private static array $map = [
		'%' => Qualification::UNCERTAIN_AND_APPROXIMATE,
		'?' => Qualification::UNCERTAIN,
		'~' => Qualification::APPROXIMATE,
	];

	// TODO
	private static function genQualificationValue(?string $flag = null): int
	{
		assert(is_string($flag));
		return (int)self::$map[$flag];
	}

	public function buildSet(string $input): Set
	{
		preg_match(
			"/(?x)
					 (?<openFlag>[\[|\{])
					 (?<value>.*)
					 (?<closeFlag>[\]|\}])
					/",
			$input,
			$matches
		);
		if(0 === count($matches)){
			throw new \InvalidArgumentException(sprintf(
				"Can't create EDTF::Set from '%s' input", $input
			));
		}

		$openFlag = $matches['openFlag'];
		$values = explode(",",$matches['value']);
		$allMembers = '[' === $openFlag ? false:true;
		$earlier = false;
		$later = false;

		$sets = [];
		foreach($values as $value){
			if(false === strpos($value, '..')){
				$sets[] = (new Parser())->createEdtf($value);
			}
			elseif(false != preg_match('/^\.\.(.+)/', $value, $matches)){
				// earlier date like ..1760-12-03
				$earlier = true;
				$sets[] = (new Parser())->createEdtf($matches[1]);
			}
			elseif(false != preg_match('/(.+)\.\.$/', $value, $matches)){
				// later date like 1760-12..
				$later = true;
				$sets[] = (new Parser())->createEdtf($matches[1]);
			}
			elseif(false != preg_match('/(.+)\.\.(.+)/', $value, $matches)){
				$start = (int)$matches[1];
				$end = (int)$matches[2];
				for($i=$start;$i<=$end;$i++){
					$sets[] = (new Parser())->createEdtf((string)$i);
				}
			}
			continue;
		}

		return new Set($sets, $allMembers, $earlier, $later);
	}

	public function buildInterval(string $input): Interval
	{
		$pos = strrpos($input, '/');

		if(false === $pos){
			throw new \InvalidArgumentException(
				sprintf("Can't create interval from %s",$input)
			);
		}
		$startDateStr = substr( $input, 0, $pos );
		$endDateStr   = substr( $input, $pos + 1 );

		return new Interval(
			$this->buildDateUsingIntervalMode($startDateStr),
			$this->buildDateUsingIntervalMode($endDateStr)
		);
	}

	private function buildDateUsingIntervalMode( string $dateString ): ExtDate {
		$parser = new Parser();
		$parser->parse($dateString, true);
		return $parser->buildDate();
	}

	public function createSignificantDigitInterval(): Interval
	{
		$strEstimated = (string)$this->yearNum;
		$significantDigit = $this->yearSignificantDigit;
		assert(is_int($significantDigit));
		$year = substr($strEstimated,0, strlen($strEstimated) - $significantDigit);
		$startYear = $year.(str_repeat("0", $significantDigit));
		$endYear = $year.(str_repeat("9", $significantDigit));

		return new Interval(
			new ExtDate((int)$startYear),
			new ExtDate((int)$endYear),
			$significantDigit,
			$this->yearNum
		);
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
}