<?php

declare( strict_types = 1 );

namespace EDTF\PackagePrivate\Humanizer;

use EDTF\EdtfValue;
use EDTF\HumanizationResult;
use EDTF\Humanizer;
use EDTF\Model\Set;
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
			return HumanizationResult::newSimpleHumanization( 'Empty set' ); // TODO i18n
		}

		$humanizedDates = $this->getHumanizedDatesFromSet( $edtf );

		if ( $humanizedDates->shouldUseList() ) {
			return HumanizationResult::newStructuredHumanization(
				$humanizedDates->humanizedDates,
				$edtf->isAllMembers() ? 'All of these:' : 'One of these:' // TODO i18n
			);
		}

		return HumanizationResult::newSimpleHumanization(
			$this->humanizeSetDatesToSingleMessage( $humanizedDates, $edtf->isAllMembers() )
		);
	}

	private function getHumanizedDatesFromSet( Set $edtf ): HumanizedSetDates {
		$humanizedDates = [];

		foreach ( $edtf->getDates() as $date ) {
			$humanizedDates[] = $this->humanizer->humanize( $date );
		}

		return new HumanizedSetDates( $humanizedDates );
	}

	private function humanizeSetDatesToSingleMessage( HumanizedSetDates $humanizedDates, bool $isAllMembers ): string {
		if ( count( $humanizedDates->humanizedDates ) === 2 ) {
			if ( $isAllMembers ) {
				return $humanizedDates->humanizedDates[0] . ' and ' . $humanizedDates->humanizedDates[1]; // TODO i18n
			}

			return $humanizedDates->humanizedDates[0] . ' or ' . $humanizedDates->humanizedDates[1]; // TODO i18n
		}

		if ( $isAllMembers ) {
			return 'All of these: ' . implode( ', ', $humanizedDates->humanizedDates ); // TODO i18n
		}

		return 'One of these: ' . implode( ', ', $humanizedDates->humanizedDates ); // TODO i18n
	}

}
