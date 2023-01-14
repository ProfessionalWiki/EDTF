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
		$q = new Qualification( Qualification::UNCERTAIN );
		$this->assertTrue( $q->yearIsUncertain() );
	}

	public function testUncertainWithNullPart(): void {
		$q = new Qualification();
		$this->assertFalse( $q->isUncertain() );

		$q = new Qualification( Qualification::UNCERTAIN );
		$this->assertTrue( $q->isUncertain() );
	}

	public function testApproximateWithNullPart(): void {
		$q = new Qualification( Qualification::KNOWN, Qualification::APPROXIMATE );
		$this->assertTrue( $q->isApproximate() );
		$this->assertFalse( $q->yearIsApproximate() );
		$this->assertTrue( $q->monthIsApproximate() );
		$this->assertFalse( $q->dayIsApproximate() );
	}

	public function testUncertainAndApproximate(): void {
		$q = new Qualification( Qualification::UNCERTAIN_AND_APPROXIMATE );
		$this->assertTrue( $q->yearIsUncertain() );
		$this->assertTrue( $q->yearIsApproximate() );
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
