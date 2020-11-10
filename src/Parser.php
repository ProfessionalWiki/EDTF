<?php

declare(strict_types = 1);

namespace EDTF;


use EDTF\Contracts\DateTimeInterface;

class Parser
{
    private string $regexPattern = "/(?x) # Turns on free spacing mode for easier readability

					# Year start
						(?<year>
							(?<yearNum>[+-]?(?:\d+e\d+|[0-9u][0-9ux]*))
							(?>S # Literal S letter. It is for the significant digit indicator
							(?<yearPrecision>\d+))?
							(?<yearEnd>\)?[~?]{0,2})
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
						(?<dayEnd>[)~?]*)
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

    public function parse(string $data): DateTimeInterface
    {
        if (false !== strpos($data, '/')) {
            return $this->createInterval($data);
        }
        return $this->createExtDateTime($data);
    }

    private function createInterval(string $data): DateTimeInterface
    {
        $pos = strrpos($data, '/');

        if(false === $pos){
            throw new \Exception("Can't create interval from ${data}");
        }

        $startDateStr = substr( $data, 0, $pos );
        $endDateStr   = substr( $data, $pos + 1 );
        $interval = new Interval();

        $startDate = $this->createExtDateTime($startDateStr);
        $endDate = $this->createExtDateTime($endDateStr);

        $interval
            ->setLower($startDate)
            ->setUpper($endDate)
        ;
        return $interval;
    }

    /**
     * @TODO: add a way to validate and handle invalid $data format
     */
    private function createExtDateTime(string $data): ExtDateTime
    {
        $regexPattern = $this->regexPattern;
        $dateTime = new ExtDateTime();

        preg_match($regexPattern, $data, $matches);
        $dateTime->fromRegexMatches($matches);
        return $dateTime;
    }
}