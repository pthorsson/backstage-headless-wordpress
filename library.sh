#!/bin/sh

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
        *)           printf "\033[0m";;
    esac
}

# Colors
_HLWP_WP_DEFAULT()
{
    case $1 in
        user)     printf "my_wp_user" ;;
        password) printf "thismightbeagoodpassword" ;;
        email)    printf "my_email@mywebsite.com" ;;
        dbname)   printf "wp_headless" ;;
        *)        printf "undefined" ;;
    esac
}

# Log function
_HLWP_LOG()
{
    local MESSAGE=$1
    printf "$(_HLWP_COLOR lightcyan)[HLWP]$(_HLWP_COLOR) ${MESSAGE}\n"
}

# Description
_HLWP_CHECK_PREREQUISITES()
{

}

# Read config
_HLWP_READ_CONFIG()
{
    HLWP_CONFIG_STATUS=0;

    while [ $HLWP_CONFIG_STATUS == 0 ]
    do
        _HLWP_LOG "Installation config:"

        echo

        printf "   $(_HLWP_COLOR white)Username:$(_HLWP_COLOR) $(_HLWP_COLOR darkgray)($(_HLWP_WP_DEFAULT user))$(_HLWP_COLOR) "
        read HLWP_WP_USER

        printf "   $(_HLWP_COLOR white)Password:$(_HLWP_COLOR) $(_HLWP_COLOR darkgray)($(_HLWP_WP_DEFAULT password))$(_HLWP_COLOR) "
        read HLWP_WP_PASSWORD

        printf "   $(_HLWP_COLOR white)Email:$(_HLWP_COLOR) $(_HLWP_COLOR darkgray)($(_HLWP_WP_DEFAULT email))$(_HLWP_COLOR) "
        read HLWP_WP_EMAIL

        printf "   $(_HLWP_COLOR white)Database name:$(_HLWP_COLOR) $(_HLWP_COLOR darkgray)($(_HLWP_WP_DEFAULT dbname))$(_HLWP_COLOR) "
        read HLWP_WP_DB_NAME

        echo

        printf "   Is the data above correct? ($(_HLWP_COLOR lightgreen)y$(_HLWP_COLOR) = Yes, continue / $(_HLWP_COLOR yellow)n$(_HLWP_COLOR) = No, edit config / $(_HLWP_COLOR lightred)c$(_HLWP_COLOR) = Cancel installation) "
        read HLWP_CONFIG_COMPLETE

        case $HLWP_CONFIG_COMPLETE in
            "y")
                HLWP_CONFIG_STATUS=1
                echo
                ;;
            "n")
                echo
                ;;
            *)
                echo
                _HLWP_LOG "Cancelling installation"
                _HLWP_UNSET_VARIABLES
                kill -INT $$
                ;;
        esac
        
    done
}

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
    unset HLWP_WP_THEME_DIR
    unset HLWP_WP_THEME_NAME
    unset HLWP_WP_DESC
    unset HLWP_WP_DB_NAME

    # Functions
    unset -f _HLWP_COLOR
    unset -f _HLWP_WP_DEFAULT
    unset -f _HLWP_LOG
    unset -f _HLWP_CHECK_PREREQUISITES
    unset -f _HLWP_READ_CONFIG
    unset -f _HLWP_UNSET_VARIABLES
}
