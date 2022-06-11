<!---
Copyright (c) 2018-2022 Yann 'Ze' Richard <yann.richard@univ-rennes2.fr>
SPDX-License-Identifier: LGPL-3.0-or-later
License-Filename: LICENSE
-->
# Standford's like password policy : PHP implementation

[![Latest Stable Version](https://img.shields.io/packagist/v/universiterennes2/standfordlikepasswordpolicy)](https://packagist.org/packages/universiterennes2/standfordlikepasswordpolicy)
[![REUSE compliant](https://reuse.software/badge/reuse-compliant.svg)](https://reuse.software/)
[![Minimum PHP Version](https://img.shields.io/packagist/php-v/universiterennes2/standfordlikepasswordpolicy?color=green&style=flat-square)](https://php.net/)
[![Unit tests](https://github.com/DSI-Universite-Rennes2/php-standford-like-password-policy/actions/workflows/unit-tests.yml/badge.svg)](https://github.com/DSI-Universite-Rennes2/php-standford-like-password-policy/actions/workflows/unit-tests.yml)
[![Coverage Status](https://coveralls.io/repos/github/DSI-Universite-Rennes2/php-standford-like-password-policy/badge.svg?branch=main)](https://coveralls.io/github/DSI-Universite-Rennes2/php-standford-like-password-policy?branch=main)
[![License](https://img.shields.io/packagist/l/universiterennes2/standfordlikepasswordpolicy?color=gree)](LICENSE)

This library implements a checker for Standford's password policy in PHP
with only one minor change :

- The minimal password's length is 9 (instead of 8 in the original policy)

The [Standford password policy](https://uit.stanford.edu/service/accounts/passwords)
is a length-based password policy : increase password length = decrease constraints.

- 9-11 character passwords require the use of upper and lower case, numerical and special characters.
- 12-15 character passwords require the use of upper and lower case and numerical characters.
- 16-19 character passwords require upper and lower case characters
- 20+ characters require any characters.

## Table of Contents

- [Standford's like password policy : PHP implementation](#standfords-like-password-policy--php-implementation)
  - [Table of Contents](#table-of-contents)
  - [Install](#install)
  - [Usage](#usage)
  - [Contribute](#contribute)
  - [License](#license)

## Install

```
composer require universiterennes2/standfordlikepasswordpolicy
```

## Usage

```
<?php
require_once __DIR__ . "/vendor/autoload.php";

use UniversiteRennes2\StandfordLikePasswordPolicy\StandfordLikePasswordPolicy;

$passwordPolicy = new StandfordLikePasswordPolicy();

$password = 'not compliant';

if ( $passwordPolicy->isCompliant($password) ) {
    // Compliant password
    echo "Compliant\n";
} else {
    // Not compliant !
    echo "Not compliant\n";
}
```

See a more complete example in `examples/` directory.

## Contribute

See [CONTRIBUTING.md](CONTRIBUTING.md)

## License

This program is free software: you can redistribute it and/or modify
it under the terms of the [GNU Lesser General Public License v3.0 or later](LICENSE)
as published by the Free Software Foundation.

The program in this repository meet the requirements to be REUSE compliant,
meaning its license and copyright is expressed in such as way so that it
can be read by both humans and computers alike.

For more information, see https://reuse.software/
