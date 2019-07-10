# Twig prefixer

Utility that uses [php-scoper](https://github.com/humbug/php-scoper) to add a 
namespace to [Twig](https://github.com/twigphp/Twig) 1.42.*

It applies a series of patches to make sure things keep working despite Twig's 
codebase, which presents several surprises (global functions, hardcoded class
names, dynamically rendered PHP code, etc).

The magic happens in scoper.inc.php

## Installation

1. Clone from git
2. `composer install`

## Usage

Running `vendor/bin/php-scoper add-prefix` will produce the scoped version of 
Twig in the `build` directory.

Alternatively, you can run the `run-toolset.sh` script if you're dealing with
Toolset development directly (see the script for further details).

## Considerations

Obviously, the patching is ridiculously hacky and may not work well between 
different Twig versions (that's also why is the version hardcoded in `composer.json`).
Be very careful if you upgrade Twig and test everything before putting it into
production. Otherwise, no guarantees are provided! 
