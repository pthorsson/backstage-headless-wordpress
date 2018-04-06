#!/bin/sh

# Clean up
_HLWP_UNSET_VARIABLES()
{
    # Misc
    unset HLWP_CONFIG_STATUS
    unset HLWP_CONFIG_COMPLETE

    # Input data
    unset HLWP_WP_USER
    unset HLWP_WP_PASSWORD
    unset HLWP_WP_EMAIL
    unset HLWP_WP_DB_HOST
    unset HLWP_WP_DB_USER
    unset HLWP_WP_DB_USER_PASSWORD
    unset HLWP_WP_DB_NAME

    # Functions
    unset -f _HLWP_COLOR
    unset -f _HLWP_WP_DEFAULT
    unset -f _HLWP_LOG
    unset -f _HLWP_KILL_IF_ERROR
    unset -f _HLWP_CHECK_PREREQUISITES
    unset -f _HLWP_READ_CONFIG
    unset -f _HLWP_LIST_PLUGINS
    unset -f _HLWP_UNSET_VARIABLES
}

# Colors
_HLWP_COLOR()
{
    case $1 in
        black)       printf "\033[0;30m" ;;
        red)         printf "\033[0;31m" ;;
        green)       printf "\033[0;32m" ;;
        orange)      printf "\033[0;33m" ;;
        blue)        printf "\033[0;34m" ;;
        purple)      printf "\033[0;35m" ;;
        cyan)        printf "\033[0;36m" ;;
        lightgray)   printf "\033[0;37m" ;;
        darkgray)    printf "\033[1;30m" ;;
        lightred)    printf "\033[1;31m" ;;
        lightgreen)  printf "\033[1;32m" ;;
        yellow)      printf "\033[1;33m" ;;
        lightblue)   printf "\033[1;34m" ;;
        lightpurple) printf "\033[1;35m" ;;
        lightcyan)   printf "\033[1;36m" ;;
        white)       printf "\033[1;37m" ;;
        *)           printf "\033[0m" ;;
    esac
}

# Default config data
_HLWP_WP_DEFAULT()
{
    case $1 in
        user)           printf "my_wp_user" ;;
        password)       printf "thismightbeagoodpassword" ;;
        email)          printf "my_email@mywebsite.com" ;;
        dbhost)         printf "localhost" ;;
        dbuser)         printf "hlwp-test" ;;
        dbuserpassword) printf "qwerty" ;;
        dbname)         printf "hlwp-test" ;;
        themename)      printf "Postlight Headless WP Starter" ;;
        themedir)       printf "postlight-headless-wp" ;;
        wpdesc)         printf "Just another (headless) WordPress site" ;;
        *)              printf "undefined" ;;
    esac
}

# Log function
_HLWP_LOG()
{
    case $2 in
        error)   local COLOR=$(_HLWP_COLOR lightred) ;;
        warning) local COLOR=$(_HLWP_COLOR yellow) ;;
        success) local COLOR=$(_HLWP_COLOR lightgreen) ;;
        *)       local COLOR=$(_HLWP_COLOR lightcyan) ;;
    esac

    local MESSAGE=$1
    
    printf "${COLOR}[HLWP]$(_HLWP_COLOR) ${MESSAGE}\n"
}

# Error log function
_HLWP_KILL_IF_ERROR()
{
    if [ -s ./error.log ]; then
        _HLWP_LOG "$1 - check error.log for more information" error
        kill -INT $$
    fi
}

# Description
_HLWP_CHECK_PREREQUISITES()
{
    rm error.log
    echo "_HLWP_CHECK_PREREQUISITES goes here"
}

