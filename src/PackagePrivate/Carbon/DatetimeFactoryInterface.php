<?php

declare( strict_types = 1 );

namespace EDTF\PackagePrivate\Carbon;

/**
 * FIXME: return type missing
 * TODO: what value does this interface bring?
 * FIXME: Utils is a bad NS. Also: maybe this should be PackagePrivate
 */
interface DatetimeFactoryInterface {

	/**
	 * @throws DatetimeFactoryException
	 */
	public function create( int $year = 0, int $month = 1, int $day = 1, int $hour = 0, int $minute = 0, int $second = 0, $tz = null );
}