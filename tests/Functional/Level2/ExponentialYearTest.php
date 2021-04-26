<?php

namespace EDTF\Tests\Functional\Level2;

use EDTF\Tests\Unit\FactoryTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\PackagePrivate\Parser\Parser
 * @package EDTF\Tests\Functional
 */
class ExponentialYearTest extends TestCase {

	use FactoryTrait;

	public function testWithNegativeExponential() {
		$d = $this->createExtDate( 'Y-17E7' );
		$this->assertSame( -170000000, $d->getYear() );
	}

	public function testWithPositiveExponential() {
		$d = $this->createExtDate( 'Y17E4' );
		$this->assertSame( 170000, $d->getYear() );
	}
}