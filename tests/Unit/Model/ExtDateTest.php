<?php

namespace EDTF\Tests\Unit\Model;

use Carbon\Carbon;
use EDTF\Model\ExtDate;
use EDTF\Model\Qualification;
use EDTF\Model\UnspecifiedDigit;
use EDTF\PackagePrivate\Carbon\CarbonFactory;
use EDTF\PackagePrivate\Carbon\DatetimeFactoryException;
use EDTF\Tests\Unit\FactoryTrait;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers \EDTF\Model\ExtDate
 * @covers \EDTF\PackagePrivate\Carbon\CarbonFactory
 * @package EDTF\Tests\Unit
 */
class ExtDateTest extends TestCase {

	use FactoryTrait;

	public function testCreate(): void {
		$q = $this->createMock( Qualification::class );
		$u = $this->createMock( UnspecifiedDigit::class );

		$d = new ExtDate( 2010, 10, 1, $q, $u );

		$this->assertSame( 2010, $d->getYear() );
		$this->assertSame( 10, $d->getMonth() );
		$this->assertSame( 1, $d->getDay() );
		$this->assertSame( $q, $d->getQualification() );
		$this->assertSame( $u, $d->getUnspecifiedDigit() );
	}

	/**
	 * @param ExtDate $extDate
	 * @param int $expectedMin
	 *
	 * @throws RuntimeException
	 * @dataProvider minDataProvider
	 */
	public function testGetMin( ExtDate $extDate, int $expectedMin ) {
		$this->assertSame( $expectedMin, $extDate->getMin() );
	}

	/**
	 * @param ExtDate $extDate
	 * @param int $expectedMax
	 *
	 * @dataProvider maxDataProvider
	 */
	public function testGetMax( ExtDate $extDate, int $expectedMax ) {
		$this->assertSame( $expectedMax, $extDate->getMax() );
	}

	public function testGetMinThrowsException(): void {
		$year = 1987;
		$month = 12;
		$day = 100;

		$date = new ExtDate( $year, $month, $day );

		$dateTimeFactoryMock = $this->createMock( CarbonFactory::class );
		$dateTimeFactoryMock->method( 'create' )
			->with( $year, $month, $day )
			->willThrowException( new DatetimeFactoryException() );

		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage( "Can't generate minimum date." );

		$date->setDatetimeFactory( $dateTimeFactoryMock );
		$date->getMin();
	}

	public function testGetMaxThrowsException(): void {
		$year = 1987;
		$month = 100;

		$date = new ExtDate( $year, $month );

		$dateTimeFactoryMock = $this->createMock( CarbonFactory::class );
		$dateTimeFactoryMock->expects( $this->once() )
			->method( 'create' )
			->with( $year, $month )
			->willThrowException( new DatetimeFactoryException() );

		$date->setDatetimeFactory( $dateTimeFactoryMock );

		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage( "Can't generate max value" );
		$date->getMax();
	}

	public function testShouldProvideUncertainInfo(): void {
		$q = new Qualification( Qualification::UNCERTAIN, Qualification::UNDEFINED, Qualification::UNDEFINED );
		$d = new ExtDate( null, null, null, $q );

		$this->assertTrue( $d->isUncertain() );
		$this->assertTrue( $d->uncertain() );

		$this->assertTrue( $d->getQualification()->yearIsUncertain() );
		$this->assertFalse( $d->getQualification()->monthIsUncertain() );
		$this->assertFalse( $d->getQualification()->dayIsUncertain() );
	}

	public function testShouldProvideApproximateInfo(): void {
		$q = new Qualification( Qualification::UNDEFINED, Qualification::APPROXIMATE, Qualification::UNDEFINED );
		$d = new ExtDate( null, null, null, $q );

		$this->assertTrue( $d->isApproximate() );
		$this->assertTrue( $d->approximate() );

		$this->assertFalse( $d->getQualification()->yearIsApproximate() );
		$this->assertTrue( $d->getQualification()->monthIsApproximate() );
		$this->assertFalse( $d->getQualification()->dayIsApproximate() );
	}

	public function testShouldProvideUncertainAndApproximateInfo(): void {
		$q = new Qualification(
			Qualification::UNDEFINED,
			Qualification::UNDEFINED,
			Qualification::UNCERTAIN_AND_APPROXIMATE
		);
		$d = new ExtDate( null, null, null, $q );

		$this->assertTrue( $d->isUncertain() );
		$this->assertTrue( $d->isApproximate() );
		$this->assertFalse( $d->getQualification()->yearIsUncertain() );
		$this->assertFalse( $d->getQualification()->monthIsUncertain() );
		$this->assertTrue( $d->getQualification()->dayIsUncertain() );
		$this->assertTrue( $d->getQualification()->dayIsApproximate() );
	}

	public function testNegativeYear(): void {
		$date = $this->createExtDate( '-0999' );

		$this->assertInstanceOf( ExtDate::class, $date );
		$this->assertSame( -999, $date->getYear() );
		$this->assertNull( $date->getMonth() );
		$this->assertNull( $date->getDay() );
	}

	public function testWithZeroYear(): void {
		$date = $this->createExtDate( '0000' );

		$this->assertInstanceOf( ExtDate::class, $date );
		$this->assertSame( 0, $date->getYear() );
		$this->assertNull( $date->getMonth() );
		$this->assertNull( $date->getDay() );
	}

