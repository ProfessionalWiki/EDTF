<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Functional;

use EDTF\EdtfFactory;
use EDTF\Model\ExtDate;
use EDTF\Model\Season;
use EDTF\Model\Set;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\EdtfFactory
 */
class EdtfFactoryTest extends TestCase {

	public function testHumanizationEnglish(): void {
		$this->assertSame(
			'Spring 2021',
			EdtfFactory::newHumanizerForLanguage( 'en' )->humanize( new Season( 2021, 21 ) )
		);
	}

	public function testHumanizationFrench(): void {
		$this->assertSame(
			'printemps 2021',
			EdtfFactory::newHumanizerForLanguage( 'fr' )->humanize( new Season( 2021, 21 ) )
		);
	}

	public function testHumanizationFallback(): void {
		$this->assertSame(
			'Spring 2021',
			EdtfFactory::newHumanizerForLanguage( '404' )->humanize( new Season( 2021, 21 ) )
		);
	}

	public function testNewValidator(): void {
		$this->assertTrue( EdtfFactory::newValidator()->isValidEdtf( '2021-02-15' ) );
		$this->assertFalse( EdtfFactory::newValidator()->isValidEdtf( '~[,,_,,]:3' ) );
	}

	public function testNewParser(): void {
		$this->assertTrue( EdtfFactory::newParser()->parse( '2021-02-15' )->isValid() );
		$this->assertFalse( EdtfFactory::newParser()->parse( '~[,,_,,]:3' )->isValid() );
	}

	public function testNewStructuredHumanizer(): void {
		$humanizer = EdtfFactory::newStructuredHumanizerForLanguage( 'en' );

		$this->assertSame(
			'Spring 2021',
			$humanizer->humanize( new Season( 2021, 21 ) )->getSimpleHumanization()
		);

		$this->assertSame(
			[
				'February 14th, 2021',
				'February 15th, 2021',
			],
			$humanizer->humanize(
				new Set(
					[
						new ExtDate( 2021, 2, 14 ),
						new ExtDate( 2021, 2, 15 ),
					],
					true
				)
			)->getStructuredHumanization()
		);
	}

}
