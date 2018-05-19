# Standford's like password policy : PHP implementation

This library implements a checker for Standford's password policy in PHP 
with only one minor change :

- The minimal password's length is 9 (instead of 8 in the original policy)

The [Standford password policy](https://uit.stanford.edu/service/accounts/passwords) 
is a length-based password policy : increase password length = decrease constraints.

- 9-11 character passwords require the use of upper and lower case, numerical and special characters.
- 12-15 character passwords require the use of upper and lower case and numerical characters.
- 16-19 character passwords require upper and lower case characters
- 20+ characters require lower case characters.

## Table of Contents

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
} else {
    // Not compliant !
}
```

## Contribute

See [CONTRIBUTING.md](CONTRIBUTING.md)

## License

This program is free software: you can redistribute it and/or modify
it under the terms of the [GNU Lesser General Public License v3.0 or later](LICENCE)
as published by the Free Software Foundation.

The program in this repository meet the requirements to be REUSE compliant,
meaning its license and copyright is expressed in such as way so that it
can be read by both humans and computers alike.

For more information, see https://reuse.software/
