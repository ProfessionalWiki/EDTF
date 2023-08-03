<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Unit\Model;

use EDTF\Model\ExtDate;
use EDTF\Model\ExtDateTime;
use EDTF\Tests\Unit\FactoryTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\Model\ExtDateTime
 * @package EDTF\Tests\Unit
 */
class ExtDateTimeTest extends TestCase {

	use FactoryTrait;

	public function testCreate(): void {
		$dt = new ExtDateTime( new ExtDate( 2010, 10, 1 ), 1, 1, 1 );

		$this->assertSame( 2010, $dt->getYear() );
		$this->assertSame( 10, $dt->getMonth() );
		$this->assertSame( 1, $dt->getDay() );
		$this->assertSame( 1, $dt->getHour() );
		$this->assertSame( 1, $dt->getMinute() );
		$this->assertSame( 1, $dt->getSecond() );
	}

	public function testNoTimeZoneResultsInNullOffset(): void {
		$date = $this->createExtDateTime( "2001-02-03T09:30:01" );

		$this->assertSame( 2001, $date->getYear() );
		$this->assertSame( 2, $date->getMonth() );
		$this->assertSame( 3, $date->getDay() );
		$this->assertSame( 9, $date->getHour() );
		$this->assertSame( 30, $date->getMinute() );
		$this->assertSame( 1, $date->getSecond() );
		$this->assertNull( $date->getTimezoneOffset() );
	}

	public function testWithZSuffixResultsInUTC(): void {
		$date = $this->createExtDateTime( "2004-01-01T10:10:10Z" );

		$this->assertSame( 2004, $date->getYear() );
		$this->assertSame( 1, $date->getMonth() );
		$this->assertSame( 1, $date->getDay() );
		$this->assertSame( 10, $date->getHour() );
		$this->assertSame( 10, $date->getMinute() );
		$this->assertSame( 10, $date->getSecond() );
		$this->assertSame( 0, $date->getTimezoneOffset() );
		$this->assertSame( '2004-01-01T10:10:10+00:00', $date->iso8601() );
	}

	public function testWithSpesificTimezone(): void {
		$date = $this->createExtDateTime( '2004-01-01T10:10:10+05:00' );

		$this->assertSame( 2004, $date->getYear() );
		$this->assertSame( 1, $date->getMonth() );
		$this->assertSame( 1, $date->getDay() );
		$this->assertSame( 10, $date->getHour() );
		$this->assertSame( 10, $date->getMinute() );
		$this->assertSame( 10, $date->getSecond() );
		$this->assertSame( 300, $date->getTimezoneOffset() );
	}

	public function testIsoDate(): void {
		$date = $this->createExtDateTime( '2004-01-01T10:10:10' );
		$this->assertSame( '2004-01-01T10:10:10', $date->iso8601() );

		$date = $this->createExtDateTime( '2004-01-01T10:10:10+00:00' );
		$this->assertSame( '2004-01-01T10:10:10+00:00', $date->iso8601() );

		$date = $this->createExtDateTime( '2004-01-01T10:10:10+05:00' );
		$this->assertSame( '2004-01-01T10:10:10+05:00', $date->iso8601() );

		$date = $this->createExtDateTime( '2004-01-01T10:10:10-05:00' );
		$this->assertSame( '2004-01-01T10:10:10-05:00', $date->iso8601() );

	}
}
