#!/bin/sh
. ./library.sh

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
php wp-cli.phar core download --version=4.9.4 --locale=en_US --force

# Clean up the variables
_HLWP_UNSET_VARIABLES
