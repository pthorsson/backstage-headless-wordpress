<?php

function backstage_admin_page_endpoints() {
    if ( !current_user_can( 'manage_options' ) )  {
	    wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }

    global $backstage;

    $backstage->enqueue_scripts( array( 'backstage-endpoints' ) );
    $backstage->enqueue_styles( array( 'backstage-endpoints' ) );

    require_once( 'views/html_endpoints.php' );
}
