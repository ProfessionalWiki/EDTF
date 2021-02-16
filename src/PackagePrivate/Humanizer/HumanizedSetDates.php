<?php

declare( strict_types = 1 );

namespace EDTF\PackagePrivate\Humanizer;

class HumanizedSetDates {

	/**
	 * @readonly
	 * @var string[]
	 */
	public array $humanizedDates;

	/**
	 * @param string[] $humanizedDates
	 */
	public function __construct( array $humanizedDates ) {
		$this->humanizedDates = $humanizedDates;
	}

	public function shouldUseList(): bool {
		$allText = implode( '', $this->humanizedDates );

		if ( str_contains( $allText, ',' ) ) {
			return true;
		}

		return strlen( $allText ) > 50
			&& !ctype_digit( $allText );
	}

}
