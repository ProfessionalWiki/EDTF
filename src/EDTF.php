<?php

declare(strict_types=1);

namespace EDTF;

use EDTF\Contracts\DateTimeInterface;

class EDTF
{
    public static function from(string $data): DateTimeInterface
    {
        $parser = new Parser();
        return $parser->parse($data);
    }
}