<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Unit\PackagePrivate\Humanizer;

use EDTF\EdtfFactory;
use EDTF\EdtfValue;
use EDTF\HumanizationResult;
use EDTF\Model\ExtDate;
use EDTF\Model\Season;
use EDTF\Model\Set;
use EDTF\Tests\TestDoubles\NullEdtfValue;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\PackagePrivate\Humanizer\PrivateStructuredHumanizer
 * @covers \EDTF\HumanizationResult
 */
class PrivateStructuredHumanizerTest extends TestCase {

	public function testEmptySet(): void {
		$this->assertHumanizedToSingleMessage(
			'Empty set',
			$this->humanize( new Set( [], false ) )
		);
	}

	private function humanize( EdtfValue $edtf ): HumanizationResult {
		return EdtfFactory::newStructuredHumanizerForLanguage( 'en' )->humanize( $edtf );
	}

	private function assertHumanizedToSingleMessage( string $expected, HumanizationResult $actual ): void {
		$this->assertSame(
			$expected,
			$actual->getSimpleHumanization()
		);

		$this->assertSame(
			[],
			$actual->getStructuredHumanization()
		);

		$this->assertSame(
			'',
			$actual->getContextMessage()
		);

		$this->assertTrue(
			$actual->wasHumanized()
		);

		$this->assertTrue(
			$actual->isOneMessage()
		);
	}

	public function testNotHumanized(): void {
		$result = $this->humanize( new NullEdtfValue() );

		$this->assertFalse( $result->wasHumanized() );
		$this->assertSame( '', $result->getSimpleHumanization() );
	}

	public function testNonSet(): void {
		$this->assertHumanizedToSingleMessage(
			'Spring 2021',
			$this->humanize( new Season( 2021, 21 ) )
		);
	}

	public function testAllValuesSetWithSingleMessage(): void {
		$set = new Set(
			[
				new Season( 2021, 21 ),
				new Season( 2021, 23 ),
				new Season( 2022, 21 )
			],
			true
		);

		$this->assertHumanizedToSingleMessage(
			'All of these: Spring 2021, Autumn 2021, Spring 2022',
			$this->humanize( $set )
		);
	}

	public function testOneValueSetWithSingleMessage(): void {
		$set = new Set(
			[
				new Season( 2021, 21 ),
				new Season( 2021, 23 ),
				new Season( 2022, 21 )
			],
			false
		);

		$this->assertHumanizedToSingleMessage(
			'One of these: Spring 2021, Autumn 2021, Spring 2022',
			$this->humanize( $set )
		);
	}

	public function testAllValuesSetWithTwoEdtf(): void {
		$set = new Set(
			[
				new Season( 2021, 21 ),
				new Season( 2021, 23 ),
			],
			true
		);

		$this->assertHumanizedToSingleMessage(
			'Spring 2021 and Autumn 2021',
			$this->humanize( $set )
		);
	}

	public function testOneValueSetWithTwoEdtf(): void {
		$set = new Set(
			[
				new Season( 2021, 21 ),
				new Season( 2021, 23 ),
			],
			false
		);

		$this->assertHumanizedToSingleMessage(
			'Spring 2021 or Autumn 2021',
			$this->humanize( $set )
		);
	}

	public function testAllValueSetsWithStructuredResult(): void {
		$set = new Set(
			[
				new ExtDate( 2021, 2, 14 ),
				new ExtDate( 2021, 2, 15 ),
			],
			true
		);

		$result = $this->humanize( $set );

		$this->assertTrue( $result->wasHumanized() );
		$this->assertFalse( $result->isOneMessage() );
		$this->assertSame( 'All of these:', $result->getContextMessage() );

		$this->assertSame(
			[
				'February 14th, 2021',
				'February 15th, 2021',
			],
			$result->getStructuredHumanization()
		);
	}

	public function testOneValueSetWithStructuredResult(): void {
		$set = new Set(
			[
				new ExtDate( 2021, 2, 14 ),
				new ExtDate( 2021, 2, 15 ),
			],
			false
		);

		$result = $this->humanize( $set );

		$this->assertTrue( $result->wasHumanized() );
		$this->assertFalse( $result->isOneMessage() );
		$this->assertSame( 'One of these:', $result->getContextMessage() );

		$this->assertSame(
			[
				'February 14th, 2021',
				'February 15th, 2021',
			],
			$result->getStructuredHumanization()
		);
	}
}
