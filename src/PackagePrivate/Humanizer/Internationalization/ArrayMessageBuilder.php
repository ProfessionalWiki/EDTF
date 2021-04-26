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

	/**
	 * @throws UnknownMessageKey
	 */
	public function buildMessage( string $messageKey, string ...$arguments ): string {
		return $this->getTranslation( $messageKey, ...$arguments );
	}

	/**
	 * @throws UnknownMessageKey
	 */
	protected function getTranslation( string $messageKey, string ...$arguments ): string {
		if ( !array_key_exists( $messageKey, $this->messages ) ) {
			throw new UnknownMessageKey( "Translation for key '$messageKey' was not found" );
		}

		return $this->replaceVariables( $this->messages[$messageKey], ...$arguments );
	}

	private function replaceVariables( string $messageTemplate, string ...$arguments ): string {
		return str_replace(
			array_map(
				fn( int $key ) => '$' . (string)( $key + 1 ),
				array_keys( $arguments )
			),
			$arguments,
			$messageTemplate
		);
	}

}
