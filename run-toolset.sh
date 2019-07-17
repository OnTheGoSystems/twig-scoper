#!/bin/sh
# This script runs the php-scoper, overwrites the embedded Twig library inside of Toolset Common
# and recreates the autoloader classmap of Toolset Common.

TOOLSET_COMMON_PATH="${1:-../tcl-override/toolset-common}"
TWIG_SCOPER_PREFIX="${2:-OTGS\Toolset}"

echo "Clearing the build directory..."
rm -rf ./build/*

vendor/humbug/php-scoper/bin/php-scoper -vv --no-interaction add-prefix --prefix="${TWIG_SCOPER_PREFIX}"

echo "Removing Twig from Toolset Common..."
rm -rf "${TOOLSET_COMMON_PATH}/lib/Twig/*"

echo "Copying the new version of Twig into Toolset Common..."
cp -r ./build/* "${TOOLSET_COMMON_PATH}/lib/Twig"

echo "Recreating Toolset Common classmaps..."
cd "${TOOLSET_COMMON_PATH}"
./recreate_classmap.sh
cd -