# Read config
_HLWP_READ_CONFIG()
{
    HLWP_CONFIG_STATUS=0;

    while [ $HLWP_CONFIG_STATUS == 0 ]
    do
        _HLWP_LOG "Installation config"

        echo

        printf "   $(_HLWP_COLOR white)WordPress config$(_HLWP_COLOR)\n"
        printf "    - $(_HLWP_COLOR white)Username:$(_HLWP_COLOR) $(_HLWP_COLOR darkgray)($(_HLWP_WP_DEFAULT user))$(_HLWP_COLOR) "
        read HLWP_WP_USER
        HLWP_WP_USER=${HLWP_WP_USER:-$(_HLWP_WP_DEFAULT user)}

        printf "    - $(_HLWP_COLOR white)Password:$(_HLWP_COLOR) $(_HLWP_COLOR darkgray)($(_HLWP_WP_DEFAULT password))$(_HLWP_COLOR) "
        read HLWP_WP_PASSWORD
        HLWP_WP_PASSWORD=${HLWP_WP_PASSWORD:-$(_HLWP_WP_DEFAULT password)}

        printf "    - $(_HLWP_COLOR white)Email:$(_HLWP_COLOR) $(_HLWP_COLOR darkgray)($(_HLWP_WP_DEFAULT email))$(_HLWP_COLOR) "
        read HLWP_WP_EMAIL
        HLWP_WP_EMAIL=${HLWP_WP_EMAIL:-$(_HLWP_WP_DEFAULT email)}

        echo

        printf "   $(_HLWP_COLOR white)MySQL database config$(_HLWP_COLOR)\n"
        printf "    - $(_HLWP_COLOR white)Database host:$(_HLWP_COLOR) $(_HLWP_COLOR darkgray)($(_HLWP_WP_DEFAULT dbhost))$(_HLWP_COLOR) "
        read HLWP_WP_DB_HOST
        HLWP_WP_DB_HOST=${HLWP_WP_DB_HOST:-$(_HLWP_WP_DEFAULT dbhost)}

        printf "    - $(_HLWP_COLOR white)Username:$(_HLWP_COLOR) $(_HLWP_COLOR darkgray)($(_HLWP_WP_DEFAULT dbuser))$(_HLWP_COLOR) "
        read HLWP_WP_DB_USER
        HLWP_WP_DB_USER=${HLWP_WP_DB_USER:-$(_HLWP_WP_DEFAULT dbuser)}

        printf "    - $(_HLWP_COLOR white)Password:$(_HLWP_COLOR) $(_HLWP_COLOR darkgray)($(_HLWP_WP_DEFAULT dbuserpassword))$(_HLWP_COLOR) "
        read HLWP_WP_DB_USER_PASSWORD
        HLWP_WP_DB_USER_PASSWORD=${HLWP_WP_DB_USER_PASSWORD:-$(_HLWP_WP_DEFAULT dbuserpassword)}

        printf "    - $(_HLWP_COLOR white)Database name:$(_HLWP_COLOR) $(_HLWP_COLOR darkgray)($(_HLWP_WP_DEFAULT dbname))$(_HLWP_COLOR) "
        read HLWP_WP_DB_NAME
        HLWP_WP_DB_NAME=${HLWP_WP_DB_NAME:-$(_HLWP_WP_DEFAULT dbname)}

        echo

        HLWP_CONFIG_CONFIRM_STATUS=0;

        while [ $HLWP_CONFIG_CONFIRM_STATUS == 0 ]
        do

            printf "   Is the data above correct? ($(_HLWP_COLOR lightgreen)y$(_HLWP_COLOR) = Yes, continue / $(_HLWP_COLOR yellow)n$(_HLWP_COLOR) = No, edit config / $(_HLWP_COLOR lightred)c$(_HLWP_COLOR) = Cancel installation) "
            read HLWP_CONFIG_COMPLETE

            case $HLWP_CONFIG_COMPLETE in
                "y")
                    HLWP_CONFIG_CONFIRM_STATUS=1
                    HLWP_CONFIG_STATUS=1
                    echo
                    ;;
                "n")
                    HLWP_CONFIG_CONFIRM_STATUS=1
                    echo
                    ;;
                "c")
                    echo
                    HLWP_CONFIG_CONFIRM_STATUS=1
                    _HLWP_LOG "Cancelling installation"
                    _HLWP_UNSET_VARIABLES
                    kill -INT $$
                    ;;
                *)
                    echo "   Invalid anwser"
                    ;;
                    
            esac

        done
        
    done
}

_HLWP_LIST_PLUGINS()
{
    local PLUGINS=""

    for plugin in "./wordpress/wp-content/plugins"/*
    do
        plugin=${plugin%*/}
        if [ ${plugin##*/} != "index.php" ]; then
            PLUGINS="$PLUGINS ${plugin##*/}"
        fi

        unset plugin
    done

    echo $PLUGINS
}
