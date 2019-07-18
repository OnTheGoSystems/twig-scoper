#!/bin/sh
# This script runs the php-scoper, and overwrites the embedded Twig library inside of WCML.

WCML_PLUGIN_PATH="${1:-../woocommerce-multilingual}"
TWIG_SCOPER_PREFIX="${2:-WCML}"

echo "Clearing the build directory..."
rm -rf ./build/*

vendor/humbug/php-scoper/bin/php-scoper add-prefix -vv --no-interaction --prefix=$TWIG_SCOPER_PREFIX

WCML_LIB_PATH="${WCML_PLUGIN_PATH}/lib/twig"

if [ ! -d "WCML_LIB_PATH" ]; then
    mkdir -p "$WCML_LIB_PATH"
fi

rm -rf "${WCML_LIB_PATH}/*"
cp -r ./build/* "${WCML_LIB_PATH}"
