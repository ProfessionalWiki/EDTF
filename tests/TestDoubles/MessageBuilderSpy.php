<?php

declare( strict_types = 1 );

namespace EDTF\Tests\TestDoubles;

use EDTF\PackagePrivate\Humanizer\Internationalization\MessageBuilder;

class MessageBuilderSpy implements MessageBuilder {

	/**
	 * @var array<int, array<int, string>>
	 */
	private array $buildMessageCalls = [];

	public function buildMessage( string $messageKey, string ...$arguments ): string {
		$this->buildMessageCalls[] = [ $messageKey, ...$arguments ];

		return $messageKey;
	}

	/**
	 * @return array<int, array<int, string>>
	 */
	public function getBuildMessageCalls(): array {
		return $this->buildMessageCalls;
	}
}
