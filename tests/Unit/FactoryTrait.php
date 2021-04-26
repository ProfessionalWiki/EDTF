<?php

/** @noinspection PhpIncompatibleReturnTypeInspection */

/** @psalm-suppress all */

declare( strict_types = 1 );

namespace EDTF\Tests\Unit;

use EDTF\Model\ExtDate;
use EDTF\Model\ExtDateTime;
use EDTF\Model\Interval;
use EDTF\Model\Season;
use EDTF\Model\Set;
use EDTF\PackagePrivate\Parser\Parser;

/**
 * @todo Remove this class after library become stable
 */
trait FactoryTrait {

	public function createExtDate( string $data ): ExtDate {
		return $this->parse( $data );
	}

	public function createExtDateTime( string $data ): ExtDateTime {
		return $this->parse( $data );
	}

	public function createInterval( string $data ): Interval {
		return $this->parse( $data );
	}

	public function createSeason( string $data ): Season {
		return $this->parse( $data );
	}

	public function createSet( string $data ): Set {
		return $this->parse( $data );
	}

	private function parse( $data ): object {
		$parser = new Parser();
		return $parser->createEdtf( $data );
	}
}