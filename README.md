# EDTF PHP Library

[![GitHub Workflow Status](https://img.shields.io/github/workflow/status/ProfessionalWiki/EDTF/CI)](https://github.com/ProfessionalWiki/EDTF/actions?query=workflow%3ACI)
[![Type Coverage](https://shepherd.dev/github/ProfessionalWiki/EDTF/coverage.svg)](https://shepherd.dev/github/ProfessionalWiki/EDTF)
[![codecov](https://codecov.io/gh/ProfessionalWiki/EDTF/branch/master/graph/badge.svg?token=GnOG3FF16Z)](https://codecov.io/gh/ProfessionalWiki/EDTF)
[![Latest Stable Version](https://poser.pugx.org/professional-wiki/edtf/version.png)](https://packagist.org/packages/professional-wiki/edtf)
[![Download count](https://poser.pugx.org/professional-wiki/edtf/d/total.png)](https://packagist.org/packages/professional-wiki/edtf)
[![License](https://img.shields.io/packagist/l/professional-wiki/edtf)](LICENSE)

EDTF PHP is a small library for parsing, representing and working with the
[Extended Date/Time Format] specification.

EDTF PHP was created by and is maintained by [Professional.Wiki]. Initial development was funded by the Luxembourg Ministry of Culture.
It is an open source project, and contributions are welcome!

- [Usage](#usage)
  * [Parsing](#parsing)
  * [Validating](#validating)
  * [Humanizing](#humanizing)
  * [Object model](#object-model)
- [EDTF support and limits](#edtf-support-and-limits)
- [Installation](#installation)
- [Development](#development)
- [Release notes](#release-notes)

## Usage

### Parsing

```php
$parser = \EDTF\EdtfFactory::newParser();
$parsingResult = $parser->parse('1985-04-12T23:20:30');
$parsingResult->isValid(); // true
$parsingResult->getEdtfValue(); // \EDTF\EdtfValue
$parsingResult->getInput(); // '1985-04-12T23:20:30'
```

### Validating

```php
$validator = \EDTF\EdtfFactory::newValidator();
$validator->isValidEdtf('1985-04-12T23:20:30'); // true
````

### Humanizing

```php
$humanizer = \EDTF\EdtfFactory::newHumanizerForLanguage( 'en' );
$humanizer->humanize($edtfValue); // string
````

### Object model

```php
$edtfValue->getMax(); // int
$edtfValue->getMin(); // int
$edtfValue->covers(\EDTF\EdtfValue $anotherValue); // bool
```

```php
$edtfDate->getYear(); // int
$edtfDate->isOpenInterval(); // bool
$edtfDate->getQualification(); // \EDTF\Qualification
```

## EDTF support and limits

All level 0, 1 and 2 EDTF formats can be parsed and represented, except for:

* Open ranges with a date (Level 2: Qualification): `..2004-06-01/2004-06-20` (This is supported: `../2004-06-20`)

Humanization has more limits:

* Significant digits (EDTF level 2): `1950S2` (some year between 1900 and 1999, estimated to be 1950)
* Group Qualification (EDTF level 2): `2004-06~-11` (year and month approximate)
* Qualification of Individual Component (EDTF level 2): `?2004-06-~11` (year uncertain; month known; day approximate)
* Level 2 Unspecified Digit: `1XXX-1X` (October, November, or December during the 1000s)

## Installation

To use the EDTF library in your project, simply add a dependency on professional-wiki/edtf
to your project's `composer.json` file. Here is a minimal example of a `composer.json`
file that just defines a dependency on EDTF 1.x:

```json
{
    "require": {
        "professional-wiki/edtf": "~1.0"
    }
}
```

## Development

Start by installing the project dependencies by executing

    composer update

You can run the tests by executing

    make test
    
You can run style checks and static analysis by executing

    make cs
    
To run all CI checks, execute

    make
    
You can also invoke PHPUnit directly to pass it arguments, as follows

    vendor/bin/phpunit --filter SomeClassNameOrFilter

## Release notes

### Version 2.0.2 - 2022-04-29

* Improved translations

### Version 2.0.1 - 2022-02-19

* `?` is no longer recognized as valid date

### Version 2.0.0 - 2021-04-28

* Fixed performance issue for sets with large range elements like `1000-01-01..2000-12-30`
* Fixed humanization of sets with more than one element, of which at least one an open range
* Improved humanization of sets with range elements like `2000..2010`
* Intervals and set ranges with end dates earlier than their start dates are now rejected
* Various breaking changes to the `Set` class
  * Constructor signature changed
  * Removed `hasOpenStart` and `hasOpenEnd`
  * Removed `isSingleElement`
* Added `Set::isEmpty`
* Added `Set::getElements`
* Added `SetElement` interface with implementations
  * `OpenSetElement`
  * `RangeSetElement`
  * `SingleDateSetElement`
* `ExtDate::precision` and `Season::precision` are now guaranteed to return an integer
* `precisionAsString` in `ExtDate` and `Season` is now guaranteed to return a non-empty string

### Version 1.3.0 - 2021-04-26

* Fixed season support in intervals
* Fixed parsing of open sets with an extra space like `{ ..2021}` (thanks @chaudbak)
* Added `ExtDate::iso8601` and `ExtDateTime::iso8601` (thanks @seth-shaw-unlv)
* Added `ParsingResult::getErrorMessage`

### Version 1.2.0 - 2021-04-16

* Improved humanization of open sets

### Version 1.1.0 - 2021-03-20

* Added internationalization to the `StructuredHumanizer` service
* Fixed handling of "year 0"

### Version 1.0.0 - 2021-03-19

* Initial release with
    * Support for EDTF levels 0, 1 and 2
    * Parsing
    * Object model
    * Internationalized humanization
    * Validation service
    * Example data

[Professional.Wiki]: https://professional.wiki
[Extended Date/Time Format]: https://www.loc.gov/standards/datetime/
