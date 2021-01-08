<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Unit;

use EDTF\EdtfValidator;
use EDTF\ExampleData\ValidEdtfStrings;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\EdtfValidator
 */
class EdtfValidatorTest extends TestCase {

	/**
	 * @dataProvider validValueProvider
	 */
	public function testValidEdtf( string $validEdtf ) {
		$this->assertTrue(
			EdtfValidator::newInstance()->isValidEdtf( $validEdtf )
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
			EdtfValidator::newInstance()->isValidEdtf( $invalidEdtf )
		);
	}

	public function invalidValueProvider(): \Generator {
		yield 'empty string' => [ '' ];
		yield 'random stuff' => [ '~=[,,_,,]:3' ];

		yield 'stuff after valid date' => [ '1985wtf' ];
//	TODO	yield 'stuff before valid date' => [ 'wtf1985' ];
//	TODO	yield 'stuff inside valid date' => [ '19wtf85' ];


		foreach ( ValidEdtfStrings::all() as $key => $value ) {
			// TODO
			// yield [ 'invalid ' . $value ];
		}
	}

}
