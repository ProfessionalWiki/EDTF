<?php

declare( strict_types = 1 );

namespace EDTF\PackagePrivate\Parser;

/**
 * @internal
 */
class ParserValidator {

	private const VALID_SEASON_MIN = 21;
	private const VALID_SEASON_MAX = 41;

	private Parser $parser;

	private array $messages = [];

	public function __construct( Parser $parser ) {
		$this->parser = $parser;
	}

	public function isValid(): bool {
		$this->validateInput();
		$this->validateSeason();

		return 0 === count( $this->messages );
	}

	private function validateInput(): void {
		$parser = $this->parser;
		$input = $parser->getInput();
		$matches = $parser->getMatches();

		$hasValue = false;
		/** @var mixed $v
		 * Need this to satisfy Psalm check
		 */
		foreach ( $matches as $k => $v ) {
			if ( !is_string( $v ) ) {
				$this->messages[] = "Invalid data format: $k must be a string";
				break;
			}
			if ( "" != $v ) {
				$hasValue = true;
			}
		}

		if ( !$hasValue ) {
			$this->messages[] = "Invalid edtf format $input";
		}
	}

	private function validateSeason(): void {
		$season = $this->parser->getParsedData()->getDate()->getSeason();

		if ( $season > 0 && $this->isOutsideValidRange( $season ) ) {
			$this->messages[] = "Invalid season number $season in {$this->parser->getInput()} is out of range. Accepted season number is between 21-41";
		}
	}

	private function isOutsideValidRange( int $season ): bool {
		return $season < self::VALID_SEASON_MIN || $season > self::VALID_SEASON_MAX;
	}

	public function getMessages(): string {
		return implode( "\n", $this->messages );
	}
}