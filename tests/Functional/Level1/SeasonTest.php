<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Functional\Level1;

use EDTF\Tests\Unit\FactoryTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\Model\Season
 * @covers \EDTF\PackagePrivate\Parser\Parser
 */
class SeasonTest extends TestCase {

	use FactoryTrait;

	public function testCreatingSeason() {
		$season = $this->createSeason( '2001-33' );

		$this->assertSame( 2001, $season->getYear() );
		$this->assertSame( 33, $season->getSeason() );
	}
}