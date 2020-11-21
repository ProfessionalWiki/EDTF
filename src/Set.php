<?php

declare(strict_types=1);

namespace EDTF;


use EDTF\Contracts\ExtDateInterface;

class Set implements ExtDateInterface
{
    private bool $allMembers;

    private bool $earlier;

    private array $lists;
    /**
     * @var bool
     */
    private bool $later;

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