<?php

declare( strict_types = 1 );

namespace EDTF\Tests\TestDoubles;

use EDTF\PackagePrivate\Humanizer\Internationalization\MessageBuilder;
use PHPUnit\Framework\TestCase;

class MessageBuilderSpy implements MessageBuilder
{
    private TestCase $testCase;

    private array $buildMessageCalls = [];

    public function __construct(TestCase $testCase)
    {
        $this->testCase = $testCase;
    }

    public function buildMessage(string $messageKey, string ...$arguments): string
    {
        $this->buildMessageCalls[] = func_get_args();

        return $messageKey;
    }

    public function getBuildMessageCalls(): array
    {
        return $this->buildMessageCalls;
    }
}
