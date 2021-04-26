<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Functional\Level1;

use EDTF\Tests\Unit\FactoryTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\Model\ExtDateTime
 * @covers \EDTF\PackagePrivate\Parser\Parser
 * @package EDTF\Tests\Unit
 */
class PrefixedYearTest extends TestCase {

	use FactoryTrait;

	public function testPositiveYear() {
		$dateTime = $this->createExtDate( 'Y170000002' );

		$this->assertSame( 170000002, $dateTime->getYear() );
	}

	public function testNegativeYear() {
		$dateTime = $this->createExtDate( 'Y-170000002' );

		$this->assertSame( -170000002, $dateTime->getYear() );
	}
}