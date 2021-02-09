<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Unit\Humanize;

use EDTF\Humanize\HumanizerFactory;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\Humanize\StringHumanizer
 * @covers \EDTF\Humanize\HumanizerFactory
 */
class StringHumanizerTest extends TestCase {

	public function testFoo(): void {
		$this->assertSame(
			'43',
			$this->humanize( '0043' )
		);
	}

	private function humanize( string $edtf ): string {
		return HumanizerFactory::newStringHumanizerForLanguage( 'en' )->humanize( $edtf );
	}

}
