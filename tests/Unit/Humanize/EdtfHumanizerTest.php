<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Unit\Humanize;

use EDTF\Humanize\EdtfHumanizer;
use EDTF\Season;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\Humanize\EdtfHumanizer
 */
class EdtfHumanizerTest extends TestCase {

	public function testFoo(): void {
		$this->assertSame(
			'Summer 2001',
			( new EdtfHumanizer() )->humanize( new Season( 2001, 21 ) )
		);
	}

}
