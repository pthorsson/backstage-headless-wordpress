<?php

function backstage_admin_page_cors() {
    if ( !current_user_can( 'manage_options' ) )  {
	    wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }

    wp_enqueue_script( 'backstage-lib' );
    wp_enqueue_script( 'backstage-cors' );

    wp_enqueue_style( 'backstage-cors' );

    require_once( 'views/html_cors.php' );
}
