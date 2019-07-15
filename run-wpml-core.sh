#!/bin/sh
# This script runs the php-scoper, and overwrites the embedded Twig library inside of WPML Core.

WPML_CORE_PATH="${1:-../sitepress-multilingual-cms}"

vendor/humbug/php-scoper/bin/php-scoper -vv --no-interaction add-prefix --config=wpml-core.scoper.inc.php
rm -rf "${WPML_CORE_PATH}/lib/twig/*"
cp -r ./build/* "${WPML_CORE_PATH}/lib/twig"
