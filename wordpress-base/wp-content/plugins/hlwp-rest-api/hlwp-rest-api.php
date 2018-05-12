<?php

/*
Plugin Name: HLWP REST API
Version: 0.1
Description: Adds new REST API endpoints.
Author: Patrik Thorsson
*/

/**
 * Filter data with seleted fields.
 */
function _hlwp_filter_post($fields, $post) {
    $filtered_post = array();

    // Adds property to filtered data if fields contains key.
    foreach ( $post as $key => $value ) {
        if ( in_array( $key, $fields ) ) {
            $filtered_post[$key] = $value;
        }
    }

    return $filtered_post;
}

/**
 * Method for loading posts.
 */
function hlwp_get_content( WP_REST_Request $context ) {

    // Get content data.
    $url_params = $context->get_url_params();
    $query_params = $context->get_query_params();

    // Post preferences.
    $post_type = $url_params['type'];
    $post_name = $url_params['name'];

    // Check if user is logged in
    global $hlwp_is_user_logged_in;

    // Get available post types.
    $available_post_types = get_post_types( array( 'public' => true ) );

    // Respond with an 404/empty array if invalid post type.
    if ( !$available_post_types[$post_type] ) {
        return new WP_Error(
            'Not found',
            'Post type "' . $post_type . '" does not exist',
            array( 'status' => 404 )
        );
    }

    // Determines if load multiple or one.
    $load_one = $url_params['name'] !== null;

    // Determines if to filter fields or not.
    $load_full_post = in_array( $query_params['full'], array( 'true', '1', 'yes' ) );

    // Instantiates Wordpress REST posts controller.
    $posts_controller = new WP_REST_Posts_Controller( 'post' );

    // Container for filtered posts.
    $filtered_posts = array();

    // Fields to keep in filter.
    if ( $query_params['fields'] ) {
        $fields = explode(' ', $query_params['fields']);
    } else {
        $fields = array( 'acf', 'slug', 'tags', 'status', 'content' );
    }

    // Query to use in get_posts.
    $query = array(
        'post_type'	=> $post_type
    );

    // Adds name selector to quest if single page/post.
    if ( $load_one ) {
        $query['name'] = $url_params['name'];
    }

    // Return all posts regardless of status is logged in.
    if ( $hlwp_is_user_logged_in ) {
        $query['post_status'] = 'any';
    }
    
    // Get posts.
    $posts = get_posts( $query );

    // Respond with an 404/empty array if nothing was found.
    if ( empty( $posts ) ) {
        return $load_one ? new WP_Error(
            'Not found',
            $post_type . ' "' . $url_params['name'] . '" does not exist',
            array( 'status' => 404 )
        ) : array();
    }

    // Run posts through wp REST posts controller and then filters fields.
    for ( $i = 0; $i < count($posts); $i++ ) { 
        $data = $posts_controller->prepare_item_for_response( $posts[$i], $request );
        $post = $posts_controller->prepare_response_for_collection( $data );

        if ( $load_full_post ) {
            array_push( $filtered_posts, $post );
        } else {
            array_push( $filtered_posts, _hlwp_filter_post( $fields, $post ) );
        }
    }

    return new WP_REST_Response( $load_one ? $filtered_posts[0] : $filtered_posts );
}

/**
 * Add actions to wordpress.
 */
add_action( 'rest_api_init', function() {

    $hlwp_namespace = 'hlwp/v1';

    $response_fields = array_merge( array(), array( 'description' => 'Will return selected fields.' ) );
    $response_full = array_merge( array(), array( 'description' => 'Will return non-filtered data.' ) );
    $response_all = array_merge( array(), array( 'description' => 'Will return all items available if set to true.' ) );

    // Register routes

    /**
     * Universal route for loading one or multiple posts by type.
     */
    register_rest_route( $hlwp_namespace, '/content/(?P<type>[a-zA-Z0-9_-]+)(?:/(?P<name>[a-zA-Z0-9_\-=\(\)\[\]$.*+,;!~]+))?', array(
        'methods'  => 'GET',
        'callback' => 'hlwp_get_content',
        'args' => array(
            'fields' => array_merge( $response_fields, array( 'required' => false ) ),
            'full' => array_merge( $response_full, array( 'required' => false ) ),
            'all' => array_merge( $response_full, array( 'required' => false ) )
        )
    ) );

});

/**
 * An ugly fix for accessing is_user_logged_in() in endpoint.
 */
add_action( 'rest_api_init', function() {
    global $hlwp_is_user_logged_in;
    $hlwp_is_user_logged_in = is_user_logged_in();
});