<?php

declare(strict_types=1);

namespace EDTF;

class EDTF
{
    public static function from(string $data): object
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
            return new ExtDateTime($parser);
        }
        return new ExtDate($parser);
    }

    public static function createInterval(string $data): Interval
    {
        $pos = strrpos($data, '/');

        if(false === $pos){
            throw new \InvalidArgumentException(
                sprintf("Can't create interval from %s",$data)
            );
        }

        $interval = new Interval();
        $startDateStr = substr( $data, 0, $pos );
        $endDateStr   = substr( $data, $pos + 1 );
        $startParser = (new Parser())->parse($startDateStr);
        $endParser = (new Parser())->parse($endDateStr);

        $startDate = new ExtDate($startParser);
        $endDate = new ExtDate($endParser);

        $interval
            ->setStart($startDate)
            ->setEnd($endDate)
        ;
        return $interval;
    }

}