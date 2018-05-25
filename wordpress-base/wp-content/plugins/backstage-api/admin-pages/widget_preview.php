<?php

/**
 * Add a widget to the dashboard.
 *
 * This function is hooked into the 'wp_dashboard_setup' action below.
 */
function backstage_admin_widget_preview() {
	wp_add_dashboard_widget(
        'backstage_preview_widget',               // Widget slug.
        'Backstage API - Preview mode',           // Title.
        'backstage_admin_widget_preview_function' // Display function.
    );
}

add_action( 'wp_dashboard_setup', 'backstage_admin_widget_preview' );

/**
 * Create the function to output the contents of our Dashboard Widget.
 */
function backstage_admin_widget_preview_function() {
    global $backstage_config;

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