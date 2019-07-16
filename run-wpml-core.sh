#!/bin/sh
# This script runs the php-scoper, and overwrites the embedded Twig library inside of WPML Core.

WPML_CORE_PATH="${1:-../sitepress-multilingual-cms}"
TWIG_SCOPER_PREFIX="${2:-WPML\Core}"

echo "Clearing the build directory..."
rm -rf ./build/*

TWIG_SCOPER_PREFIX="${TWIG_SCOPER_PREFIX}" vendor/humbug/php-scoper/bin/php-scoper -vv --no-interaction add-prefix

rm -rf "${WPML_CORE_PATH}/lib/twig/*"
cp -r ./build/* "${WPML_CORE_PATH}/lib/twig"
