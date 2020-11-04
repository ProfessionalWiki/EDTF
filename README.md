# EDTF PHP Library

[![GitHub Workflow Status](https://img.shields.io/github/workflow/status/ProfessionalWiki/EDTF/CI)](https://github.com/ProfessionalWiki/EDTF/actions?query=workflow%3ACI)
[![Type Coverage](https://shepherd.dev/github/ProfessionalWiki/EDTF/coverage.svg)](https://shepherd.dev/github/ProfessionalWiki/EDTF)

EDTF PHP is a small library for parsing, representing and working with the
[Extended Date/Time Format](https://www.loc.gov/standards/datetime/) specification.

## Usage

TODO

## Installation

To use the EDTF library in your project, simply add a dependency on professional-wiki/edtf
to your project's `composer.json` file. Here is a minimal example of a `composer.json`
file that just defines a dependency on UPDATE_NAME 1.x:

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
