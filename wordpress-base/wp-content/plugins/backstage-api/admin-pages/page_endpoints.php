<?php

function backstage_admin_page_endpoints() {
    if ( !current_user_can( 'manage_options' ) )  {
	    wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }

    global $backstage;

    $json_data = $backstage->load_config('endpoints');

    $json_data = htmlspecialchars(json_encode($json_data), ENT_QUOTES, 'UTF-8');

    require_once( 'views/html_endpoints.php' );
}
