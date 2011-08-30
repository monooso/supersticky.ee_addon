#!/bin/bash

echo "Enter the path to the target 'system' directory (e.g. /Users/Stephen/Sites/mysite/sysdir/):"
read SYSTEM_DIRECTORY

echo "Enter the path to the target 'themes' directory (e.g. /Users/Stephen/Sites/mysite/themes/):"
read THEMES_DIRECTORY

addon_package_name="supersticky"

if [ -d "$SYSTEM_DIRECTORY" ]; then
    ln -sf $PWD"/third_party/"$addon_package_name $SYSTEM_DIRECTORY"expressionengine/third_party/"
    echo "Package symlink created."
else
    echo "Package path does not exist."
fi

if [ -d "$THEMES_DIRECTORY" ]; then
    ln -sf $PWD"/themes/third_party/"$addon_package_name $THEMES_DIRECTORY"third_party/"
    echo "Package themes created."
else
    echo "Themes path does not exist."
fi
