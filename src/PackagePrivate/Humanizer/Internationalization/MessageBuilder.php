<?php

declare( strict_types = 1 );

namespace EDTF\PackagePrivate\Humanizer\Internationalization;

interface MessageBuilder {

	public function buildMessage( string $messageKey, string ...$arguments ): string;

}
