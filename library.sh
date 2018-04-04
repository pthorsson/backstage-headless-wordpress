#!/bin/sh

# Colors
COLOR_BLUE='\033[0;34m'
COLOR_YELLOW='\033[0;33m'
NOCOLOR='\033[0m'

# Log function
_HLWP_LOG()
{
    local MESSAGE=$1
    printf "${COLOR_BLUE}[HLWP]${NOCOLOR} ${MESSAGE}\n"
}

# Default config
HLWP_DEFAULT_USER='my_wp_user'
HLWP_DEFAULT_PASSWORD='thismightbeagoodpassword'
HLWP_DEFAULT_EMAIL='my_email@mywebsite.com'
HLWP_DEFAULT_THEME_DIR='postlight-headless-wp'
HLWP_DEFAULT_THEME_NAME='Postlight Headless WP Starter'
HLWP_DEFAULT_DESC='Just another (headless) WordPress site'
HLWP_DEFAULT_DB_NAME='wp_headless'

# Read config
_HLWP_READ_CONFIG()
{
    _HLWP_LOG "Installation config:"

    echo

    printf "   ${COLOR_YELLOW}Username:${NOCOLOR} "
    read -e -i "$HLWP_DEFAULT_USER" HLWP_WP_USER

    printf "   ${COLOR_YELLOW}Password:${NOCOLOR} "
    read -e -i "$HLWP_DEFAULT_PASSWORD" HLWP_WP_PASSWORD

    printf "   ${COLOR_YELLOW}Email:${NOCOLOR} "
    read -e -i "$HLWP_DEFAULT_EMAIL" HLWP_WP_EMAIL

    printf "   ${COLOR_YELLOW}Theme directory:${NOCOLOR} "
    read -e -i "$HLWP_DEFAULT_THEME_DIR" HLWP_WP_THEME_DIR

    printf "   ${COLOR_YELLOW}Theme name:${NOCOLOR} "
    read -e -i "$HLWP_DEFAULT_THEME_NAME" HLWP_WP_THEME_NAME

    printf "   ${COLOR_YELLOW}Description:${NOCOLOR} "
    read -e -i "$HLWP_DEFAULT_DESC" HLWP_WP_DESC

    printf "   ${COLOR_YELLOW}Database name:${NOCOLOR} "
    read -e -i "$HLWP_DEFAULT_DB_NAME" HLWP_WP_DB_NAME

    echo
}

# Clean up
_HLWP_UNSET_VARIABLES()
{
    # Misc
    unset COLOR_BLUE
    unset COLOR_YELLOW
    unset NOCOLOR

    # Default data
    unset HLWP_DEFAULT_USER
    unset HLWP_DEFAULT_PASSWORD
    unset HLWP_DEFAULT_EMAIL
    unset HLWP_DEFAULT_THEME_DIR
    unset HLWP_DEFAULT_THEME_NAME
    unset HLWP_DEFAULT_DESC
    unset HLWP_DEFAULT_DB_NAME

    # Input data
    unset HLWP_WP_USER
    unset HLWP_WP_PASSWORD
    unset HLWP_WP_EMAIL
    unset HLWP_WP_THEME_DIR
    unset HLWP_WP_THEME_NAME
    unset HLWP_WP_DESC
    unset HLWP_WP_DB_NAME

    # Functions
    unset -f _HLWP_LOG
    unset -f _HLWP_READ_CONFIG
    unset -f _HLWP_UNSET_VARIABLES
}