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
    public function create($year = 0, $month = 1, $day = 1, $hour = 0, $minute = 0, $second = 0, $tz = null);
}