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
_HLWP_WP_CLI "core download --version=4.9.4 --locale=en_US --force" 2> error.log
_HLWP_KILL_IF_ERROR "Could not download WordPress"

# WP-CLI - Creating wp-config file
_HLWP_LOG "Creating wp-config ..."
_HLWP_WP_CLI "core config --dbname='$HLWP_WP_DB_NAME' --dbuser='$HLWP_WP_DB_USER' --dbpass='$HLWP_WP_DB_USER_PASSWORD' --dbhost='$HLWP_WP_DB_HOST'" 2> error.log
_HLWP_KILL_IF_ERROR "Could not create configuration for WordPress"

# WP-CLI - Ensuring clean database
_HLWP_LOG "Ensuring clean database ..."
_HLWP_WP_CLI "db drop --yes" 2> error.log
_HLWP_KILL_IF_ERROR "Could not drop the current database"
_HLWP_WP_CLI "db create" 2> error.log
_HLWP_KILL_IF_ERROR "Could not create a new database"

# WP-CLI - Install WordPress
_HLWP_LOG "Running WordPress installation process ..."
_HLWP_WP_CLI "core install --url='http://localhost:9000' --title='$(_HLWP_WP_DEFAULT themename)' --admin_user='$HLWP_WP_USER' --admin_password='$HLWP_WP_PASSWORD' --admin_email='$HLWP_WP_EMAIL' --skip-email"  2> error.log
_HLWP_KILL_IF_ERROR "Could complete WordPress installation"

# BUG: If the path to the wordpress installation contains a ".",
#      the siteurl and home will become corrupted. Though it works
#      if we use the wp-cli "option update" on siteurl and home.

# WP-CLI - Sets siteurl and home again
_HLWP_WP_CLI "option update home 'http://localhost:9000'"
_HLWP_WP_CLI "option update siteurl 'http://localhost:9000'"

# WP-CLI - Delete unnecessary files
_HLWP_LOG "Deleting unnecessary files ..."
_HLWP_WP_CLI "theme activate '$(_HLWP_WP_DEFAULT themedir)'"
_HLWP_WP_CLI "theme delete twentyfourteen twentyfifteen twentysixteen twentyseventeen"
_HLWP_WP_CLI "plugin delete akismet hello"

# WP-CLI - Activating plugins
_HLWP_LOG "Activating plugins ..."
_HLWP_WP_CLI "plugin activate $(_HLWP_LIST_PLUGINS)" 2> error.log
_HLWP_KILL_IF_ERROR "Could not activate plugins"

# WP-CLI - Sync ACF
_HLWP_LOG "Sync ACF ..."
_HLWP_WP_CLI "acf sync" 2> error.log
_HLWP_KILL_IF_ERROR "Could not sync ACF"

# WP-CLI - Rewrite structure
_HLWP_LOG "Rewrite structure ..."
_HLWP_WP_CLI "rewrite structure '/%year%/%monthnum%/%day%/%postname%/'" 2> error.log
_HLWP_KILL_IF_ERROR "Could not edit rewrite structure"

# WP-CLI - Adding some test data ...
_HLWP_LOG "Adding some test data ..."
_HLWP_WP_CLI "option update blogdescription '$(_HLWP_WP_DEFAULT wpdesc)'"
_HLWP_WP_CLI "post update 1 wordpress/wp-content/themes/postlight-headless-wp/post-content/sample-post.txt --post_title='Sample Post' --post_name=sample-post"
_HLWP_WP_CLI "post create wordpress/wp-content/themes/postlight-headless-wp/post-content/welcome.txt --post_type=page --post_status=publish --post_name=welcome --post_title='Congratulations!'"
_HLWP_WP_CLI "term update category 1 --name='Sample Category'"
_HLWP_WP_CLI "menu create 'Header Menu'"
_HLWP_WP_CLI "menu item add-post header-menu 1"
_HLWP_WP_CLI "menu item add-post header-menu 2"
_HLWP_WP_CLI "menu item add-term header-menu category 1"
_HLWP_WP_CLI "menu item add-custom header-menu 'Read about the Starter Kit on Medium' https://trackchanges.postlight.com/introducing-postlights-wordpress-react-starter-kit-a61e2633c48c"
_HLWP_WP_CLI "menu location assign header-menu header-menu"

# Installation complete
_HLWP_LOG "Installation is now complete!" success
_HLWP_LOG "Quickstart:\n   1. Run serve.sh\n   2. Go to localhost:8080/wp-admin\n   3. Login with $(_HLWP_COLOR white)$HLWP_WP_USER$(_HLWP_COLOR) / $(_HLWP_COLOR white)$HLWP_WP_PASSWORD$(_HLWP_COLOR)" success

# Clean up the variables
_HLWP_UNSET_VARIABLES
