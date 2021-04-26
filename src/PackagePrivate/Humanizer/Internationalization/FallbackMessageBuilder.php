<?php

declare( strict_types = 1 );

namespace EDTF\PackagePrivate\Humanizer\Internationalization;

class FallbackMessageBuilder implements MessageBuilder {

	private MessageBuilder $primaryBuilder;

	private MessageBuilder $fallbackBuilder;

	public function __construct( MessageBuilder $primaryBuilder, MessageBuilder $fallbackBuilder ) {
		$this->primaryBuilder = $primaryBuilder;
		$this->fallbackBuilder = $fallbackBuilder;
	}

	/**
	 * @throws UnknownMessageKey
	 */
	public function buildMessage( string $messageKey, string ...$arguments ): string {
		try {
			$message = $this->primaryBuilder->buildMessage( $messageKey, ...$arguments );
		}
		catch ( UnknownMessageKey $exception ) {
			$message = $this->fallbackBuilder->buildMessage( $messageKey, ...$arguments );
		}

		return $message;
	}
}