#!/bin/sh
. ./library.sh

# Check that the needed dependencies are
_HLWP_CHECK_PREREQUISITES

# Display and input for setup config
_HLWP_READ_CONFIG

# New install file
_HLWP_LOG "Installing WordPress headless"

# Removing existing WordPress
_HLWP_LOG "Removing existing WordPress ..."
rm -rf wordpress/

# Cloning WordPress base
_HLWP_LOG "Cloning WordPress base ..."
cp -ar wordpress-base/ wordpress/

# Download WP-CLI
_HLWP_LOG "Downloading local WP-CLI ..."
curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar

# WP-CLI - Download WordPress
_HLWP_LOG "Downloading WordPress version 4.9.4 ..."
php wp-cli.phar core download --version=4.9.4 --locale=en_US --force 2> error.log
_HLWP_KILL_IF_ERROR "Could not download WordPress"

# WP-CLI - Creating wp-config file
_HLWP_LOG "Creating wp-config ..."
php wp-cli.phar core config --dbname="$HLWP_WP_DB_NAME" --dbuser="$HLWP_WP_DB_USER" --dbpass="$HLWP_WP_DB_USER_PASSWORD" --dbhost="$HLWP_WP_DB_HOST" 2> error.log
_HLWP_KILL_IF_ERROR "Could not create configuration for WordPress"

# WP-CLI - Ensuring clean database
_HLWP_LOG "Ensuring clean database ..."
php wp-cli.phar db drop --yes 2> error.log
_HLWP_KILL_IF_ERROR "Could not drop the current database"
php wp-cli.phar db create 2> error.log
_HLWP_KILL_IF_ERROR "Could not create a new database"

# WP-CLI - Install WordPress
_HLWP_LOG "Running WordPress installation process ..."
php wp-cli.phar core install --url=localhost:8080 --title="$(_HLWP_WP_DEFAULT themename)" --admin_user="$HLWP_WP_USER" --admin_password="$HLWP_WP_PASSWORD" --admin_email="$HLWP_WP_EMAIL" --skip-email  2> error.log
_HLWP_KILL_IF_ERROR "Could complete WordPress installation"

# WP-CLI - Delete unnecessary files
_HLWP_LOG "Deleting unnecessary files ..."
php wp-cli.phar theme activate "$(_HLWP_WP_DEFAULT themedir)"
php wp-cli.phar theme delete twentyfourteen
php wp-cli.phar theme delete twentyfifteen
php wp-cli.phar theme delete twentysixteen
php wp-cli.phar theme delete twentyseventeen
php wp-cli.phar plugin delete akismet
php wp-cli.phar plugin delete hello

# WP-CLI - Activating plugins
_HLWP_LOG "Activating plugins ..."
php wp-cli.phar plugin activate $(_HLWP_LIST_PLUGINS) 2> error.log
_HLWP_KILL_IF_ERROR "Could not activate plugins"

# WP-CLI - Sync ACF
_HLWP_LOG "Sync ACF ..."
php wp-cli.phar acf sync 2> error.log
_HLWP_KILL_IF_ERROR "Could not sync ACF"

# WP-CLI - Rewrite structure
_HLWP_LOG "Rewrite structure ..."
php wp-cli.phar rewrite structure "/%year%/%monthnum%/%day%/%postname%/" 2> error.log
_HLWP_KILL_IF_ERROR "Could not edit rewrite structure"

# WP-CLI - Adding some test data ...
_HLWP_LOG "Adding some test data ..."
php wp-cli.phar option update blogdescription "$(_HLWP_WP_DEFAULT wpdesc)"
php wp-cli.phar post update 1 wordpress/wp-content/themes/postlight-headless-wp/post-content/sample-post.txt --post_title="Sample Post" --post_name=sample-post
php wp-cli.phar post create wordpress/wp-content/themes/postlight-headless-wp/post-content/welcome.txt --post_type=page --post_status=publish --post_name=welcome --post_title="Congratulations!"
php wp-cli.phar term update category 1 --name="Sample Category"
php wp-cli.phar menu create "Header Menu"
php wp-cli.phar menu item add-post header-menu 1
php wp-cli.phar menu item add-post header-menu 2
php wp-cli.phar menu item add-term header-menu category 1
php wp-cli.phar menu item add-custom header-menu "Read about the Starter Kit on Medium" https://trackchanges.postlight.com/introducing-postlights-wordpress-react-starter-kit-a61e2633c48c
php wp-cli.phar menu location assign header-menu header-menu

# Installation complete
_HLWP_LOG "Installation is now complete! You can now start your webserver and login." success

# Clean up the variables
_HLWP_UNSET_VARIABLES
