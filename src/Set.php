<?php

declare(strict_types=1);

namespace EDTF;

class Set implements EdtfValue
{

	/**
	 * @var array<int, EdtfValue>
	 */
    private array $dates;
	private bool $allMembers;
	private bool $earlier;
	private bool $later;

    /**
     * @param array<int, EdtfValue> $lists
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
        $this->dates = $lists;
        $this->allMembers = $allMembers;
        $this->earlier = $earlier;
        $this->later = $later;
    }

    /**
     * @TODO: (low priority) add a way to covers with earlier or later
     */
    public function covers(EdtfValue $edtf): bool
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
        return $this->earlier;
    }

    public function hasOpenEnd(): bool
    {
        return $this->later;
    }

    public function getDates(): array
    {
        return $this->dates;
    }

    private function startElementInSet(): EdtfValue
    {
        return $this->dates[0];
    }

    private function endElementInSet(): EdtfValue
    {
        $listsCount = count($this->dates);
        return $listsCount === 1 ? $this->dates[0] : $this->dates[$listsCount - 1];
    }
}