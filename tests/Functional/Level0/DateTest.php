<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Functional\Level0;

use EDTF\Model\ExtDate;
use EDTF\Tests\Unit\FactoryTrait;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\PackagePrivate\Parser\Parser
 * @covers \EDTF\Model\ExtDate
 * @package EDTF\Tests\Unit
 */
class DateTest extends TestCase {

	use FactoryTrait;

	public function testCompleteRepresentation() {
		$date = $this->createExtDate( '1985-04-12' );

		$this->assertInstanceOf( ExtDate::class, $date );
		$this->assertSame( 1985, $date->getYear() );
		$this->assertSame( 4, $date->getMonth() );
		$this->assertSame( 12, $date->getDay() );
	}

	public function testReducedPrecisionForYearAndMonth() {
		$date = $this->createExtDate( '1985-04' );

		$this->assertInstanceOf( ExtDate::class, $date );
		$this->assertSame( 1985, $date->getYear() );
		$this->assertSame( 4, $date->getMonth() );
		$this->assertNull( $date->getDay() );
	}

	public function testReducedPrecisionForYear() {
		$date = $this->createExtDate( '1985' );

		$this->assertInstanceOf( ExtDate::class, $date );
		$this->assertSame( 1985, $date->getYear() );
		$this->assertNull( $date->getMonth() );
		$this->assertNull( $date->getDay() );
	}

	public function testErrorForNonLeapYear() {
		$this->expectException( InvalidArgumentException::class );
		$this->createExtDate( '1900-02-29' );
	}

	public function testCorrectDateForLeapYear() {
		$date = $this->createExtDate( '1904-02-29' );

		$this->assertInstanceOf( ExtDate::class, $date );
		$this->assertSame( 1904, $date->getYear() );
		$this->assertSame( 2, $date->getMonth() );
		$this->assertSame( 29, $date->getDay() );
	}
}