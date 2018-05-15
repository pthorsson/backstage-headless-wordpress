<?php

/**
 * Add a widget to the dashboard.
 *
 * This function is hooked into the 'wp_dashboard_setup' action below.
 */
function hlwp_preview_link_widget() {
	wp_add_dashboard_widget(
        'hlwp_preview_link',                 // Widget slug.
        'Backstage HLWP - Content preview',  // Title.
        'hlwp_preview_link_function'         // Display function.
    );	
}

add_action( 'wp_dashboard_setup', 'hlwp_preview_link_widget' );

/**
 * Create the function to output the contents of our Dashboard Widget.
 */
function hlwp_preview_link_function() {
    $restNonce = wp_create_nonce( 'wp_rest' );

    ?>
        <p>
            You can open your front end application in preview mode allowing it to fetch unpublished content from Backstage.
        </p>
        <div style="padding-top: 10px; float: right; height: 30px;">
            <a href="http://localhost:9001?_wpnonce=<?php echo $restNonce ?>" target="_blank" class="button button-primary">Open preview mode</a>
        </div>
        <div style="padding-top: 15px; height: 25px; overflow: hidden;">
            Token: <code><?php echo $restNonce ?></code>
        </div>
    <?php
}