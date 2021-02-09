<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Unit\Humanize;

use EDTF\Humanize\HumanizerFactory;
use EDTF\Season;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\Humanize\HumanizerFactory
 */
class HumanizerFactoryTest extends TestCase {

	public function testEnglish(): void {
		$this->assertSame(
			'Spring 2021',
			HumanizerFactory::newForLanguage( 'en' )->humanize( new Season( 2021, 21 ) )
		);
	}

	public function testFrench(): void {
		$this->assertSame(
			'Printemps 2021',
			HumanizerFactory::newForLanguage( 'fr' )->humanize( new Season( 2021, 21 ) )
		);
	}

	public function testFallback(): void {
		$this->assertSame(
			'Spring 2021',
			HumanizerFactory::newForLanguage( '404' )->humanize( new Season( 2021, 21 ) )
		);
	}

}
