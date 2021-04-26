<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Functional\Level0;

use EDTF\Tests\Unit\FactoryTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\PackagePrivate\Parser\Parser
 * @covers \EDTF\Model\ExtDateTime
 * @package EDTF\Tests\Unit
 */
class DateTimeTest extends TestCase {

	use FactoryTrait;

	public function testCompleteRepresentationsForDateAndLocalTime() {
		$t = $this->createExtDateTime( '1985-04-12T23:20:30' );

		$this->assertSame( 1985, $t->getYear() );
		$this->assertSame( 4, $t->getMonth() );
		$this->assertSame( 12, $t->getDay() );
		$this->assertSame( 23, $t->getHour() );
		$this->assertSame( 20, $t->getMinute() );
		$this->assertSame( 30, $t->getSecond() );
		$this->assertNull( $t->getTzSign() );
	}

	public function testCompleteRepresentationsForDateAndUtcTime() {
		$t = $this->createExtDateTime( '1985-04-12T23:20:30Z' );

		$this->assertSame( 1985, $t->getYear() );
		$this->assertSame( 4, $t->getMonth() );
		$this->assertSame( 12, $t->getDay() );
		$this->assertSame( 23, $t->getHour() );
		$this->assertSame( 20, $t->getMinute() );
		$this->assertSame( 30, $t->getSecond() );
		$this->assertSame( "Z", $t->getTzSign() );
	}

	public function testWithTimeShiftInHoursOnly() {
		$t = $this->createExtDateTime( '1985-04-12T23:20:30-04' );

		$this->assertSame( 1985, $t->getYear() );
		$this->assertSame( 4, $t->getMonth() );
		$this->assertSame( 12, $t->getDay() );
		$this->assertSame( 23, $t->getHour() );
		$this->assertSame( 20, $t->getMinute() );
		$this->assertSame( 30, $t->getSecond() );
		$this->assertSame( "-", $t->getTzSign() );
		$this->assertSame( 4, $t->getTzHour() );
		$this->assertNull( $t->getTzMinute() );

		// timezone offset = 4 hour * 60 minutes
		$this->assertSame( -240, $t->getTimezoneOffset() );
	}

	public function testWithTimeShiftInHoursAndMinutes() {
		$t = $this->createExtDateTime( '1985-04-12T23:20:30+04:30' );

		$this->assertSame( 1985, $t->getYear() );
		$this->assertSame( 4, $t->getMonth() );
		$this->assertSame( 12, $t->getDay() );
		$this->assertSame( 23, $t->getHour() );
		$this->assertSame( 20, $t->getMinute() );
		$this->assertSame( 30, $t->getSecond() );
		$this->assertSame( "+", $t->getTzSign() );
		$this->assertSame( 4, $t->getTzHour() );
		$this->assertSame( 30, $t->getTzMinute() );

		// timezone offset = (4 hour * 60 minutes) + 30 minute
		$this->assertSame( 270, $t->getTimezoneOffset() );
	}
}