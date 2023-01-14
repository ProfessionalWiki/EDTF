<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Functional\Level1;

use EDTF\Tests\Unit\FactoryTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\PackagePrivate\Parser\Parser
 * @covers \EDTF\Model\Interval
 * @covers \EDTF\Model\ExtDateTime
 * @covers \EDTF\Model\Qualification
 */
class QualificationTest extends TestCase {

	use FactoryTrait;

	public function testUncertainYear() {
		$date = $this->createExtDate( '1984?' );
		$this->assertTrue( $date->getQualification()->yearIsUncertain() );
	}

	public function testApproximateYearAndMonth() {
		$date = $this->createExtDate( "2004-06~" );

		$this->assertTrue( $date->getQualification()->yearIsApproximate() );
		$this->assertTrue( $date->getQualification()->monthIsApproximate() );
		$this->assertFalse( $date->getQualification()->dayIsApproximate() );
		$this->assertSame( 2004, $date->getYear() );
		$this->assertSame( 6, $date->getMonth() );
	}

	public function testApproximateAndUncertainYearMonthDay() {
		$date = $this->createExtDate( "2004-06-11%" );

		$this->assertTrue( $date->isUncertain() );
		$this->assertTrue( $date->isApproximate() );
		$this->assertTrue( $date->getQualification()->dayIsUncertain() );
		$this->assertTrue( $date->getQualification()->monthIsUncertain() );
		$this->assertTrue( $date->getQualification()->yearIsUncertain() );
		$this->assertTrue( $date->getQualification()->dayIsApproximate() );
		$this->assertTrue( $date->getQualification()->monthIsApproximate() );
		$this->assertTrue( $date->getQualification()->yearIsApproximate() );

		$this->assertSame( 2004, $date->getYear() );
		$this->assertSame( 6, $date->getMonth() );
		$this->assertSame( 11, $date->getDay() );
	}
}