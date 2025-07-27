#!/usr/bin/env bash

# Rename the plugin from WP Plugin Template to New Plugin Name (first argument)
NEW_NAME="$1"
if [ -z "$NEW_NAME" ]; then
	echo "Please provide a new plugin name."
	exit 1
fi

OLD_NAME="WP Plugin Template"
OLD_CAMEL_CASE_NAME="WpPluginTemplate"
OLD_KEBAB_CASE_NAME="wp-plugin-template"

# Convert the new plugin name to Camel Case.
CAMEL_CASE_NAME=$(echo "$NEW_NAME" | awk '{for(i=1;i<=NF;i++){if(i==1){printf "%s", $i}else{printf "%s%s", toupper(substr($i,1,1)), substr($i,2)}}print ""}')

# Kebab case name
KEBAB_CASE_NAME=$(echo "$NEW_NAME" | tr '[:upper:]' '[:lower:]' | tr ' ' '-')

echo "Renaming plugin to: $CAMEL_CASE_NAME"
echo "Using kebab case: $KEBAB_CASE_NAME"

# Search all files for the old plugin name and replace it with the new one
sed -i '' "s/${OLD_NAME}/${NEW_NAME}/g" *.* inc/*.* tests/*.*
# Camel case replacements
sed -i '' "s/${OLD_CAMEL_CASE_NAME}/${CAMEL_CASE_NAME}/g" *.* inc/*.* tests/*.*
# Kebab case replacements
sed -i '' "s/${OLD_KEBAB_CASE_NAME}/${KEBAB_CASE_NAME}/g" *.* inc/*.* tests/*.*

# Replace the plugin description in the main file with the new name
# Rename the main plugin file
MAIN_PLUGIN_FILE="wp-plugin-template.php"
sed -i '' "s/^ \* Description:.*$/ \* Description: $NEW_NAME/g" "$MAIN_PLUGIN_FILE"
mv "$MAIN_PLUGIN_FILE" "${KEBAB_CASE_NAME}.php"

echo "Plugin renamed to {$NEW_NAME} with Camel Case: $CAMEL_CASE_NAME and Kebab Case: $KEBAB_CASE_NAME"
echo "The rename script can be removed after renaming."
