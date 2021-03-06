# Twig scoper

Utility that uses [php-scoper](https://github.com/humbug/php-scoper) to add a 
namespace to [Twig](https://github.com/twigphp/Twig) 1.42.*

It applies a series of patches to make sure things keep working despite Twig's 
codebase, which presents several pleasant surprises (global functions, hardcoded class
names, dynamically rendered PHP code, etc).

The (dark) magic happens in [scoper.inc.php](./scoper.inc.php)

![](demo.gif)

## Installation

1. Clone from git
2. `composer install`

## Usage

Running this command will produce the scoped version of 
Twig in the `build` directory:

```bash
vendor/bin/php-scoper add-prefix --prefix='My\Safe\Namespace' 
```

Alternatively, you can run the `run-toolset.sh` script if you're dealing with
[Toolset](https://toolset.com) development directly ([see the script](./run-toolset.sh) for further 
details). For [WPML](https://wpml.org) development, use [`run-wpml-core.sh`](./run-wpml-core.sh).

## Considerations

Obviously, the patching is ridiculously hacky and may not work well between 
different Twig versions (that's also why is the version hardcoded in `composer.json`).
**Be very careful if you upgrade Twig and test everything before putting it into
production.** Otherwise, no guarantees are provided! 

Made with :heart: for [Toolset](http://toolset.com) and [OnTheGoSystems](http://onthegosystems.com).
