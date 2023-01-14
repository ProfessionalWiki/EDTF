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

	public function testUncertainThrowsExceptionOnInvalidPart(): void {
		$this->expectException( InvalidArgumentException::class );
		$q = new Qualification( Qualification::UNCERTAIN );
		$q->uncertain( 'foo' );
	}

	public function testUncertain(): void {
		$q = new Qualification( Qualification::UNCERTAIN );
		$this->assertTrue( $q->uncertain( 'year' ) );
	}

	public function testUncertainWithNullPart(): void {
		$q = new Qualification();
		$this->assertFalse( $q->uncertain() );

		$q = new Qualification( Qualification::UNCERTAIN );
		$this->assertTrue( $q->uncertain() );
	}

	public function testApproximate(): void {
		$q = new Qualification( Qualification::APPROXIMATE );
		$this->assertTrue( $q->approximate( 'year' ) );
	}

	public function testApproximateWithNullPart(): void {
		$q = new Qualification( Qualification::KNOWN, Qualification::APPROXIMATE );
		$this->assertTrue( $q->approximate() );
		$this->assertFalse( $q->approximate( 'year' ) );
		$this->assertTrue( $q->approximate( 'month' ) );
		$this->assertFalse( $q->approximate( 'day' ) );
	}

	public function testApproximateThrowExceptionOnInvalidPart(): void {
		$this->expectException( InvalidArgumentException::class );
		$q = new Qualification( Qualification::APPROXIMATE );
		$q->uncertain( 'foo' );
	}

	public function testUncertainAndApproximate(): void {
		$q = new Qualification( Qualification::UNCERTAIN_AND_APPROXIMATE );
		$this->assertTrue( $q->uncertain( 'year' ) );
		$this->assertTrue( $q->approximate( 'year' ) );
	}

	public function testIsFullyKnown(): void {
		$qualification = new Qualification(
			Qualification::KNOWN,
			Qualification::KNOWN,
			Qualification::KNOWN
		);

		$this->assertTrue( $qualification->isFullyKnown() );
	}

	/**
	 * @dataProvider notFullyKnownProvider
	 */
	public function testIsNotFullyKnown( Qualification $qualification ): void {
		$this->assertFalse( $qualification->isFullyKnown() );
	}

	public function notFullyKnownProvider(): iterable {
		yield [
			new Qualification(
				Qualification::UNCERTAIN,
				Qualification::KNOWN,
				Qualification::KNOWN
			)
		];

		yield [
			new Qualification(
				Qualification::KNOWN,
				Qualification::APPROXIMATE,
				Qualification::KNOWN
			)
		];

		yield [
			new Qualification(
				Qualification::KNOWN,
				Qualification::KNOWN,
				Qualification::UNCERTAIN_AND_APPROXIMATE
			)
		];
	}

}
