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
		$messageTemplate = $this->convertPlural( $messageTemplate, ...$arguments );

		return str_replace(
			array_map(
				fn( int $key ) => '$' . (string)( $key + 1 ),
				array_keys( $arguments )
			),
			$arguments,
			$messageTemplate
		);
	}

	private function convertPlural( string $messageTemplate, string ...$arguments ): string {
		return preg_replace_callback(
			'/{{PLURAL:(.*?)\|(.*?)}}/i',
			function ( array $matches ) use ( $arguments ) {
				$argumentKey = intval( str_replace( '$', '', $matches[1] ) ) -1;

				if ( !array_key_exists( $argumentKey, $arguments ) ) {
					return "";
				}

				$forms = explode( '|', $matches[2] );
				$count = (int)$arguments[$argumentKey];

				return $forms[(int)( $count > 1 )];
			},
			$messageTemplate
		);
	}
}
