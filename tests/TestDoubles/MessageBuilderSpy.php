<?php

declare( strict_types = 1 );

namespace EDTF\Tests\TestDoubles;

use EDTF\PackagePrivate\Humanizer\Internationalization\MessageBuilder;

class MessageBuilderSpy implements MessageBuilder
{
    private array $buildMessageCalls = [];

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
