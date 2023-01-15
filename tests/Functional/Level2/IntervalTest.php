<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Functional\Level2;

use EDTF\Tests\Unit\FactoryTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\Model\Interval
 * @covers \EDTF\PackagePrivate\Parser\Parser
 * @package EDTF\Tests\Functional
 */
class IntervalTest extends TestCase {

	use FactoryTrait;

	public function testApproximatelyInStartAndEnd() {
		$i = $this->createInterval( '2004-06-~01/2004-06-~20' );
		$start = $i->getStartDate();
		$end = $i->getEndDate();

		$this->assertTrue( $start->isApproximate() );
		$this->assertTrue( $end->isApproximate() );
		$this->assertTrue( $start->getQualification()->dayIsApproximate() );
		$this->assertTrue( $end->getQualification()->dayIsApproximate() );
	}

	public function testUsingUnspecifiedPart() {
		$i = $this->createInterval( '2004-06-XX/2004-07-03' );
		$start = $i->getStartDate();
		$end = $i->getEndDate();

		$this->assertTrue( $start->unspecified() );
		$this->assertFalse( $end->unspecified() );
		$this->assertTrue( $start->unspecified( 'day' ) );
		$this->assertNull( $start->getDay() );
	}
}