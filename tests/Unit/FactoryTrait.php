<?php

declare(strict_types=1);

namespace EDTF\Tests\Unit;


use EDTF\EDTF;
use EDTF\ExtDate;
use EDTF\ExtDateTime;
use EDTF\Interval;
use EDTF\Parser;

trait FactoryTrait
{
    public function createExtDate(string $data): ExtDate
    {
        return $this->parse($data);
    }

    public function createExtDateTime(string $data): ExtDateTime
    {
        return $this->parse($data);
    }

    public function createInterval(string $data): Interval
    {
        return $this->parse($data);
    }

    private function parse($data): object
    {
        $parser = new Parser();
        return $parser->parse($data);
    }
}