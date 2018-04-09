<?php

/*
Plugin Name: HLWP REST API
Version: 0.1
Description: Adds new REST API endpoints.
Author: Patrik Thorsson
*/

function _hlwp_filter_data($fields, $data) {
    $filteredData = array();

    foreach ( $data as $key => $value ) {
        if ( in_array( $key, $fields ) ) {
            $filteredData[$key] = $value;
        }
    }

    return $filteredData;
}

/**
 * Get posts
 */
function hlwp_get_posts( WP_REST_Request $context ) {
    $controller = new WP_REST_Posts_Controller( 'post' );    
    $fields = array( 'acf', 'slug', 'tags' );
    $filteredPosts = array();
    $posts = get_posts(array(
        'posts_per_page' => in_array( $context['all'], array( 'true', '1', 'yes' ) ) ? -1 : 5,
        'post_type'	=> 'post'
    ));

    if ( empty( $posts ) ) {
		return new WP_REST_Response( array() );
    }

    for ( $i = 0; $i < count($posts); $i++ ) { 
        $data = $controller->prepare_item_for_response( $posts[$i], $request );
        $post = $controller->prepare_response_for_collection( $data );
        
        if ( in_array( $context['full'], array( 'true', '1', 'yes' ) ) ) {
            array_push( $filteredPosts, $post );
        } else {
            array_push( $filteredPosts, _hlwp_filter_data( $fields, $post ) );
        }
    }
    
    return new WP_REST_Response( $filteredPosts );
}

/**
 * Get post
 */
function hlwp_get_post( WP_REST_Request $context ) {
    $controller = new WP_REST_Posts_Controller( 'post' );
    $fields = array( 'acf', 'slug', 'tags' );
    $filteredPosts = array();
    $post = get_posts( array(
        'name' => $context['post'],
        'post_type'	=> 'post'
    ) );

    if ( empty( $post ) ) {
		return new WP_Error(
			'Not found',
			$context['post'] . ' does not exist',
			array( 'status' => 404 )
		);
    }

    $data = $controller->prepare_item_for_response( $post[0], $request );
    $post = $controller->prepare_response_for_collection( $data );
    
    return _hlwp_filter_data( $fields, $post );
}

add_action( 'rest_api_init', function () {

    $hlwp_namespace = 'hlwp/v1';
    
    $response_full = array_merge( array(), array( 'description' => 'Will return the non-stripped version of the data if set to true.' ) );
    $response_all = array_merge( array(), array( 'description' => 'Will return all items available if set to true.' ) );

	// Register routes
	register_rest_route( $hlwp_namespace, '/posts', array(
		'methods'  => 'GET',
        'callback' => 'hlwp_get_posts',
        'args' => array(
			'full' => array_merge( $response_full, array( 'required' => false ) ),
			'all' => array_merge( $response_full, array( 'required' => false ) )
		)
    ) );
    
    register_rest_route( $hlwp_namespace, '/post/(?P<post>.+)', array(
		'methods'  => 'GET',
        'callback' => 'hlwp_get_post',
        'args' => array(
			'full' => array_merge( $response_full, array( 'required' => false ) )
		)
    ) );
    
    // register_rest_route( $hlwp_namespace, '/page/(?P<page>.+)', array(
	// 	'methods'  => 'GET',
	// 	'callback' => 'rest_get_post'
	// ) );

	// register_rest_route( $hlwp_namespace, '/page', array(
	// 	'methods'  => 'GET',
	// 	'callback' => 'rest_get_page',
	// 	'args' => array(
	// 		'slug' => array_merge( $page_slug_arg, array( 'required' => true ) ),
	// 	)
	// ) );

	// register_rest_route($hlwp_namespace, '/post/preview', array(
	// 	'methods'  => 'GET',
	// 	'callback' => 'rest_get_post_preview',
	// 	'args' => array(
	// 		'id' => array(
	// 			'validate_callback' => function($param, $request, $key) {
	// 				return ( is_numeric( $param ) );
	// 			},
	// 			'required' => true,
	// 			'description' => 'Valid WordPress post ID',
	// 		),
	// 	),
	// 	'permission_callback' => function() {
	// 		return current_user_can( 'edit_posts' );
	// 	}
	// ) );
});