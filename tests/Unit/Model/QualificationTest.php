<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Unit\Model;

use EDTF\Model\Qualification;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\Model\Qualification
 * @package EDTF\Tests\Unit
 */
class QualificationTest extends TestCase {

	public function testDefaultPartValueShouldBeUndefined() {
		$q = new Qualification();
		$this->assertTrue( $q->undefined( 'year' ) );
		$this->assertTrue( $q->undefined( 'month' ) );
		$this->assertTrue( $q->undefined( 'day' ) );
	}

	public function testUncertainThrowsExceptionOnInvalidPart() {
		$this->expectException( InvalidArgumentException::class );
		$q = new Qualification( Qualification::UNCERTAIN );
		$q->uncertain( 'foo' );
	}

	public function testUncertain() {
		$q = new Qualification( Qualification::UNCERTAIN );
		$this->assertTrue( $q->uncertain( 'year' ) );
	}

	public function testUncertainWithNullPart() {
		$q = new Qualification();
		$this->assertFalse( $q->uncertain() );

		$q = new Qualification( Qualification::UNCERTAIN );
		$this->assertTrue( $q->uncertain() );
	}

	public function testApproximate() {
		$q = new Qualification( Qualification::APPROXIMATE );
		$this->assertTrue( $q->approximate( 'year' ) );
	}

	public function testApproximateWithNullPart() {
		$q = new Qualification( Qualification::UNDEFINED, Qualification::APPROXIMATE );
		$this->assertTrue( $q->approximate() );
		$this->assertFalse( $q->approximate( 'year' ) );
		$this->assertTrue( $q->approximate( 'month' ) );
		$this->assertFalse( $q->approximate( 'day' ) );
	}

	public function testApproximateThrowExceptionOnInvalidPart() {
		$this->expectException( InvalidArgumentException::class );
		$q = new Qualification( Qualification::APPROXIMATE );
		$q->uncertain( 'foo' );
	}

	public function testUncertainAndApproximate() {
		$q = new Qualification( Qualification::UNCERTAIN_AND_APPROXIMATE );
		$this->assertTrue( $q->uncertain( 'year' ) );
		$this->assertTrue( $q->approximate( 'year' ) );
	}
}
