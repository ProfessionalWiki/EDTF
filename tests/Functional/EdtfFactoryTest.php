<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Functional;

use EDTF\EdtfFactory;
use EDTF\Season;
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
			'Printemps 2021',
			EdtfFactory::newHumanizerForLanguage( 'fr' )->humanize( new Season( 2021, 21 ) )
		);
	}

	public function testHumanizationFallback(): void {
		$this->assertSame(
			'Spring 2021',
			EdtfFactory::newHumanizerForLanguage( '404' )->humanize( new Season( 2021, 21 ) )
		);
	}

}
