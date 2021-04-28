<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Unit\Model\SetElement;

use EDTF\Model\ExtDate;
use EDTF\Model\SetElement\SingleDateSetElement;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\Model\SingleDateSetElement
 */
class SingleDateSetElementTest extends TestCase {

	public function testGetDatesReturnsSingleDateForSingleDateElement(): void {
		$date = new ExtDate( 2021, 4, 27 );

		$this->assertEquals(
			$date,
			( new SingleDateSetElement( $date ) )->getDate()
		);
	}

}
