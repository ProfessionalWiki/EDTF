<?php

declare(strict_types = 1);

namespace EDTF;

interface ExtDateInterface
{
    /**
     * @return int unix timestamp
     */
    public function getMax(): int;

    /**
     * @return int unix timestamp
     */
    public function getMin(): int;

    public function covers(ExtDateInterface $edtf): bool;

    /**
     * @return string object type
     */
    public function getType(): string;

}