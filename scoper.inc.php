<?php

declare(strict_types=1);

namespace OTGS\TwigPrefixer {

	const TWIG_BASE_DIR = __DIR__ . '/vendor/twig/twig';

	function endsWith( $haystack, $needle ) {
		$length = strlen( $needle );
		if ( $length == 0 ) {
			return true;
		}

		return ( substr( $haystack, - $length ) === $needle );
	}

	function getPrefix() {
		global $argv;
		static $prefix = null;

		if ( $prefix ) {
			return $prefix;
		}

		foreach ( $argv as $arg ) {
			if ( 0 === strpos( $arg, '--prefix=' ) ) {
				$prefix = trim( explode( '=', $arg )[1] );
			}
		}

		return $prefix;
	}

	function getFormattedPrefix( $backslashDuplicationFactor = 0, $includeInitialBackslash = true ) {
		$prefix = getPrefix();

		if( $includeInitialBackslash ) {
			$prefix = '\\' . $prefix;
		}

		for( $i = 0; $i < $backslashDuplicationFactor; $i++ ) {
			$prefix = str_replace( '\\', '\\\\', $prefix );
		}

		return $prefix;
	}

}

namespace {

	use Isolated\Symfony\Component\Finder\Finder;
	use function OTGS\TwigPrefixer\getFormattedPrefix;
	use function OTGS\TwigPrefixer\getPrefix;
	use const OTGS\TwigPrefixer\TWIG_BASE_DIR;

	echo "Loading twig-scoper config with prefix: " . getPrefix() . "\n\n";

	return [

		'prefix' => getPrefix(),

		// For more see: https://github.com/humbug/php-scoper#finders-and-paths
		'finders' => [
			Finder::create()->files()->in( TWIG_BASE_DIR . '/lib' ),
			Finder::create()->files()->in( TWIG_BASE_DIR . '/src' ),
		],

		// When scoping PHP files, there will be scenarios where some of the code being scoped indirectly references the
		// original namespace. These will include, for example, strings or string manipulations. PHP-Scoper has limited
		// support for prefixing such strings. To circumvent that, you can define patchers to manipulate the file to your
		// heart contents.
		//
		// For more see: https://github.com/humbug/php-scoper#patchers
		'patchers' => [

			/**
			 * Patcher for all files.
			 */
			function ( string $filePath, string $prefix, string $contents ): string {
				// Hardcoded class names in code
				$contents = preg_replace(
					'/("|\')((\\\\){1,2}Twig(\\\\){1,2}[A-Za-z\\\\]+)\1/m',
					'$1' . getFormattedPrefix( 2 ) . '$2$1',
					$contents
				);

				// Hardcoded "use" statements
				$contents = preg_replace(
					'/use\s+(Twig)(\\\\){1,2}/m',
					'use ' . getFormattedPrefix( 2 ) . '\\\\\\\\Twig\\\\\\\\',
					$contents
				);

				// Add namespaces to generated Twig template names
				$contents = preg_replace(
					'/(\'|")(__TwigTemplate_)\1/m',
					'$1' . getFormattedPrefix( 2 ) . '\\\\\\\\$2$1',
					$contents
				);

				return $contents;
			},

			/**
			 * Patcher for \$prefix\Twig\Node\ModuleNode.
			 */
			function ( string $filePath, string $prefix, string $contents ): string {
				if ( \OTGS\TwigPrefixer\endsWith( $filePath, 'src' . DIRECTORY_SEPARATOR . 'Node' . DIRECTORY_SEPARATOR . 'ModuleNode.php' ) ) {
					// Fix template compilation - add the namespace to the template file.
					$contents = preg_replace(
						'/(compileClassHeader\s*\([^\)]+\)\s*{\s*\s*\$compiler\s*->\s*write\s*\(\s*)"\\\\n\\\\n"(\s*\)\s*;)/m',
						'$1"\\n\\nnamespace ' . getFormattedPrefix( 1, false ) . ';\\n\\n"$2',
						$contents
					);

					// When generating the PHP template, make sure its class declaration doesn't contain the namespace.
					// That's the only place where we don't want to have it.
					$string_to_remove =  getFormattedPrefix() . '\\';
					$contents = preg_replace(
						'/(->write\s*\(\s*\'class \'\s*\.\s*)(\$compiler\s*->\s*getEnvironment\s*\(\s*\)\s*->\s*getTemplateClass\s*\(\s*\$this\s*->\s*getSourceContext\s*\(\s*\)\s*->\s*getName\s*\(\s*\)\s*,\s*\$this\s*->\s*getAttribute\s*\(\s*\'index\'\s*\)\s*\))/m',
						'$1 \\substr( $2, ' . strlen( $string_to_remove ) . ' ) ',
						$contents
					);
				}

				return $contents;
			},

			/**
			 * Patcher for \$prefix\Twig\Extension\CoreExtension.
			 */
			function ( string $filePath, string $prefix, string $contents ): string {
				// Fix the usage of global twig_* and _twig_* functions.
				if ( \OTGS\TwigPrefixer\endsWith( $filePath, 'src' . DIRECTORY_SEPARATOR . 'Extension' . DIRECTORY_SEPARATOR . 'CoreExtension.php' ) ) {
					$contents = preg_replace(
						'/(new ' . getFormattedPrefix( 1 ) . '\\\\Twig\\\\TwigFilter\(\s*\'[^\']+\'\s*,\s*\')((_)?twig_[^\']+\')/m',
						'$1' . getFormattedPrefix( 2 ) . '\\\\\\\\$2',
						$contents
					);

					// Also handle the occurrence in the is_safe_callback array element.
					$contents = preg_replace(
						'/(new ' . getFormattedPrefix( 1 ) . '\\\\Twig\\\\TwigFilter\(\s*\'[^\']+\'\s*,\s*\'.*twig_[^\']+\',\s*\[[^\]]*,\s*\'is_safe_callback\'\s*=>\s*\')((_)?twig_[^\']+\'\s*\]\s*\))/m',
						'$1' . getFormattedPrefix( 2 ) . '\\\\\\\\$2',
						$contents
					);

				}

				return $contents;
			},
		],
	];

}
