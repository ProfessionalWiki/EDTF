<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Unit\PackagePrivate;

use EDTF\PackagePrivate\SaneParser;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\PackagePrivate\SaneParser
 * @covers \EDTF\ParsingResult
 */
class SaneParserTest extends TestCase {

	/**
	 * @dataProvider errorMessageProvider
	 */
	public function testInvalidEdtfResultsInErrorMessage( string $input, string $expectedError ): void {
		$parser = new SaneParser();

		$this->assertSame(
			$expectedError,
			$parser->parse( $input )->getErrorMessage()
		);
	}

	public function errorMessageProvider(): iterable {
		yield [ '', "Can't create EDTF from empty string" ];
		yield [ '../..', 'Interval needs to have one normal side' ];
	}

}
