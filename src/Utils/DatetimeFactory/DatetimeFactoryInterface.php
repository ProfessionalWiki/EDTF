<?php

namespace EDTF\Utils\DatetimeFactory;

interface DatetimeFactoryInterface
{
    /**
     * @param $year
     * @param $month
     * @param $day
     * @param $hour
     * @param $minute
     * @param $second
     * @param $tz
     * @return mixed
     *
     * @throws DatetimeFactoryException
     */
    public function create($year, $month, $day, $hour, $minute, $second, $tz);
}