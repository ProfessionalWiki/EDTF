<?php

declare( strict_types = 1 );

namespace EDTF\PackagePrivate\Humanizer;

use EDTF\Contracts\HasPrecision;
use EDTF\EdtfValue;
use EDTF\HumanizationResult;
use EDTF\Humanizer;
use EDTF\Model\Set;
use EDTF\PackagePrivate\Humanizer\Internationalization\MessageBuilder;
use EDTF\StructuredHumanizer;
use InvalidArgumentException;

class PrivateStructuredHumanizer implements StructuredHumanizer {

	private Humanizer $humanizer;
	private MessageBuilder $messageBuilder;

	public function __construct( Humanizer $humanizer, MessageBuilder $messageBuilder ) {
		$this->humanizer = $humanizer;
		$this->messageBuilder = $messageBuilder;
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

	private function humanizeSet( Set $set ): HumanizationResult {
		if ( $set->isSingleElement() ) {
			$edtf = $set->getDates()[0];

			if ( !$edtf instanceof HasPrecision ) {
				throw new InvalidArgumentException( "Set element should support 'precisionAsString' method" );
			}

			$precisionSuffix = $edtf->precisionAsString();
			$humanizedDate = $this->humanizer->humanize( $edtf );

			if ( $set->hasOpenStart() ) {
				return HumanizationResult::newSimpleHumanization(
					$this->message(
						$set->isAllMembers(
						) ? 'edtf-' . $precisionSuffix . '-and-all-earlier' : 'edtf-' . $precisionSuffix . '-or-earlier',
						$humanizedDate
					)
				);
			}

			if ( $set->hasOpenEnd() ) {
				return HumanizationResult::newSimpleHumanization(
					$this->message(
						$set->isAllMembers(
						) ? 'edtf-' . $precisionSuffix . '-and-all-later' : 'edtf-' . $precisionSuffix . '-or-later',
						$humanizedDate
					)
				);
			}
		}

		if ( $set->getDates() === [] ) {
			return HumanizationResult::newSimpleHumanization( $this->message( 'edtf-empty-set' ) );
		}

		$humanizedDates = $this->getHumanizedDatesFromSet( $set );

		if ( $humanizedDates->shouldUseList() ) {
			return HumanizationResult::newStructuredHumanization(
				$humanizedDates->humanizedDates,
				$this->message( $set->isAllMembers() ? 'edtf-all-of-these' : 'edtf-one-of-these' )
			);
		}

		return HumanizationResult::newSimpleHumanization(
			$this->humanizeSetDatesToSingleMessage( $humanizedDates, $set->isAllMembers() )
		);
	}

	private function message( string $key, string ...$parameters ): string {
		return $this->messageBuilder->buildMessage( $key, ...$parameters );
	}

	private function getHumanizedDatesFromSet( Set $set ): HumanizedSetDates {
		$humanizedDates = [];

		foreach ( $set->getDates() as $date ) {
			$humanizedDates[] = $this->humanizer->humanize( $date );
		}

		return new HumanizedSetDates( $humanizedDates );
	}

	private function humanizeSetDatesToSingleMessage( HumanizedSetDates $humanizedDates, bool $isAllMembers ): string {
		if ( count( $humanizedDates->humanizedDates ) === 2 ) {
			return $this->message(
				$isAllMembers ? 'edtf-both-dates' : 'edtf-one-of-two-dates',
				$humanizedDates->humanizedDates[0],
				$humanizedDates->humanizedDates[1]
			);
		}

		return $this->message(
			$isAllMembers ? 'edtf-inline-all-of-these' : 'edtf-inline-one-of-these',
			implode( ', ', $humanizedDates->humanizedDates )
		);
	}

}
