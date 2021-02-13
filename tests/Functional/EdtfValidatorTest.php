<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Functional;

use EDTF\PackagePrivate\Validator;
use EDTF\ExampleData\ValidEdtfStrings;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\PackagePrivate\Validator
 * @covers \EDTF\ExampleData\ValidEdtfStrings
 * @covers \EDTF\PackagePrivate\Parser
 * @covers \EDTF\PackagePrivate\ParserValidator
 */
class EdtfValidatorTest extends TestCase {

	/**
	 * @dataProvider validValueProvider
	 */
	public function testValidEdtf( string $validEdtf ) {
		$this->assertTrue(
			Validator::newInstance()->isValidEdtf( $validEdtf )
		);
	}

	public function validValueProvider(): \Generator {
		foreach ( ValidEdtfStrings::all() as $key => $value ) {
			yield $key => [ $value ];
		}
	}

	/**
	 * @dataProvider invalidValueProvider
	 */
	public function testInvalidEdtf( string $invalidEdtf ) {
		$this->assertFalse(
			Validator::newInstance()->isValidEdtf( $invalidEdtf )
		);
	}

	public function invalidValueProvider(): \Generator {
		yield 'empty string' => [ '' ];
		yield 'random stuff' => [ '~=[,,_,,]:3' ];

		yield 'stuff after valid date' => [ '1985wtf' ];
		yield 'stuff before valid date' => [ 'wtf1985' ];
		yield 'stuff inside valid date' => [ '19wtf85' ];

		yield 'day too high' => [ '2021-01-32' ];
		yield 'month too high' => [ '2021-13-01' ];

		foreach ( ValidEdtfStrings::all() as $key => $value ) {
			yield [ 'invalid ' . $value ];
			yield [ $value . 'invalid' ];
		}
	}

}
