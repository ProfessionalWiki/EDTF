<?php

declare( strict_types = 1 );

namespace EDTF\PackagePrivate\Humanizer;

use EDTF\EdtfValue;
use EDTF\HumanizationResult;
use EDTF\Humanizer;
use EDTF\Model\Set;
use EDTF\Model\SetElement;
use EDTF\Model\SetElement\OpenSetElement;
use EDTF\Model\SetElement\RangeSetElement;
use EDTF\Model\SetElement\SingleDateSetElement;
use EDTF\PackagePrivate\Humanizer\Internationalization\MessageBuilder;
use EDTF\StructuredHumanizer;

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
		if ( $set->isEmpty() ) {
			return HumanizationResult::newSimpleHumanization( $this->message( 'edtf-empty-set' ) );
		}

		if ( count( $set->getElements() ) === 1 ) {
			$element = $set->getElements()[0];

			if ( $element instanceof OpenSetElement || $element instanceof RangeSetElement ) {
				return HumanizationResult::newSimpleHumanization(
					$this->humanizeSetElement( $element, $set->isAllMembers() )
				);
			}
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

		foreach ( $set->getElements() as $setElement ) {
			$humanizedDates[] = $this->humanizeSetElement( $setElement, $set->isAllMembers() );
		}

		return new HumanizedSetDates( $humanizedDates );
	}

	private function humanizeSetElement( SetElement $setElement, bool $isAllMembers ): string {
		if ( $setElement instanceof SingleDateSetElement ) {
			return $this->humanizer->humanize( $setElement->getDate() );
		}

		if ( $setElement instanceof RangeSetElement ) {
			return $this->humanizeRangeSetElement( $setElement, $isAllMembers );
		}

		if ( $setElement instanceof OpenSetElement ) {
			return $this->humanizeOpenSetElement( $setElement, $isAllMembers );
		}

		return '';
	}

	private function humanizeRangeSetElement( RangeSetElement $rangeSetElement, bool $isAllMembers ): string {
		$precisionSuffix = $rangeSetElement->getStart()->precisionAsString();

		return $this->message(
			'edtf-set-range-' . ( $isAllMembers ? 'all' : 'one' ) . '-' . $precisionSuffix,
			$this->humanizer->humanize( $rangeSetElement->getStart() ),
			$this->humanizer->humanize( $rangeSetElement->getEnd() )
		);
	}

	private function humanizeOpenSetElement( OpenSetElement $openSetElement, bool $isAllMembers ): string {
		$humanizedDate = $this->humanizer->humanize( $openSetElement->getDate() );
		$precisionSuffix = $openSetElement->getDate()->precisionAsString();

		if ( $openSetElement->isOpenEnd() ) {
			return $this->message(
				$isAllMembers ? 'edtf-' . $precisionSuffix . '-and-all-later' : 'edtf-' . $precisionSuffix . '-or-later',
				$humanizedDate
			);
		}

		return $this->message(
			$isAllMembers ? 'edtf-' . $precisionSuffix . '-and-all-earlier' : 'edtf-' . $precisionSuffix . '-or-earlier',
			$humanizedDate
		);
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
