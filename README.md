# PHP Library Template

[![GitHub Workflow Status](https://img.shields.io/github/workflow/status/JeroenDeDauw/new-php-library/CI)](https://github.com/JeroenDeDauw/new-php-library/actions?query=workflow%3ACI)
[![Type Coverage](https://shepherd.dev/github/JeroenDeDauw/new-php-library/coverage.svg)](https://shepherd.dev/github/JeroenDeDauw/new-php-library)

This is a template for starting new PHP libraries. Copy or fork to get started quickly.

## Usage

TODO

## Installation

To use the UPDATE_NAME library in your project, simply add a dependency on UPDATE/NAME
to your project's `composer.json` file. Here is a minimal example of a `composer.json`
file that just defines a dependency on UPDATE_NAME 1.x:

```json
{
    "require": {
        "UPDATE/NAME": "~1.0"
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
