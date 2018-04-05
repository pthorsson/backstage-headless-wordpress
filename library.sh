#!/bin/sh

# Colors
HLWP_COLOR_BLUE='\033[0;34m'
HLWP_COLOR_YELLOW='\033[0;33m'
HLWP_COLOR_NONE='\033[0m'

# Default config
HLWP_WP_USER_DEFAULT='my_wp_user'
HLWP_WP_PASSWORD_DEFAULT='thismightbeagoodpassword'
HLWP_WP_EMAIL_DEFAULT='my_email@mywebsite.com'
HLWP_WP_THEME_DIR_DEFAULT='postlight-headless-wp'
HLWP_WP_THEME_NAME_DEFAULT='Postlight Headless WP Starter'
HLWP_WP_DESC_DEFAULT='Just another (headless) WordPress site'
HLWP_WP_DB_NAME_DEFAULT='wp_headless'

# Log function
_HLWP_LOG()
{
    local HLWP_MESSAGE=$1
    printf "${HLWP_COLOR_BLUE}[HLWP]${HLWP_COLOR_NONE} ${HLWP_MESSAGE}\n"
}

# Read config
_HLWP_READ_CONFIG()
{
    _HLWP_LOG "Installation config:"

    echo

    printf "   ${HLWP_COLOR_YELLOW}Username:${HLWP_COLOR_NONE} "
    read -e -i "$HLWP_WP_USER_DEFAULT" HLWP_WP_USER

    printf "   ${HLWP_COLOR_YELLOW}Password:${HLWP_COLOR_NONE} "
    read -e -i "$HLWP_WP_PASSWORD_DEFAULT" HLWP_WP_PASSWORD

    printf "   ${HLWP_COLOR_YELLOW}Email:${HLWP_COLOR_NONE} "
    read -e -i "$HLWP_WP_EMAIL_DEFAULT" HLWP_WP_EMAIL

    printf "   ${HLWP_COLOR_YELLOW}Theme directory:${HLWP_COLOR_NONE} "
    read -e -i "$HLWP_WP_THEME_DIR_DEFAULT" HLWP_WP_THEME_DIR

    printf "   ${HLWP_COLOR_YELLOW}Theme name:${HLWP_COLOR_NONE} "
    read -e -i "$HLWP_WP_THEME_NAME_DEFAULT" HLWP_WP_THEME_NAME

    printf "   ${HLWP_COLOR_YELLOW}Description:${HLWP_COLOR_NONE} "
    read -e -i "$HLWP_WP_DESC_DEFAULT" HLWP_WP_DESC

    printf "   ${HLWP_COLOR_YELLOW}Database name:${HLWP_COLOR_NONE} "
    read -e -i "$HLWP_WP_DB_NAME_DEFAULT" HLWP_WP_DB_NAME

    echo
}

# Clean up
_HLWP_UNSET_VARIABLES()
{
    # Misc
    unset HLWP_COLOR_BLUE
    unset HLWP_COLOR_YELLOW
    unset HLWP_COLOR_NONE

    # Default data
    unset HLWP_WP_USER_DEFAULT
    unset HLWP_WP_PASSWORD_DEFAULT
    unset HLWP_WP_EMAIL_DEFAULT
    unset HLWP_WP_THEME_DIR_DEFAULT
    unset HLWP_WP_THEME_NAME_DEFAULT
    unset HLWP_WP_DESC_DEFAULT
    unset HLWP_WP_DB_NAME_DEFAULT

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
