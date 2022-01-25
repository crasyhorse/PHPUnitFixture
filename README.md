PHP-Unit Fixture [![Release](https://img.shields.io/github/v/release/crasyhorse/PHPUnitFixture)](https://github.com/crasyhorse/PHPUnitFixture/releases/latest) [![Downloads](https://img.shields.io/github/downloads/crasyhorse/PHPUnitFixture/total)](https://github.com/crasyhorse/PHPUnitFixture)
=========

Inspired by the fixture command of [cypress-io/cypress](https://github.com/cypress-io/cypress)

* Installable via Composer
* Easy to configure
* Simple to use

Built with PHP 7.4, PHP-Unit 9.5.7 and XDebug 2.9.8

# Overview
* [Documentation](https://github.com/crasyhorse/PHPUnitFixture/blob/master/DOCUMENTATION.md)
* [API](https://github.com/crasyhorse/PHPUnitFixture/blob/master/docs/api/index.html)
# Installation
<!-- [![Package](https://img.shields.io/badge/Composer%20package-0.3.3-brightgreen)](https://github.com/crasyhorse/PHPUnitFixture/releases/latest) -->

PHP-Unit Fixture has actualy not been published to [packagist.org](https://packagist.org) because it is still in development.

To install it you need to configure this Github repository in your composer.json.

```json
    "repositories": [
        {
            "type": "vcs",
            "url":  "https://github.com/crasyhorse/PHPUnitFixture.git"
        }
    ],
```

Now you may install the package via Composer 2.x.

```
composer require --dev crasyhorse/phpunit-fixture
```

# License

[![License](https://img.shields.io/github/license/crasyhorse/PHPUnitFixture?color=light%20green)](https://github.com/crasyhorse/PHPUnitFixture/blob/master/LICENSE.md)

This project is licensed under the terms of the [MIT License](https://github.com/crasyhorse/PHPUnitFixture/blob/master/LICENSE.md).