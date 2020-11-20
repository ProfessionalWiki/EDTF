<?php

declare(strict_types = 1);

namespace EDTF;


use EDTF\Contracts\ExtDateInterface;

class Parser
{
    private string $regexPattern = "/(?x) # Turns on free spacing mode for easier readability

					# Year start
						(?<year>
						    (?<yearOpenFlags>[~?%]{0,2})
							(?<yearNum>[+-]?(?:\d+e\d+|[0-9u][0-9ux]*))
							(?>S # Literal S letter. It is for the significant digit indicator
							(?<yearPrecision>\d+))?
							(?<yearCloseFlag>\)?[~%?]{0,2})
						)
					# Year end

					(?>- # Literal - (hyphen)

					# Month start
						(?<month>
							(?<monthOpenParents>\(+)?
							(?<monthNum>
								(?>1[0-9u]|[0u][0-9u]|2[1-4])
							)
							(?>\^
								(?<seasonQualifier>[\P{L}\P{N}\P{M}:.-]+)
							)?
						)

						(?<monthEnd>(?:\)?[~?]{0,2}){0,2})
					# Month end

					(?>- # Literal - (hyphen)

					# Day start
						(?<day>
						(?<dayOpenParents>\(+)?
						(?<dayNum>(?>[012u][0-9u]|3[01u])))
						(?<dayEnd>[)~%?]*)
					# Day end

					# Others start #
						(?>T # Literal T
						(?<hour>2[0-3]|[01][0-9]):
						(?<minute>[0-5][0-9]):
						(?<second>[0-5][0-9])
						(?>(?<tzUtc>Z)|
						(?<tzSign>[+-])
						(?<tzHour>[01][0-9]):
						(?<tzMinute>[0-5][0-9]))?)?)?)?$
					# Others end #
					/";


    private ?int $year = null;
    private ?int $month = null;
    private ?int $day = null;
    private ?int $hour = null;
    private ?int $minute = null;
    private ?int $second = null;

    private ?string $tzSign = null;
    private ?int $tzMinute = null;
    private ?int $tzHour = null;
    private ?string $tzUtc = null;

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

    private function doParse(string $data): object
    {
        $stringTypes = ['tzUtc', 'tzSign'];

        preg_match($this->regexPattern, $data, $matches);

        if("" !== $data && count($matches) <= 1){
            throw new \InvalidArgumentException(
                sprintf("invalid data %s", $data)
            );
        }

        foreach($matches as $name => $value){
            if(is_int($name) || $value === ""){
                continue;
            }
            if(!in_array($name, $stringTypes)){
                $value = (int) $value;
            }
            $this->$name = $value;
        }

        return $this;
    }

    private function createIntervalPair(string $data): object
    {
        $parser = new Parser();
        return $parser->parse($data);
    }

    private function createExtDate(): ExtDate
    {
        return new ExtDate($this->getYear(), $this->getMonth(), $this->getDay());
    }

    public function parse(string $data): ExtDateInterface
    {
        if("" === $data){
            throw new \InvalidArgumentException("Can't create EDTF from empty string.");
        }
        if (false !== strpos($data, '/')) {
            return $this->createInterval($data);
        }

        $this->doParse($data);

        if(!is_null($this->getHour())){
            return $this->createExtDateTime();
        }
        return $this->createExtDate();
    }

    public function createExtDateTime(): ExtDateTime
    {
        return new ExtDateTime(
            $this->year,
            $this->month,
            $this->day,
            $this->hour,
            $this->minute,
            $this->second,
            $this->tzSign,
            $this->tzHour,
            $this->tzMinute
        );
    }

    public function getTzUtc(): ?string
    {
        return $this->tzUtc;
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