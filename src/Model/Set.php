<?php

declare(strict_types=1);

namespace EDTF\Model;

use EDTF\Contracts\Coverable;
use EDTF\Contracts\SimpleEdtf;

class Set extends EdtfValue implements Coverable
{
	/**
	 * @var array<int, SimpleEdtf>
	 */
    private array $dates;
	private bool $allMembers;
	private bool $hasOpenStart;
	private bool $hasOpenEnd;

    /**
     * @param array<int, SimpleEdtf> $lists
     * @param bool $allMembers
     * @param bool $hasOpenStart
     * @param bool $hasOpenEnd
     */
    public function __construct(
        array $lists,
        bool $allMembers,
        bool $hasOpenStart,
        bool $hasOpenEnd
    )
    {
        $this->dates = $lists;
        $this->allMembers = $allMembers;
        $this->hasOpenStart = $hasOpenStart;
        $this->hasOpenEnd = $hasOpenEnd;
    }

    /**
     * @TODO: (low priority) add a way to covers with earlier or later
     */
    public function covers(Coverable $edtf): bool
    {
        foreach( $this->dates as $list){
            if ($list->covers($edtf)) {
                return true;
            }
        }

        return false;
    }

    public function getMax(): int
    {
        return $this->hasOpenEnd() ? 0 : $this->endElementInSet()->getMax();
    }

    public function getMin(): int
    {
        return $this->hasOpenStart() ? 0 : $this->startElementInSet()->getMin();
    }

    public function isAllMembers(): bool
    {
        return $this->allMembers;
    }

    public function hasOpenStart(): bool
    {
        return $this->hasOpenStart;
    }

    public function hasOpenEnd(): bool
    {
        return $this->hasOpenEnd;
    }

	/**
	 * @return array<int, SimpleEdtf>
	 */
    public function getDates(): array
    {
        return $this->dates;
    }

    public function isSingleElement(): bool
	{
		return count($this->getDates()) === 1;
	}

    private function startElementInSet(): SimpleEdtf
    {
        return $this->dates[0];
    }

    private function endElementInSet(): SimpleEdtf
    {
        $listsCount = count($this->dates);
        return $listsCount === 1 ? $this->dates[0] : $this->dates[$listsCount - 1];
    }
}