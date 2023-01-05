<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Unit\PackagePrivate\Humanizer\Internationalization;

use EDTF\PackagePrivate\Humanizer\Internationalization\ArrayMessageBuilder;
use EDTF\PackagePrivate\Humanizer\Internationalization\UnknownMessageKey;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\PackagePrivate\Humanizer\Internationalization\ArrayMessageBuilder
 */
class ArrayMessageBuilderTest extends TestCase {

	public function testWhenMessageKeyIsNotKnown_exceptionIsThrown(): void {
		$builder = new ArrayMessageBuilder( [] );

		$this->expectException( UnknownMessageKey::class );
		$builder->buildMessage( 'unknown-message' );
	}

	public function testSimpleMessage(): void {
		$builder = new ArrayMessageBuilder(
			[
				'wrong-key' => 'Wrong result',
				'right-key' => 'Right result',
				'another-wrong-key' => 'Another wrong result',
			]
		);

		$this->assertSame(
			'Right result',
			$builder->buildMessage( 'right-key' )
		);
	}

	public function testParameters(): void {
		$builder = new ArrayMessageBuilder(
			[
				'right-key' => 'This was written by $2 on $1 ($1)'
			]
		);

		$this->assertSame(
			'This was written by Jeroen De Dauw on 2021-02-21 (2021-02-21)',
			$builder->buildMessage( 'right-key', '2021-02-21', 'Jeroen De Dauw' )
		);

	}

	public function testPluralArgumentIsSingular(): void {
		$builder = new ArrayMessageBuilder(
			[
				"edtf-day-and-year" => "$1e{{PLURAL:$1|r|}} jour d’un mois inconnu de $2"
			]
		);

		$this->assertSame(
			'1er jour d’un mois inconnu de 1985',
			$builder->buildMessage( 'edtf-day-and-year', '1', '1985' )
		);
	}

	public function testPluralArgumentIsPlural(): void {
		$builder = new ArrayMessageBuilder(
			[
				"edtf-day-and-year" => "$1e{{PLURAL:$1|r|}} jour d’un mois inconnu de $2"
			]
		);

		$this->assertSame(
			'3e jour d’un mois inconnu de 1985',
			$builder->buildMessage( 'edtf-day-and-year', '3', '1985' )
		);
	}

	public function testPluralMultipleArguments(): void {
		$builder = new ArrayMessageBuilder(
			[
				"multiple-arguments" => "day{{PLURAL:$1||s}} $1 and $2 month{{PLURAL:$2||s}}",
			]
		);

		$this->assertSame(
			'day 1 and 3 months',
			$builder->buildMessage( 'multiple-arguments', '1', '3' )
		);
	}

	public function testPluralMultipleArgumentsInverted(): void {
		$builder = new ArrayMessageBuilder(
			[
				"multiple-arguments-inverted" => "day{{PLURAL:$1||s}} $1 and $2 month{{PLURAL:$2||s}}",
			]
		);

		$this->assertSame(
			'days 3 and 1 month',
			$builder->buildMessage( 'multiple-arguments-inverted', '3', '1' )
		);
	}

	public function testPluralMultipleArgumentsMissingParameter(): void {
		$builder = new ArrayMessageBuilder(
			[
				"multiple-arguments-missing-parameter" => "first parameter ($1) is {{PLURAL:$1|singular|plural}} and second parameter ($2) is {{PLURAL:$2|singular|plural}}",
			]
		);

		$this->assertSame(
			'first parameter (3) is plural and second parameter ($2) is ',
			$builder->buildMessage( 'multiple-arguments-missing-parameter', '3' )
		);

	}

}
