<?php

declare(strict_types=1);

namespace EDTF;

class Set implements EdtfValue
{
    private array $lists;
	private bool $allMembers;
	private bool $earlier;
	private bool $later;

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
    }

    /**
     * @TODO: add a way to covers with earlier or later
     */
    public function covers(EdtfValue $edtf): bool
    {
        foreach($this->lists as $list){
            if ($list->covers($edtf)) {
                return true;
            }
        }

        return false;
    }

    public function getMax(): int
    {
        return $this->isLater() ? 0 : $this->endElementInSet()->getMax();
    }

    public function getMin(): int
    {
        return $this->isEarlier() ? 0 : $this->startElementInSet()->getMin();
    }

    public function isAllMembers(): bool
    {
        return $this->allMembers;
    }

    public function isEarlier(): bool
    {
        return $this->earlier;
    }

    public function isLater(): bool
    {
        return $this->later;
    }

    public function getLists(): array
    {
        return $this->lists;
    }

    private function startElementInSet(): EdtfValue
    {
        return $this->lists[0];
    }

    private function endElementInSet(): EdtfValue
    {
        $listsCount = count($this->lists);
        return $listsCount === 1 ? $this->lists[0] : $this->lists[$listsCount - 1];
    }
}