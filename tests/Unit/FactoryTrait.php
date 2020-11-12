<?php

declare(strict_types=1);

namespace EDTF\Tests\Unit;


use EDTF\ExtDateTime;
use EDTF\Interval;
use EDTF\Parser;

trait FactoryTrait
{
    public function createExtDateTime(string $data): ExtDateTime
    {
        return (new Parser())->createExtDateTime($data);
    }

    public function createInterval(string $data): Interval
    {
        return (new Parser())->createInterval($data);
    }
}