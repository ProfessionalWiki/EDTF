<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Functional;

use EDTF\ExampleData\ValidEdtfStrings;
use EDTF\PackagePrivate\SaneParser;
use Generator;
use PHPUnit\Framework\TestCase;
use TypeError;

/**
 * @covers \EDTF\PackagePrivate\SaneParser
 * @covers \EDTF\ParsingResult
 * @covers \EDTF\ExampleData\ValidEdtfStrings
 * @covers \EDTF\PackagePrivate\Parser\Parser
 * @covers \EDTF\PackagePrivate\Parser\ParserValidator
 */
class EdtfParserTest extends TestCase {

	public function testisValidReturnsFalseForNonEdtf(): void {
		$this->assertFalse( $this->newParser()->parse( 'not edtf' )->isValid() );

		// TODO: test more invalid cases
	}

	private function newParser(): SaneParser {
		return new SaneParser();
	}

	/**
	 * @dataProvider validValueProvider
	 */
	public function testisValidReturnsTrueForValidEdtf( string $validEdtf ): void {
		$this->assertTrue( $this->newParser()->parse( $validEdtf )->isValid() );
	}

	public function validValueProvider(): Generator {
		foreach ( ValidEdtfStrings::all() as $key => $value ) {
			yield $key => [ $value ];
		}
	}

	/**
	 * @dataProvider validValueProvider
	 */
	public function testGetInputReturnsInputValue( string $validEdtf ): void {
		$this->assertSame(
			$validEdtf,
			$this->newParser()->parse( $validEdtf )->getInput()
		);
	}

	public function testGetInputReturnsInputValueEvenWhenNotEdtf(): void {
		$this->assertSame(
			'not edtf',
			$this->newParser()->parse( 'not edtf' )->getInput()
		);
	}

	public function testGetDateTimeCausesErrorForInvalidEdtf(): void {
		$this->expectException( TypeError::class );
		$this->newParser()->parse( 'not edtf' )->getEdtfValue();
	}

	/**
	 * @dataProvider validValueProvider
	 */
	public function testGetDateTimeReturnsObjectForValidEdtf( string $validEdtf ): void {
		$this->newParser()->parse( $validEdtf )->getEdtfValue();
		$this->assertTrue( true );
	}

}
