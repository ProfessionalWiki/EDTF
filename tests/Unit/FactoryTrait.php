<?php

/** @noinspection PhpIncompatibleReturnTypeInspection */
/** @psalm-suppress all */

declare(strict_types=1);

namespace EDTF\Tests\Unit;

use EDTF\ExtDate;
use EDTF\ExtDateTime;
use EDTF\Interval;
use EDTF\PackagePrivate\Parser;
use EDTF\Season;
use EDTF\Set;

/**
 * @todo Remove this class after library become stable
 */
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

    public function createSeason(string $data): Season
    {
        return $this->parse($data);
    }

    public function createSet(string $data): Set
    {
        return $this->parse($data);
    }

    private function parse($data): object
    {
        $parser = new Parser();
        return $parser->createEdtf($data);
    }
}