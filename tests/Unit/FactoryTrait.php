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
        $parser = $this->parse($data);
        return EDTF::createExtDate($parser);
    }

    public function createExtDateTime(string $data): ExtDateTime
    {
        $parser = $this->parse($data);
        return EDTF::createExtDateTime($parser);
    }

    public function createInterval(string $data): Interval
    {
        return EDTF::createInterval($data);
    }

    private function parse($data): Parser
    {
        $parser = new Parser();
        $parser->parse($data);

        return $parser;
    }
}