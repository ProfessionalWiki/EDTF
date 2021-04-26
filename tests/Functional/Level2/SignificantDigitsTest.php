<?php

namespace EDTF\Tests\Functional\Level2;

use EDTF\Tests\Unit\FactoryTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\PackagePrivate\Parser\Parser
 * @covers \EDTF\Model\Interval
 * @package EDTF\Tests\Functional
 */
class SignificantDigitsTest extends TestCase {

	use FactoryTrait;

	public function testWithEstimatedYear() {
		$i = $this->createInterval( '1950S2' );

		$this->assertSame( 1900, $i->getStartDate()->getYear() );
		$this->assertSame( 1999, $i->getEndDate()->getYear() );
		$this->assertSame( 2, $i->getSignificantDigit() );
		$this->assertSame( 1950, $i->getEstimated() );
	}

	public function testWithPrefixedEstimatedYear() {
		$i = $this->createInterval( 'Y171010000S3' );

		$this->assertSame( 3, $i->getSignificantDigit() );
		$this->assertSame( 171010000, $i->getEstimated() );
		$this->assertSame( 171010000, $i->getStartDate()->getYear() );
		$this->assertSame( 171010999, $i->getEndDate()->getYear() );
	}

	public function testWithExponentialYear() {
		$i = $this->createInterval( 'Y3388E2S3' );

		$this->assertSame( 3, $i->getSignificantDigit() );
		$this->assertSame( 338800, $i->getEstimated() );
		$this->assertSame( 338000, $i->getStartDate()->getYear() );
		$this->assertSame( 338999, $i->getEndDate()->getYear() );
	}
}