<?php

namespace EDTF\Tests\Unit\Model;

use EDTF\Model\UnspecifiedDigit;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\Model\UnspecifiedDigit
 * @package EDTF\Tests\Unit
 */
class UnspecifiedDigitTest extends TestCase {

	public function testDefaultValue() {
		$u = new UnspecifiedDigit();
		$this->assertTrue( $u->specified( 'year' ) );
		$this->assertTrue( $u->specified( 'month' ) );
		$this->assertTrue( $u->specified( 'day' ) );
	}

	public function testSpecified() {
		$u = new UnspecifiedDigit( "1980" );
		$this->assertTrue( $u->specified( 'year' ) );
	}

	public function testSpecifiedThrowExceptionWithInvalidPart() {
		$this->expectException( InvalidArgumentException::class );
		$u = new UnspecifiedDigit();
		$u->specified( 'invalid' );
	}

	public function testUnspecified() {
		$u = new UnspecifiedDigit( "19XX" );
		$this->assertTrue( $u->unspecified( 'year' ) );
		$this->assertSame( 2, $u->getYear() );
	}

	public function testUnspecifiedWithNullValue() {
		$u = new UnspecifiedDigit( "", "XX", "" );
		$this->assertTrue( $u->unspecified() );
		$this->assertFalse( $u->unspecified( 'year' ) );
		$this->assertTrue( $u->unspecified( 'month' ) );
		$this->assertFalse( $u->unspecified( 'day' ) );

		$this->assertSame( 2, $u->getMonth() );
	}

	public function testUnspecifiedThrowExceptionWithInvalidPart() {
		$this->expectException( InvalidArgumentException::class );

		$u = new UnspecifiedDigit();
		$u->unspecified( 'invalid' );
	}
}
