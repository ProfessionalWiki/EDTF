<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Functional;

use EDTF\EdtfValue;
use EDTF\PackagePrivate\Parser\Parser;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\PackagePrivate\Parser\Parser
 * @package EDTF\Tests\Functional
 */
class EdtfTest extends TestCase {

	private function edtf( string $input ): EdtfValue {
		return ( new Parser() )->createEdtf( $input );
	}

	public function testReview(): void {
		// Date type
		$edtf = $this->edtf( '2016-02' );
		$this->assertSame( 1454284800, $edtf->getMin() );// 2016-02-01T00:00:00Z
		$this->assertSame( 1456790399, $edtf->getMax() );// 2016-02-29T23:59:59Z
		$this->assertTrue( $edtf->covers( $this->edtf( '2016-02-01' ) ) );
		$this->assertTrue( $edtf->covers( $this->edtf( '2016-02-29' ) ) );
		$this->assertFalse( $edtf->covers( $this->edtf( '2016-03-29' ) ) );

		// Interval type
		$edtf = $this->edtf( '2016-02-2X' );
		$this->assertSame( 1455926400, $edtf->getMin() );// 2016-02-20T00:00:00Z
		$this->assertSame( 1456790399, $edtf->getMax() );// 2016-02-29T23:59:59Z

		// Set type
		$edtf = $this->edtf( '[..2016,2017]' );
		$this->assertSame( 0, $edtf->getMin() );// Infinity
		$this->assertSame( 1514764799, $edtf->getMax() );// 2017-12-31T23:59:59Z
		$this->assertTrue( $edtf->covers( $this->edtf( '2016-01-01' ) ) );
		$this->assertTrue( $edtf->covers( $this->edtf( '2016-12-31' ) ) );
		$this->assertTrue( $edtf->covers( $this->edtf( '2017-01-01' ) ) );
		$this->assertTrue( $edtf->covers( $this->edtf( '2017-12-31' ) ) );
		$this->assertFalse( $edtf->covers( $this->edtf( '2018-01-01' ) ) );

		// Season type
		$edtf = $this->edtf( '2016-34' );
		$this->assertSame( 1459468800, $edtf->getMin() );// 2016-04-01T00:00:00Z
		$this->assertSame( 1467331199, $edtf->getMax() );// 2016-06-30T23:59:59Z
	}
}