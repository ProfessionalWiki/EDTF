<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Unit\Model;

use EDTF\Model\ExtDate;
use EDTF\Model\Set;
use EDTF\Model\SetElement\RangeSetElement;
use EDTF\Model\SetElement\SingleDateSetElement;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\Model\Set
 * @covers \EDTF\Model\SetElement\SingleDateSetElement
 */
class SetTest extends TestCase {

	public function testIsEmpty(): void {
		$this->assertTrue( Set::newAllMembersSet( [] )->isEmpty() );
		$this->assertFalse( Set::newAllMembersSet( [ new ExtDate( 2021 ) ] )->isEmpty() );
	}

	public function testIsAllMembers(): void {
		$this->assertTrue( Set::newAllMembersSet( [] )->isAllMembers() );
		$this->assertFalse( Set::newOneMemberSet( [] )->isAllMembers() );
	}

	public function testGetElements(): void {
		$this->assertEquals(
			[
				new SingleDateSetElement( new ExtDate( 2021 ) ),
				new SingleDateSetElement( new ExtDate( 2022 ) ),
				new RangeSetElement( new ExtDate( 1000 ), new ExtDate( 1010 ) )
			],
			Set::newAllMembersSet( [
				new ExtDate( 2021 ),
				new SingleDateSetElement( new ExtDate( 2022 ) ),
				new RangeSetElement( new ExtDate( 1000 ), new ExtDate( 1010 ) ),
			] )->getElements()
		);
	}

}