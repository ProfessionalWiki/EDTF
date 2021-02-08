<?php

declare(strict_types=1);

namespace EDTF;

use Carbon\Carbon;

class Set implements EdtfValue
{
    private bool $allMembers;

    private bool $earlier;

    /**
     * @var EdtfValue[]
     */
    private array $lists;

    /**
     * @var bool
     */
    private bool $later;

    private int $min;

    private int $max;

    /**
     * @param EdtfValue[] $lists
     * @param bool $allMembers
     * @param bool $earlier
     * @param bool $later
     */
    public function __construct(
        array $lists,
        bool $allMembers = false,
        bool $earlier = false,
        bool $later = false
    )
    {
        $this->lists = $lists;
        $this->allMembers = $allMembers;
        $this->earlier = $earlier;
        $this->later = $later;

		// FIXME: do not do work in the constructor
        $this->configure();
    }

    private function configure(): void
    {
        $lists = $this->lists;
        $start = $lists[0];
        $len = count($lists);
        $end = 1 === $len ? $start:$lists[$len-1];

        if($this->earlier){
            $this->min = 0;
        }else{
            $this->min = $start->getMin();
        }

        if($this->later){
            $this->max = 0;
        }else{
            $this->max = $end->getMax();
        }
    }

    /**
     * @TODO: add a way to covers with earlier or later
     */
    public function covers(EdtfValue $edtf): bool
    {
        $lists = $this->lists;
        $edtfMin = Carbon::createFromTimestamp($edtf->getMin());
        $edtfMax = Carbon::createFromTimestamp($edtf->getMax());

        foreach($lists as $list){
            $min = Carbon::createFromTimestamp($list->getMin());
            $max = Carbon::createFromTimestamp($list->getMax());
            if($edtfMin->isBetween($min,$max, true)){
                return true;
            }
            if($edtfMax->isBetween($min, $max, true)){
                return true;
            }
        }
        return false;
    }

    public function getMax(): int
    {
        return $this->max;
    }

    public function getMin(): int
    {
        return $this->min;
    }

    public function isAllMembers(): bool
    {
        return true === $this->allMembers;
    }

    public function isEarlier(): bool
    {
        return true === $this->earlier;
    }

    public function isLater(): bool
    {
        return $this->later;
    }

    public function getLists(): array
    {
        return $this->lists;
    }
}