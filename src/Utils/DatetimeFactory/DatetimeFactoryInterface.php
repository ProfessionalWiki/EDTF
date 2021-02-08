<?php

declare(strict_types=1);

namespace EDTF\Utils\DatetimeFactory;

interface DatetimeFactoryInterface
{
    /**
     * @throws DatetimeFactoryException
     */
    public function create(int $year = 0, int $month = 1, int $day = 1, int $hour = 0, int $minute = 0, int $second = 0, $tz = null);
}