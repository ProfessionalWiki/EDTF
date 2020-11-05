<?php

declare(strict_types = 1);

namespace EDTF;


class Parser
{
    /**
     * @var string
     */
    private $regexPattern = "/(?x) # Turns on free spacing mode for easier readability

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

    /**
     * @var string
     */
    private $data;

    /**
     * Parser constructor.
     *
     * @param string $data
     */
    public function __construct(string $data)
    {
        $this->data = $data;
    }

    /**
     * @param string $data
     * @return Parser
     */
    public static function from(string $data)
    {
        return new Parser($data);
    }

    /**
     * @return DateTime
     * @throws \Exception
     */
    public function parseDateTime()
    {
        $data = $this->data;
        $regexPattern = $this->regexPattern;
        $dateTime = new DateTime();

        $fields = [
            'year', 'month', 'day','hour', 'minute','second',
            'tzSign','tzHour','tzMinute'
        ];

        preg_match($regexPattern,$data, $matches);

        foreach($fields as $field){
            if(isset($matches[$field])){
                $setter = 'set'.$field;
                call_user_func_array([$dateTime,$setter],[$matches[$field]]);
            }
        }

        if(isset($matches['tzUtc'])){
            $tz = $matches['tzUtc'];
            $timezone = $tz == 'Z' ? "UTC":$tz;
            $dateTime->setTimezone($timezone);
        }
        if(isset($matches['tzSign'])){
            $timezone = $matches['tzSign'].$matches['tzHour'].':'.$matches['tzMinute'];
            $dateTime->setTimezone($timezone);
        }
        return $dateTime;
    }

}