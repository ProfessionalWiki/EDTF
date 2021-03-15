<?php

declare( strict_types = 1 );

namespace EDTF\PackagePrivate\Humanizer\Internationalization;

class FallbackMessageBuilder extends ArrayMessageBuilder implements MessageBuilder
{
    /**
     * @var MessageBuilder
     */
    private MessageBuilder $builder;

    public function __construct(MessageBuilder $builder, array $messages)
    {
        parent::__construct($messages);
        $this->builder = $builder;
    }

    /**
     * @throws UnknownMessageKey
     */
    public function buildMessage(string $messageKey, string ...$arguments): string
    {
        try {
            $message = $this->builder->buildMessage($messageKey, ...$arguments);
        } catch (UnknownMessageKey $exception) {
            $message = $this->getTranslation($messageKey, ...$arguments);
        }

        return $message;
    }
}