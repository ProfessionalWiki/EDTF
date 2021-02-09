<?php

declare(strict_types=1);

namespace EDTF;

use EDTF\Contracts\CoversTrait;

class Interval implements EdtfValue
{
    use CoversTrait;

    public const NORMAL    = 0;
    public const OPEN      = 1;
    public const UNKNOWN   = 2;

    private IntervalSide $start;
    private IntervalSide $end;
    private ?int $significantDigit;
    private ?int $estimated;

    public function __construct(
		IntervalSide $start,
		IntervalSide $end,
        ?int $significantDigit = null,
        ?int $estimated = null
    )
    {
    	if ( !$start->isNormalInterval() && !$end->isNormalInterval() ) {
			throw new \InvalidArgumentException( 'Interval needs to have one normal side' );
		}

        $this->start = $start;
        $this->end = $end;
        $this->significantDigit = $significantDigit;
        $this->estimated = $estimated;
    }

    public function getMin(): int
    {
    	// TODO: handle in IntervalSide
        return $this->start->getDate()->getMin();
    }

    public function getMax(): int
    {
		// TODO: handle in IntervalSide
        return $this->end->getDate()->getMax();
    }

    public function getStart(): ExtDate
    {
    	// TODO: why do we need this mehtod?
        return $this->start->getDate();
    }

    public function getEnd(): ExtDate
    {
		// TODO: why do we need this mehtod?
        return $this->end->getDate();
    }

    public function getSignificantDigit(): ?int
    {
        return $this->significantDigit;
    }

    public function getEstimated(): ?int
    {
        return $this->estimated;
    }

	public function isNormalInterval(): bool
	{
		return $this->start->isNormalInterval() && $this->end->isNormalInterval();
	}

	public function isOpenInterval(): bool
	{
		return $this->start->isOpenInterval() || $this->end->isOpenInterval();
	}

	public function isUnknownInterval(): bool
	{
		return $this->start->isUnknownInterval() || $this->end->isUnknownInterval();
	}
}