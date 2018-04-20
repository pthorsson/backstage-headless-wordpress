<?php

/*
Plugin Name: HLWP REST API
Version: 0.1
Description: Adds new REST API endpoints.
Author: Patrik Thorsson
*/

/**
 * Filter data with seleted fields
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
function _hlwp_get_posts( WP_REST_Request $context, $type, $fields ) {

    // Type of request.
    $type = in_array( $type, array( 'posts', 'post', 'pages', 'page' ) ) ? $type : 'posts';

    // Establishes post type.
    $post_type = in_array( $type, array( 'posts', 'post' ) ) ? 'post' : 'page';

    // Determines if to load one post or multiple.
    $load_one = in_array( $type, array( 'post', 'page' ) );

    // Determines if to filter fields or not.
    $load_full_post = in_array( $context['full'], array( 'true', '1', 'yes' ) );

    // Instantiates Wordpress REST posts controller.
    $posts_controller = new WP_REST_Posts_Controller( 'post' );

    // Container for filtered posts.
    $filtered_posts = array();

    // Fields to keep in filter.
    $fields = array( 'acf', 'slug', 'tags' );

    // Query to use in get_posts.
    $query = array(
        'post_type'	=> $post_type
    );

    // Adds name selector to quest if single page/post.
    if ( $load_one ) {
        $query['name'] = $context['name'];
    }
    
    // Get posts.
    $posts = get_posts( $query );

    // Respond with an 404/empty array if nothing was found.
    if ( empty( $posts ) ) {
        return $load_one ? new WP_Error(
            'Not found',
            $post_type . ' "' . $context['name'] . '" does not exist',
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
 * Get post
 */
function hlwp_get_post( WP_REST_Request $context ) {
    $fields = array( 'acf', 'slug', 'tags' );

    return _hlwp_get_posts($context, 'post', $fields);
}

/**
 * Get posts
 */
function hlwp_get_posts( WP_REST_Request $context ) {
    $fields = array( 'acf', 'slug', 'tags' );

    return _hlwp_get_posts($context, 'posts', $fields);
}

/**
 * Get page
 */
function hlwp_get_page( WP_REST_Request $context ) {
    $fields = array( 'acf', 'slug', 'tags' );

    return _hlwp_get_posts($context, 'page', $fields);
}

/**
 * Get pages
 */
function hlwp_get_pages( WP_REST_Request $context ) {
    $fields = array( 'acf', 'slug', 'tags' );

    return _hlwp_get_posts($context, 'pages', $fields);
}

/**
 * Add actions to wordpress
 */
add_action( 'rest_api_init', function() {

    $hlwp_namespace = 'hlwp/v1';
    
    $response_full = array_merge( array(), array( 'description' => 'Will return the non-stripped version of the data if set to true.' ) );
    $response_all = array_merge( array(), array( 'description' => 'Will return all items available if set to true.' ) );

    // Register routes

    register_rest_route( $hlwp_namespace, '/post/(?P<name>.+)', array(
        'methods'  => 'GET',
        'callback' => 'hlwp_get_post',
        'args' => array(
            'full' => array_merge( $response_full, array( 'required' => false ) )
        )
    ) );

    register_rest_route( $hlwp_namespace, '/posts', array(
        'methods'  => 'GET',
        'callback' => 'hlwp_get_posts',
        'args' => array(
            'full' => array_merge( $response_full, array( 'required' => false ) ),
            'all' => array_merge( $response_full, array( 'required' => false ) )
        )
    ) );

    register_rest_route( $hlwp_namespace, '/page/(?P<name>.+)', array(
        'methods'  => 'GET',
        'callback' => 'hlwp_get_page',
        'args' => array(
            'full' => array_merge( $response_full, array( 'required' => false ) )
        )
    ) );

    register_rest_route( $hlwp_namespace, '/pages', array(
        'methods'  => 'GET',
        'callback' => 'hlwp_get_pages',
        'args' => array(
            'full' => array_merge( $response_full, array( 'required' => false ) ),
            'all' => array_merge( $response_full, array( 'required' => false ) )
        )
    ) );

});