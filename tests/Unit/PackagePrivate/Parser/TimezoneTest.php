<?php

namespace EDTF\Tests\Unit\PackagePrivate\Parser;

use EDTF\PackagePrivate\Parser\Timezone;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\PackagePrivate\Parser\Time
 */
class TimezoneTest extends TestCase {

	public function testCreateTimezoneWithTime() {
		$timezone = new Timezone( 12, 0, '+', null );

		$this->assertNull( $timezone->getTzUtc() );
		$this->assertSame( 12, $timezone->getTzHour() );
		$this->assertSame( 0, $timezone->getTzMinute() );
		$this->assertSame( '+', $timezone->getTzSign() );
	}

	public function testCreateTimezoneWithUtc() {
		$timezone = new Timezone( null, null, null, 'Z' );

		$this->assertNull( $timezone->getTzHour() );
		$this->assertNull( $timezone->getTzMinute() );
		$this->assertNull( $timezone->getTzSign() );
		$this->assertSame( 'Z', $timezone->getTzUtc() );
	}
}
