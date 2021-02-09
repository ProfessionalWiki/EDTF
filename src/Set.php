<?php

declare(strict_types=1);

namespace EDTF;

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

    private ?int $min = null;

    private ?int $max = null;

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
        if (null == $this->max) {
            if ($this->isLater()) {
                $this->max = 0;
            } else {
                $this->max = $this->endElementInSet()->getMax();
            }
        }

        return $this->max;
    }

    public function getMin(): int
    {
        if (null === $this->min) {
            $this->min = $this->isEarlier() ? 0 : $this->startElementInSet()->getMin();
        }

        return $this->min;
    }

    public function getType(): string
    {
        return 'Set';
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