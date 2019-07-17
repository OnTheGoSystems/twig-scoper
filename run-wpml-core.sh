#!/bin/sh
# This script runs the php-scoper, and overwrites the embedded Twig library inside of WPML Core.

WPML_CORE_PATH="${1:-../sitepress-multilingual-cms}"
TWIG_SCOPER_PREFIX="${2:-WPML\Core}"

echo "Clearing the build directory..."
rm -rf ./build/*

vendor/humbug/php-scoper/bin/php-scoper add-prefix -vv --no-interaction --prefix=$TWIG_SCOPER_PREFIX

rm -rf "${WPML_CORE_PATH}/lib/twig/*"
cp -r ./build/* "${WPML_CORE_PATH}/lib/twig"
