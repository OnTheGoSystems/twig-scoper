#!/bin/sh
# This script runs the php-scoper, and overwrites the embedded Twig library inside of WPML Core.

WPML_PLUGIN_PATH="${1:-../sitepress-multilingual-cms}"
TWIG_SCOPER_PREFIX="${2:-WPML\Core}"

echo "Clearing the build directory..."
rm -rf ./build/*

vendor/humbug/php-scoper/bin/php-scoper add-prefix -vv --no-interaction --prefix=$TWIG_SCOPER_PREFIX

WPML_LIB_PATH="${WPML_PLUGIN_PATH}/lib/twig"

if [ ! -d "WPML_LIB_PATH" ]; then
    mkdir -p "$WPML_LIB_PATH"
fi

rm -rf "${WPML_LIB_PATH:?}/*"
cp -r ./build/* "${WPML_LIB_PATH}"
