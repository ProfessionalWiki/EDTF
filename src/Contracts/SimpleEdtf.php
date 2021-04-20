<?php

namespace EDTF\Contracts;

interface SimpleEdtf
{
    public function getYear(): ?int;

    public function getMonth(): ?int;

    public function getDay(): ?int;

    public function getSeason(): ?int;
}