<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Unit\Model\SetElement;

use EDTF\Model\ExtDate;
use EDTF\Model\SetElement\RangeSetElement;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\Model\SetElement\RangeSetElement
 */
class RangeSetElementTest extends TestCase {

	public function testPrecisionMustMatch(): void {
		$this->expectException( \InvalidArgumentException::class );

		new RangeSetElement(
			new ExtDate( 2000 ),
			new ExtDate( 2020, 4 )
		);
	}

	public function testGetters(): void {
		$rangeElement = new RangeSetElement(
			new ExtDate( 2000, 1 ),
			new ExtDate( 2020, 4 )
		);

		$this->assertEquals(
			new ExtDate( 2000, 1 ),
			$rangeElement->getStart()
		);

		$this->assertEquals(
			new ExtDate( 2020, 4 ),
			$rangeElement->getEnd()
		);
	}

	public function testEndCannotBeBeforeStart(): void {
		$this->expectException( \InvalidArgumentException::class );

		new RangeSetElement(
			new ExtDate( 2001, 5 ),
			new ExtDate( 2000, 6 )
		);
	}

	public function testEndCannotEqualStart(): void {
		$this->expectException( \InvalidArgumentException::class );

		new RangeSetElement(
			new ExtDate( 2001, 5 ),
			new ExtDate( 2001, 5 )
		);
	}
}
