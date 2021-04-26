<?php

namespace EDTF\Tests\Unit\PackagePrivate;

use EDTF\Model\ExtDate;
use PHPUnit\Framework\TestCase;

/**
 * @package EDTF\Tests\Unit\Contracts
 * @covers \EDTF\PackagePrivate\CoversTrait
 */
class CoversTraitTest extends TestCase {

	public function testCoversYearAgainstYear(): void {
		$edtf = new ExtDate( 1987 );
		$this->assertTrue( $edtf->covers( new ExtDate( 1987 ) ) );
		$this->assertFalse( $edtf->covers( new ExtDate( 1986 ) ) );
	}

	public function testCoversYearAgainstYearAndMonth(): void {
		$edtf = new ExtDate( 1987 );
		$this->assertTrue( $edtf->covers( new ExtDate( 1987, 2 ) ) );
		$this->assertFalse( $edtf->covers( new ExtDate( 1986, 4 ) ) );
	}

	public function testCoversYearAgainstFullDate(): void {
		$edtf = new ExtDate( 1987 );
		$this->assertTrue( $edtf->covers( new ExtDate( 1987, 2, 1 ) ) );
		$this->assertTrue( $edtf->covers( new ExtDate( 1987, 1, 1 ) ) );
		$this->assertTrue( $edtf->covers( new ExtDate( 1987, 11, 30 ) ) );
		$this->assertTrue( $edtf->covers( new ExtDate( 1987, 12, 31 ) ) );
		$this->assertFalse( $edtf->covers( new ExtDate( 1986, 2, 1 ) ) );
	}

	public function testCoversYearAndMonthAgainstYearAndMonth(): void {
		$edtf = new ExtDate( 1987, 1 );
		$this->assertTrue( $edtf->covers( new ExtDate( 1987, 1 ) ) );
		$this->assertFalse( $edtf->covers( new ExtDate( 1987, 2 ) ) );
	}

	public function testCoversYearAndMonthAgainstFullDate(): void {
		$edtf = new ExtDate( 1987, 1 );
		$this->assertTrue( $edtf->covers( new ExtDate( 1987, 1, 25 ) ) );
		$this->assertTrue( $edtf->covers( new ExtDate( 1987, 1, 1 ) ) );
		$this->assertTrue( $edtf->covers( new ExtDate( 1987, 1, 31 ) ) );
		$this->assertFalse( $edtf->covers( new ExtDate( 1987, 2, 1 ) ) );
	}

	public function testCoversFullDateAgainstFullDate(): void {
		$edtf = new ExtDate( 1987, 10, 12 );
		$this->assertTrue( $edtf->covers( new ExtDate( 1987, 10, 12 ) ) );
		$this->assertFalse( $edtf->covers( new ExtDate( 1987, 10, 13 ) ) );
	}
}