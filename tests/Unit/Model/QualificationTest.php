<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Unit\Model;

use EDTF\Model\Qualification;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\Model\Qualification
 * @package EDTF\Tests\Unit
 */
class QualificationTest extends TestCase {

	public function testUncertain(): void {
		$q = new Qualification( Qualification::UNCERTAIN, Qualification::UNDEFINED, Qualification::UNDEFINED );
		$this->assertTrue( $q->yearIsUncertain() );
	}

	public function testUncertainWithNullPart(): void {
		$q = Qualification::newFullyKnown();
		$this->assertFalse( $q->isUncertain() );

		$q = new Qualification( Qualification::UNCERTAIN, Qualification::UNDEFINED, Qualification::UNDEFINED );
		$this->assertTrue( $q->isUncertain() );
	}

	public function testApproximateWithNullPart(): void {
		$q = new Qualification( Qualification::UNDEFINED, Qualification::APPROXIMATE, Qualification::UNDEFINED );
		$this->assertTrue( $q->isApproximate() );
		$this->assertFalse( $q->yearIsApproximate() );
		$this->assertTrue( $q->monthIsApproximate() );
		$this->assertFalse( $q->dayIsApproximate() );
	}

	public function testUncertainAndApproximate(): void {
		$q = new Qualification( Qualification::UNCERTAIN_AND_APPROXIMATE, Qualification::UNDEFINED, Qualification::UNDEFINED );
		$this->assertTrue( $q->yearIsUncertain() );
		$this->assertTrue( $q->yearIsApproximate() );
	}

	public function testIsFullyKnown(): void {
		$qualification = new Qualification(
			Qualification::UNDEFINED,
			Qualification::UNDEFINED,
			Qualification::UNDEFINED
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
				Qualification::UNDEFINED,
				Qualification::UNDEFINED
			)
		];

		yield [
			new Qualification(
				Qualification::UNDEFINED,
				Qualification::APPROXIMATE,
				Qualification::UNDEFINED
			)
		];

		yield [
			new Qualification(
				Qualification::UNDEFINED,
				Qualification::UNDEFINED,
				Qualification::UNCERTAIN_AND_APPROXIMATE
			)
		];
	}

	public function testConstructorThrowsOnInvalidQualification(): void {
		$this->expectException( \InvalidArgumentException::class );

		new Qualification(
			Qualification::UNDEFINED,
			500,
			Qualification::UNDEFINED
		);
	}

}
