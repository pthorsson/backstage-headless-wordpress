while test $# -gt 0; do
    case "$1" in
        --name*)
            _HLWP_PLUGIN=`echo $1 | sed -e 's/^[^=]*=//g'`
            shift
            ;;
        *)
            break
            ;;
    esac
done

if [ -d "./wordpress-base/wp-content/plugins/$_HLWP_PLUGIN" ]; then
    cp -ar "./wordpress-base/wp-content/plugins/$_HLWP_PLUGIN" "./wordpress/wp-content/plugins/"
    php wp-cli.phar --path="wordpress/" plugin deactivate $_HLWP_PLUGIN
    php wp-cli.phar --path="wordpress/" plugin activate $_HLWP_PLUGIN
else
    echo "Plugin '$_HLWP_PLUGIN' does not exist"
fi

unset _HLWP_PLUGIN