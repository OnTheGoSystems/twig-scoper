#!/bin/sh
# This script runs the php-scoper, overwrites the embedded Twig library inside of Toolset Common
# and recreates the autoloader classmap of Toolset Common.

TOOLSET_COMMON_PATH="${1:-../tcl-override/toolset-common}"

vendor/bin/php-scoper -vv --no-interaction add-prefix
rm -rf "${TOOLSET_COMMON_PATH}/lib/Twig/*"
cp -r ./build/* "${TOOLSET_COMMON_PATH}/lib/Twig"
cd "${TOOLSET_COMMON_PATH}"
./recreate_classmap.sh
cd -
