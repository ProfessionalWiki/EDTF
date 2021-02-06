<?php

namespace EDTF\Utils\DatetimeFactory;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;

/**
 * This factory allows us to avoid calling Carbon static methods (i.e. Carbon::create(...)) from code.
 * Thus we can mock Carbon instances in unit tests.
 *
 * @package EDTF\Utils
 */
class CarbonFactory implements DatetimeFactoryInterface
{
    /**
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hour
     * @param int $minute
     * @param int $second
     * @param null $tz
     * @return Carbon|false
     * @throws DatetimeFactoryException
     */
    public function create($year = 0, $month = 1, $day = 1, $hour = 0, $minute = 0, $second = 0, $tz = null)
    {
        try {
            $c = Carbon::create($year, $month, $day, $hour, $minute, $second, $tz);
            return $c;
        } catch (InvalidFormatException $exception) {
            throw new DatetimeFactoryException($exception->getMessage());
        }
    }
}