<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Functional\Level2;

use EDTF\Tests\Unit\FactoryTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\PackagePrivate\Parser\Parser
 * @covers \EDTF\Model\ExtDate
 * @covers \EDTF\Model\Qualification
 * @package EDTF\Tests\Functional
 */
class QualificationTest extends TestCase {

	use FactoryTrait;

	public function testYearMonthDayUncertainAndApproximate(): void {
		$d = $this->createExtDate( '2004-06-11%' );

		$this->assertTrue( $d->isUncertain() );
		$this->assertTrue( $d->isApproximate() );

		$this->assertTrue( $d->getQualification()->yearIsUncertain() );
		$this->assertTrue( $d->getQualification()->monthIsUncertain() );
		$this->assertTrue( $d->getQualification()->dayIsUncertain() );
	}

	public function testYearAndMonthApproximate(): void {
		$d = $this->createExtDate( '2004-06~-11' );

		$this->assertTrue( $d->isApproximate() );
		$this->assertTrue( $d->getQualification()->yearIsApproximate() );
		$this->assertTrue( $d->getQualification()->monthIsApproximate() );
		$this->assertFalse( $d->getQualification()->dayIsApproximate() );
	}

	public function testYearUncertain(): void {
		$d = $this->createExtDate( '2004?-06-11' );

		$this->assertTrue( $d->isUncertain() );
		$this->assertTrue( $d->getQualification()->yearIsUncertain() );
		$this->assertFalse( $d->getQualification()->monthIsUncertain() );
		$this->assertFalse( $d->getQualification()->dayIsUncertain() );
	}

	public function testIndividualComponentWithYearAndDay(): void {
		$d = $this->createExtDate( '?2004-06-~11' );

		$this->assertTrue( $d->isUncertain() );
		$this->assertTrue( $d->isApproximate() );

		$this->assertFalse( $d->getQualification()->yearIsApproximate() );
		$this->assertTrue( $d->getQualification()->yearIsUncertain() );
		$this->assertFalse( $d->getQualification()->monthIsApproximate() );
		$this->assertFalse( $d->getQualification()->monthIsUncertain() );
		$this->assertTrue( $d->getQualification()->dayIsApproximate() );
		$this->assertFalse( $d->getQualification()->dayIsUncertain() );
	}

	public function testIndividualComponentWithMonth(): void {
		$d = $this->createExtDate( '2004-%06-11' );

		$this->assertTrue( $d->isUncertain() );
		$this->assertTrue( $d->isApproximate() );

		$this->assertFalse( $d->getQualification()->yearIsApproximate() );
		$this->assertFalse( $d->getQualification()->yearIsUncertain() );
		$this->assertTrue( $d->getQualification()->monthIsApproximate() );
		$this->assertTrue( $d->getQualification()->monthIsUncertain() );
		$this->assertFalse( $d->getQualification()->dayIsApproximate() );
		$this->assertFalse( $d->getQualification()->dayIsUncertain() );
	}
}