<?php

declare(strict_types = 1);

namespace EDTF;

class ParserValidator
{
    private Parser $parser;

    private array $messages = [];

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    public function isValid(): bool
    {
        $methods = get_class_methods(__CLASS__);
        $messages = [];

        foreach($methods as $method){
            if(false !== strpos($method, 'validate') && 'validate' !== $method){
                try{
                    call_user_func([$this, $method]);
                }catch (\Exception $e){
                    $messages[] = $e->getMessage();
                }
            }
        }

        $this->messages = $messages;
        return 0 === count($messages);
    }

    public function validateInput(): void
    {
        $parser = $this->parser;
        $input = $parser->getInput();
        $matches = $parser->getMatches();

        $hasValue = false;
        foreach($matches as $k => $v){
            assert(is_string($v));
            if("" != $v){
                $hasValue = true;
            }
        }

        if(!$hasValue){
            throw new \InvalidArgumentException(sprintf(
                'Invalid edtf format "%s".',$input
            ));
        }
    }

    public function validateSeason(): void
    {
        $season = $this->parser->getSeason();
        if($season > 0){
            if(false === ($season >= 21 && $season <= 41)){
                throw new \InvalidArgumentException(sprintf(
                    'Invalid season number "%s" in "%s" is out of range. Accepted season number is between 21-41',
                    $season,
                    $this->parser->getInput()
                ));
            }
        }
    }

    public function getMessages(): string
    {
        return implode("\n", $this->messages);
    }
}