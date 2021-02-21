<?php

declare( strict_types = 1 );

namespace EDTF\PackagePrivate\Humanizer\Internationalization;

class ArrayMessageBuilder implements MessageBuilder {

	/**
	 * @var array<string, string>
	 */
	private array $messages;

	/**
	 * @param array<string, string> $messages
	 */
	public function __construct( array $messages ) {
		$this->messages = $messages;
	}

	public function buildMessage( string $messageKey, string ...$arguments ): string {
		return str_replace( '$1', $arguments[0], $this->messages[$messageKey] );
	}

}
