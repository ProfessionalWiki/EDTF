# EDTF PHP Library

[![GitHub Workflow Status](https://img.shields.io/github/workflow/status/ProfessionalWiki/EDTF/CI)](https://github.com/ProfessionalWiki/EDTF/actions?query=workflow%3ACI)
[![Type Coverage](https://shepherd.dev/github/ProfessionalWiki/EDTF/coverage.svg)](https://shepherd.dev/github/ProfessionalWiki/EDTF)
[![codecov](https://codecov.io/gh/ProfessionalWiki/EDTF/branch/master/graph/badge.svg?token=GnOG3FF16Z)](https://codecov.io/gh/ProfessionalWiki/EDTF)
[![Latest Stable Version](https://poser.pugx.org/professional-wiki/edtf/version.png)](https://packagist.org/packages/professional-wiki/edtf)
[![Download count](https://poser.pugx.org/professional-wiki/edtf/d/total.png)](https://packagist.org/packages/professional-wiki/edtf)

EDTF PHP is a small library for parsing, representing and working with the
[Extended Date/Time Format](https://www.loc.gov/standards/datetime/) specification.

## Usage

### Parsing

```php
$parser = new EDTF\EdtfParser();
$parsingResult = $parser->parse('1985-04-12T23:20:30');
$parsingResult->isValid(); // true
$parsingResult->getEdtfValue(); // \EDTF\EdtfValue
$parsingResult->getInput(); // '1985-04-12T23:20:30'
```

### Validating

```php
$validator = EDTF\EdtfValidator::newInstance();
$validator->isValidEdtf('1985-04-12T23:20:30'); // true
````

### Humanizing

```php
$humanizer = EDTF\Humanize\HumanizerFactory::newStringHumanizerForLanguage( 'en' );
$humanizer->humanize('1985-12/2004~'); // 'December 1985 to circa 2004'
````

### Object model

```php
$edtf->getMax(); // int
$edtf->getMin(); // int
$edtf->covers(\EDTF\EdtfValue $edtf); // bool
```

```php
$edtfDate->getYear(); // int
$edtfDate->isOpenInterval(); // bool
$edtfDate->getQualification(); // \EDTF\Qualification
```

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
    
You can run the style checks by executing

    make cs
    
To run all CI checks, execute

    make ci
    
You can also invoke PHPUnit directly to pass it arguments, as follows

    vendor/bin/phpunit --filter SomeClassNameOrFilter
