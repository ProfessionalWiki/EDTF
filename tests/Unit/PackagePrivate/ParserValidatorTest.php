<?php

namespace EDTF\Tests\Unit\PackagePrivate;

use EDTF\PackagePrivate\Parser\Date;
use EDTF\PackagePrivate\Parser\ParsedData;
use EDTF\PackagePrivate\Parser\Parser;
use EDTF\PackagePrivate\Parser\ParserValidator;
use EDTF\PackagePrivate\Parser\Qualification;
use EDTF\PackagePrivate\Parser\Time;
use EDTF\PackagePrivate\Parser\Timezone;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\PackagePrivate\Parser\ParserValidator
 * @package EDTF\Tests\Unit\PackagePrivate
 */
class ParserValidatorTest extends TestCase {

	private ParserValidator $validator;

	/**
	 * @var MockObject|Parser
	 */
	private $parser;

	public function setUp(): void {
		$this->parser = $this->createMock( Parser::class );
		$this->parser->expects( $this->any() )
			->method( 'getInput' )
			->willReturn( 'input' );
		$this->validator = new ParserValidator( $this->parser );
	}

	public function testSuccessValidation() {
		$this->parser->expects( $this->once() )
			->method( 'getMatches' )
			->willReturn( [ 'yearNum' => '1987', 'monthNum' => '12' ] );

		$date = $this->createMock( Date::class );
		$date->expects( $this->once() )
			->method( 'getSeason' )
			->willReturn( 24 );

		$parsedData = new ParsedData(
			$date,
			$this->createEmptyTime(),
			$this->createEmptyQualification(),
			$this->createEmptyTimeZone()
		);

		$this->parser->expects( $this->once() )
			->method( 'getParsedData' )
			->willReturn( $parsedData );

		$this->validator->isValid();

		$this->assertEquals( "", $this->validator->getMessages() );
	}

	/**
	 * @dataProvider invalidDataTypeProvider
	 *
	 * @param mixed $yearNum
	 * @param mixed $monthNum
	 * @param string $wrongKeyNames
	 */
	public function testDataTypeFailsInputValidation( $yearNum, $monthNum, string $wrongKeyNames ) {
		$this->parser->expects( $this->once() )
			->method( 'getMatches' )
			->willReturn( [ 'yearNum' => $yearNum, 'monthNum' => $monthNum ] );

		$this->validator->isValid();
		$this->assertStringContainsString(
			"Invalid data format: $wrongKeyNames must be a string",
			$this->validator->getMessages()
		);
	}

	public function testEmptyStringsFailInputValidation() {
		$this->parser->expects( $this->once() )
			->method( 'getMatches' )
			->willReturn( [ 'yearNum' => '', 'monthNum' => '' ] );

		$this->validator->isValid();
		$this->assertEquals( "Invalid edtf format input", $this->validator->getMessages() );
	}

	public function testInvalidSeasonValueFailsSeasonValidation() {
		$this->parser->expects( $this->once() )
			->method( 'getMatches' )
			->willReturn( [ 'yearNum' => '1987', 'monthNum' => '10' ] );

		$date = $this->createMock( Date::class );
		$date->expects( $this->once() )
			->method( 'getSeason' )
			->willReturn( 19 );

		$parsedData = new ParsedData(
			$date,
			$this->createEmptyTime(),
			$this->createEmptyQualification(),
			$this->createEmptyTimeZone()
		);

		$this->parser->expects( $this->once() )
			->method( 'getParsedData' )
			->willReturn( $parsedData );

		$this->validator->isValid();
		$this->assertEquals(
			"Invalid season number 19 in input is out of range. Accepted season number is between 21-41",
			$this->validator->getMessages()
		);
	}

	public function invalidDataTypeProvider() {
		return [
			[ 1987, '10', 'yearNum' ],
			[ '1987', 10, 'monthNum' ],
			[ 10.0, 12, 'yearNum' ],
			[ null, '10', 'yearNum' ],
			[ false, null, 'yearNum' ]
		];
	}

	private function createEmptyQualification(): Qualification {
		return new Qualification(
			null,
			null,
			null,
			null,
			null,
			null
		);
	}

	private function createEmptyTime(): Time {
		return new Time( null, null, null );
	}

	private function createEmptyTimeZone(): Timezone {
		return new Timezone( null, null, null, null );
	}
}
