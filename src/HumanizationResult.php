<?php

declare( strict_types = 1 );

namespace EDTF;

class HumanizationResult {

	public static function newNonHumanized(): self {
		return new self( false );
	}

	public static function newSimpleHumanization( string $humanization ): self {
		$instance = new self( true );
		$instance->simpleHumanization = $humanization;
		return $instance;
	}

	/**
	 * @param string[] $humanization
	 */
	public static function newStructuredHumanization( array $humanization ): self {
		return new self( true, $humanization );
	}

	private bool $wasHumanized;
	private ?string $simpleHumanization = null;
	private ?string $contextMessage = null;
	/** @var string[] */
	private array $structuredHumanization;

	/**
	 * @param string[] $structuredHumanization
	 */
	private function __construct( bool $wasHumanized, array $structuredHumanization = [] ) {
		$this->wasHumanized = $wasHumanized;
		$this->structuredHumanization = $structuredHumanization;
	}

	public function wasHumanized(): bool {
		return $this->wasHumanized;
	}

	public function isOneMessage(): bool {
		return $this->structuredHumanization === [];
	}

	public function getContextMessage(): string {
		return $this->contextMessage ?? '';
	}

	public function getSimpleHumanization(): string {
		return $this->simpleHumanization ?? '';
	}

	/**
	 * @return string[]
	 */
	public function getStructuredHumanization(): array {
		return $this->structuredHumanization;
	}

}
