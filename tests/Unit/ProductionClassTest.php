<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Unit;

use PHPUnit\Framework\TestCase;
use EDTF\ProductionClass;

/**
 * @covers \EDTF\ProductionClass
 */
class ProductionClassTest extends TestCase {

	public function testGetTrue() {
		$this->assertTrue( ProductionClass::getTrue() );
	}

}
