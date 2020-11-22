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

    public static function from(array $matches): self
    {
        $openFlag = (string)$matches['openFlag'];
        $values = explode(",",(string)$matches['value']);
        $allMembers = '[' === $openFlag ? false:true;
        $earlier = false;
        $later = false;

        $sets = [];
        foreach($values as $value){
            if(false === strpos($value, '..')){
                $sets[] = (new Parser())->parse($value);
            }
            elseif(false != preg_match('/^\.\.(.+)/', $value, $matches)){
                // earlier date like ..1760-12-03
                $earlier = true;
                $sets[] = (new Parser())->parse($matches[1]);
            }
            elseif(false != preg_match('/(.+)\.\.$/', $value, $matches)){
                // later date like 1760-12..
                $later = true;
                $sets[] = (new Parser())->parse($matches[1]);
            }
            elseif(false != preg_match('/(.+)\.\.(.+)/', $value, $matches)){
                $start = (int)$matches[1];
                $end = (int)$matches[2];
                for($i=$start;$i<=$end;$i++){
                    $sets[] = (new Parser())->parse((string)$i);
                }
            }
            continue;
        }

        return new Set($sets, $allMembers, $earlier, $later);
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