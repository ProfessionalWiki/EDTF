<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Functional;

use EDTF\ExampleData\ValidEdtfStrings;
use EDTF\PackagePrivate\Validator;
use Generator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\PackagePrivate\Validator
 * @covers \EDTF\ExampleData\ValidEdtfStrings
 * @covers \EDTF\PackagePrivate\Parser\Parser
 * @covers \EDTF\PackagePrivate\Parser\ParserValidator
 */
class EdtfValidatorTest extends TestCase {

	/**
	 * @dataProvider validValueProvider
	 */
	public function testValidEdtf( string $validEdtf ): void {
		$this->assertTrue(
			Validator::newInstance()->isValidEdtf( $validEdtf )
		);
	}

	public function validValueProvider(): Generator {
		foreach ( ValidEdtfStrings::all() as $key => $value ) {
			yield $key => [ $value ];
		}
	}

	/**
	 * @dataProvider invalidValueProvider
	 */
	public function testInvalidEdtf( string $invalidEdtf ): void {
		$this->assertFalse(
			Validator::newInstance()->isValidEdtf( $invalidEdtf )
		);
	}

	public function invalidValueProvider(): Generator {
		yield 'empty string' => [ '' ];
		yield 'Question mark (https://github.com/ProfessionalWiki/EDTF/issues/74)' => [ '?' ];
		yield 'random stuff' => [ '~=[,,_,,]:3' ];

		yield 'stuff after valid date' => [ '1985wtf' ];
		yield 'stuff before valid date' => [ 'wtf1985' ];
		yield 'stuff inside valid date' => [ '19wtf85' ];

		yield 'day too high' => [ '2021-01-32' ];
		yield 'month too high' => [ '2021-13-01' ];
		yield 'too many days for non-leap year' => [ '1900-02-29' ];

		yield 'different precision in set' => [ '{1987-10..1988}' ];
		yield 'later start than end in set range' => [ '{2002..2001}' ];

		yield 'later start than end in interval' => [ '2002/2001' ];

		foreach ( ValidEdtfStrings::all() as $value ) {
			yield [ 'invalid ' . $value ];
			yield [ $value . 'invalid' ];
		}
	}

}
