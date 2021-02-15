<?php

declare( strict_types = 1 );

namespace EDTF\PackagePrivate;

use EDTF\EdtfValue;
use EDTF\HumanizationResult;
use EDTF\Humanizer;
use EDTF\Set;
use EDTF\StructuredHumanizer;

class PrivateStructuredHumanizer implements StructuredHumanizer {

	private Humanizer $humanizer;

	public function __construct( Humanizer $humanizer ) {
		$this->humanizer = $humanizer;
	}

	public function humanize( EdtfValue $edtf ): HumanizationResult {
		if ( $edtf instanceof Set ) {
			return $this->humanizeSet( $edtf );
		}

		$humanized = $this->humanizer->humanize( $edtf );

		if ( $humanized === '' ) {
			return HumanizationResult::newNonHumanized();
		}

		return HumanizationResult::newSimpleHumanization( $humanized );
	}

	private function humanizeSet( Set $edtf ): HumanizationResult {
		if ( $edtf->getDates() === [] ) {
			return HumanizationResult::newSimpleHumanization( 'Empty set' ); // TODO
		}

		$humanizedDates = $this->getHumanizedDatesFromSet( $edtf );

		if ( $humanizedDates->shouldUseList() ) {
			// TODO
			return HumanizationResult::newStructuredHumanization( $humanizedDates->humanizedDates );
		}

		if ( $edtf->isAllMembers() ) {
			return $this->humanizeAllMemberSet( $humanizedDates );
		}

		return $this->humanizeOneMemberSet( $humanizedDates );
	}

	private function getHumanizedDatesFromSet( Set $edtf ): HumanizedSetDates {
		$humanizedDates = [];

		foreach ( $edtf->getDates() as $date ) {
			$humanizedDates[] = $this->humanizer->humanize( $date );
		}

		return new HumanizedSetDates( $humanizedDates );
	}

	private function humanizeAllMemberSet( HumanizedSetDates $humanizedDates ): HumanizationResult {
		return HumanizationResult::newSimpleHumanization( 'All of these: ' ); // TODO
	}

	private function humanizeOneMemberSet( HumanizedSetDates $humanizedDates ): HumanizationResult {
		return HumanizationResult::newSimpleHumanization( 'One of these: ' ); // TODO
	}

}
