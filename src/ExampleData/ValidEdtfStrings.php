<?php

declare( strict_types = 1 );

namespace EDTF\ExampleData;

use Generator;

/**
 * Valid EDTF strings that can be used for automated testing.
 *
 * @codeCoverageIgnore (since PHPUnit fails to mark code executed via dataProvider as covered)
 */
class ValidEdtfStrings {

	/**
	 * Values taken from https://www.loc.gov/standards/datetime/
	 */
	public static function allFromStandard(): Generator {
		yield from self::level0();
		yield from self::level1();
		yield from self::level2();
	}

	public static function all(): Generator {
		yield from self::allFromStandard();
		yield from self::luxSample();
	}

	public static function level0(): Generator {
		yield '1985-04-12';
		yield '1904-02-29';
		yield '1985-04';
		yield '1985';

		yield '1985-04-12T23:20:30';
		yield '1985-04-12T23:20:30Z';
		yield '1985-04-12T23:20:30-04';
		yield '1985-04-12T23:20:30+04:30';

		yield '1964/2008';
		yield '2004-06/2006-08';
		yield '2004-02-01/2005-02-08';
		yield '2004-02-01/2005-02';
		yield '2004-02-01/2005';
		yield '2005/2006-02';
	}

	public static function level1(): Generator {
		yield 'Y170000002';
		yield 'Y-170000002';

		yield '2001-21';

		yield '1984?';
		yield '2004-06~';
		yield '2004-06-11%';

		yield '201X';
		yield '20XX';
		yield '2004-XX';
		yield '1985-04-XX';
		yield '1985-XX-XX';

		yield '1985-04-12/..';
		yield '1985-04/..';
		yield '1985/..';

		yield '../1985-04-12';
		yield '../1985-04';
		yield '../1985';

		yield '1985-04-12/';
		yield '1985-04/';
		yield '1985/';

		yield '/1985-04-12';
		yield '/1985-04';
		yield '/1985';

		yield '-1985';
	}

	public static function level2(): Generator {
		yield 'Y-17E7';

		yield '1950S2';
		yield 'Y171010000S3';
		yield 'Y3388E2S3';

		yield '2001-34';

		yield '[1667,1668,1670..1672]';
		yield '[..1760-12-03]';
		yield '[1760-12..]';
		yield '[1760-01,1760-02,1760-12..]';
		yield '[1667,1760-12]';
		yield '[..1984]';

		yield '{1667,1668,1670..1672}';
		yield '{1960,1961-12}';
		yield '{..1984}';
		yield '{1980..1985}';
		yield '{2001-01..2002-05}';
		yield '{1987-01-04..1988-01-05}';

		yield '2004-06-11%';
		yield '2004-06~-11';
		yield '2004?-06-11';

		yield '?2004-06-~11';
		yield '2004-%06-11';

		yield '156X-12-25';
		yield '15XX-12-25';
		yield 'XXXX-12-XX';
		yield '1XXX-XX';
		yield '1XXX-12';
		yield '1984-1X';

		yield '2004-06-~01/2004-06-~20';
		yield '2004-06-XX/2004-07-03';
	}

	public static function luxSample(): Generator {
		yield '-0044';
		yield '-0100';
		yield '02XX';
		yield '0658?';
		yield '0738?';
		yield '1530?';
		yield '1591-08-31';
		yield '1896-01-23';
		yield '1928-10-30';
		yield '1985-07-09';
		yield '1520-06';
		yield '1520';

		// From Representation Spreadsheet
		yield '1985-04-12';
		yield '1985-04';
		yield '1985';
		yield '1985-04-12T23:20:30';
		yield '1985-04-12T23:20:30+04:30';
		yield '1985-04-12T23:20:30+04';
		yield '1964/2008';
		yield '2004-06/2006-08';
		yield '2004-02-01/2005-02';
		yield 'Y1700000002';
		yield 'Y-1700000002';
		yield '-1985';
		yield '2001-21';
		yield '1985-04-12?';
		yield '1985-04?';
		yield '1985~';
		yield '1985-04-XX';
		yield '1985-XX-XX';
		yield '2004-XX';
		yield '201X';
		yield '20xx';
		yield '2XXX';
		yield '1985-04-12/..';
		yield '1986-04/';
		yield '/1985';
		yield '1984?/2004%';
		yield '1984-01-02~/2004-06-04';
		yield '1984~/..';
		yield 'Y-17E7';
		yield '1950S2';
		yield 'Y171010000S3';
		yield 'Y3388E2S3';
		yield '2001-34';
		yield '2001-30';
		yield '{1960, 1961-12}';
		yield '[1667, 1760-12]';
		yield '{..1983-12-31,1984-10-10..1984-11-01,1984-11-05..}';
		yield '12004-06~-11';
		yield '22004?-06-11';
		yield '?2004-06-~11';
		yield '2004-06-~01/2004-06-~20';
		//yield '..2004-06-01/~2004-06-20';
		yield '1156X-12-25';
		yield '2XXXX-12-XX';
		yield '31XXX-XX';

		// These are from the ISO standard document, should validate
		yield '1985-04-12';
		yield '1985-04';
		yield '1985';
		yield '1985-04-12T23:20:30';
		yield '1985-04-12T23:20:30Z';
		yield '1985-04-12T23:20:30+04:30';
		yield '1985-04-12T23:20:30+04';
		yield '1964/2008';
		yield '2004-06/2006-08';
		yield '2004-02-01/2005-02-08';
		yield '2004-02-01/2005-02';
		yield '2004-02-01/2005';
		yield '2005/2006-02';
		yield 'Y1700000002';
		yield '-1985';
		yield '2001-21';
		yield '1985-04-12?';
		yield '1985-04?';
		yield '1985~';
		yield '1985-04-XX';
		yield '1985-XX-XX';
		yield '2004-XX';
		yield '201X';
		yield '20XX';
		yield '1985-04-12/..';
		yield '../1985-04-12';
		yield '1986-04/';
		yield '/1985';
		yield '1984?/2004%';
		yield '1984-01-02~/2004-06-04';
		yield '1984~/2004-06';
		yield '../1985-04-12?';
		yield '1985-04-12~/';
		yield 'Y-17E7';
		yield '1950S2';
		yield 'Y171010000S3';
		yield 'Y3388E2S3';
		yield '2001-34';
		yield '{1960, 1961-12}';
		yield '[1667, 1760-12]';
//		yield '..1984';
//		yield '1984..';
//		yield '1670..1673';

		yield '[..1983-12-31,1984-10-10..1984-11-01,1984-11-05..]';

		yield '2004-06~-11';
		yield '2004?-06-11';
		yield '?2004-06-~11';
		yield '2004-%06-11';
		yield '2004-06-~01/2004-06-~20';
//		yield '..2004-06-01/~2004-06-20';
//		yield '2004-06-01~/2004-06-20..';
		yield '156X-12-25';
		yield 'XXXX-12-XX';
		yield '1XXX-XX';
		yield '1XXX-12';
	}

}
