<?php

declare(strict_types=1);

namespace EDTF;

use EDTF\Contracts\ExtDateInterface;

class EDTF
{
    public static function from(string $data): ExtDateInterface
    {
        if("" === $data){
            throw new \InvalidArgumentException("Can't create EDTF from empty string.");
        }
        if (false !== strpos($data, '/')) {
            return static::createInterval($data);
        }

        $parser = new Parser();
        $parser->parse($data);

        if(!is_null($parser->getHour())){
            return static::createExtDateTime($parser);
        }
        return static::createExtDate($parser);
    }

    public static function createInterval(string $data): Interval
    {
        $pos = strrpos($data, '/');

        if(false === $pos){
            throw new \InvalidArgumentException(
                sprintf("Can't create interval from %s",$data)
            );
        }
        $startDateStr = substr( $data, 0, $pos );
        $endDateStr   = substr( $data, $pos + 1 );

        $startDate = static::createIntervalPair($startDateStr);
        $endDate = static::createIntervalPair($endDateStr);
        return new Interval($startDate, $endDate);
    }

    public static function createExtDateTime(Parser $parser): ExtDateTime
    {
        return new ExtDateTime(
            $parser->getYear(),
            $parser->getMonth(),
            $parser->getDay(),
            $parser->getHour(),
            $parser->getMinute(),
            $parser->getSecond(),
            $parser->getTzSign(),
            $parser->getTzHour(),
            $parser->getTzMinute()
        );
    }

    public static function createExtDate(Parser $parser): ExtDate
    {
        return new ExtDate($parser->getYear(), $parser->getMonth(), $parser->getDay());
    }

    private static function createIntervalPair($data): ExtDate
    {
        $parser = new Parser();
        $parser->parse($data);
        return static::createExtDate($parser);
    }
}