	/**
	 * @dataProvider datePrecisionProvider
	 */
	public function testPrecision( ExtDate $edtf, ?int $expectedPrecisionInt, string $expectedPrecisionString ): void {
		$this->assertEquals( $expectedPrecisionInt, $edtf->precision() );
		$this->assertEquals( $expectedPrecisionString, $edtf->precisionAsString() );
	}

	/**
	 * @dataProvider isoDateProvider
	 */
	public function testIsoDate( ExtDate $edtf, string $expectedIsoString ): void {
		$this->assertEquals( $expectedIsoString, $edtf->iso8601() );
	}

	public function datePrecisionProvider(): array {
		return [
			[ new ExtDate( 2000 ), ExtDate::PRECISION_YEAR, 'year' ],
			[ new ExtDate( 2001, 02 ), ExtDate::PRECISION_MONTH, 'month' ],
			[ new ExtDate( 2001, 2, 24 ), ExtDate::PRECISION_DAY, 'day' ],
			[ new ExtDate(), ExtDate::PRECISION_YEAR, 'year' ],
		];
	}

	public function minDataProvider(): array {
		return [
			[
				new ExtDate( 1987, 10, 1 ),
				Carbon::create( 1987, 10 )->timestamp
			],
			[
				new ExtDate( 1987 ),
				Carbon::create( 1987 )->timestamp
			],
			[
				new ExtDate( 1987, 12 ),
				Carbon::create( 1987, 12 )->timestamp
			]
		];
	}

	public function maxDataProvider(): array {
		return [
			[
				new ExtDate( 1987, 10, 1 ),
				Carbon::create( 1987, 10, 1, 23, 59, 59 )->timestamp
			],
			[
				new ExtDate( 1988 ),
				Carbon::create( 1988, 12, 31, 23, 59, 59 )->timestamp
			],
			[
				new ExtDate( 1987, 2 ),
				$daysInMonth = Carbon::create( 1987, 2 )->lastOfMonth()->endOfDay()->timestamp
			]
		];
	}

	public function isoDateProvider(): array {
		return [
			[
				new ExtDate( 1987 ),
				'1987'
			],
			[
				new ExtDate( 1987, 10 ),
				'1987-10'
			],
			[
				new ExtDate( 1987, 10, 1 ),
				'1987-10-01'
			],
			[
				new ExtDate(
					1987, 10, 1,
					new Qualification(
						Qualification::UNDEFINED,
						Qualification::APPROXIMATE,
						Qualification::UNDEFINED
					)
				),
				'1987-10-01'
			],
		];
	}

	/**
	 * @dataProvider uniformQualificationProvider
	 */
	public function testIsUniformlyQualified( ExtDate $date ): void {
		$this->assertTrue( $date->isUniformlyQualified() );
	}

	public function uniformQualificationProvider(): iterable {
		yield [
			new ExtDate( 1987, 10, 1 )
		];

		yield [
			new ExtDate(
				1987, 10, 1,
				new Qualification( Qualification::UNCERTAIN, Qualification::UNCERTAIN, Qualification::UNCERTAIN )
			)
		];

		yield [
			new ExtDate(
				1987, 10, null,
				new Qualification( Qualification::UNCERTAIN, Qualification::UNCERTAIN, Qualification::UNDEFINED )
			)
		];

		yield [
			new ExtDate(
				1987, null, null,
				new Qualification( Qualification::UNCERTAIN, Qualification::UNDEFINED, Qualification::UNDEFINED )
			)
		];

		yield [
			new ExtDate(
				1987, null, null,
				new Qualification( Qualification::UNDEFINED, Qualification::UNDEFINED, Qualification::UNDEFINED )
			)
		];
	}

	/**
	 * @dataProvider mixedQualificationProvider
	 */
	public function testIsNotUniformlyQualified( ExtDate $date ): void {
		$this->assertFalse( $date->isUniformlyQualified() );
	}

	public function mixedQualificationProvider(): iterable {
		yield [
			new ExtDate(
				1987, 10, 1,
				new Qualification( Qualification::APPROXIMATE, Qualification::UNCERTAIN, Qualification::UNCERTAIN )
			)
		];

		yield [
			new ExtDate(
				1987, 10, 1,
				new Qualification( Qualification::UNCERTAIN, Qualification::APPROXIMATE, Qualification::UNCERTAIN )
			)
		];

		yield [
			new ExtDate(
				1987, 10, 1,
				new Qualification( Qualification::UNCERTAIN, Qualification::UNCERTAIN, Qualification::APPROXIMATE )
			)
		];

		yield [
			new ExtDate(
				1987, 10, 1,
				new Qualification( Qualification::UNCERTAIN_AND_APPROXIMATE, Qualification::UNCERTAIN, Qualification::UNCERTAIN )
			)
		];

		yield [
			new ExtDate(
				1987, 10, 1,
				new Qualification( Qualification::UNDEFINED, Qualification::UNCERTAIN, Qualification::UNCERTAIN )
			)
		];

		yield [
			new ExtDate(
				1987, 10, 1,
				new Qualification( Qualification::UNCERTAIN, Qualification::UNCERTAIN, Qualification::UNDEFINED )
			)
		];

		yield [
			new ExtDate(
				1987, 10, null,
				new Qualification( Qualification::UNCERTAIN, Qualification::UNDEFINED, Qualification::UNDEFINED )
			)
		];

		yield [
			new ExtDate(
				1987, 10, null,
				new Qualification( Qualification::UNCERTAIN, Qualification::UNCERTAIN_AND_APPROXIMATE, Qualification::UNDEFINED )
			)
		];
	}

}
