<?php

if( ! class_exists('BackstageEndpoints') ) :

class BackstageEndpoints {

    private $namespace = 'bs/v1';
    private $args = array();

    function __construct() {
        // Do nothing here
    }

    public function init() {
        
        // Endpoints arguments
        $this->args['fields'] = array_merge( array(), array( 'description' => 'Will return selected fields.' ) );
        $this->args['full'] = array_merge( array(), array( 'description' => 'Will return non-filtered data.' ) );
        $this->args['all'] = array_merge( array(), array( 'description' => 'Will return all items available if set to true.' ) );

        // Register endpoints
        add_action( 'rest_api_init', array( $this, 'register_endpoints' ));

    }

    /**
     * Register endpoints
     */
    public function register_endpoints() {

        // [GET] content
        register_rest_route( $this->namespace, '/content/(?P<type>[a-zA-Z0-9_-]+)(?:/(?P<name>[a-zA-Z0-9_\-=\(\)\[\]$.*+,;!~]+))?', array(
            'methods'  => 'GET',
            'callback' => array( $this, 'callback_GET_content' ),
            'args' => array(
                'fields' => array_merge( $this->args['fields'], array( 'required' => false ) ),
                'full' => array_merge( $this->args['full'], array( 'required' => false ) ),
                'all' => array_merge( $this->args['all'], array( 'required' => false ) )
            )
        ) );

    }

    /**
     * [Callback] [GET] content
     */
    public function callback_GET_content( WP_REST_Request $context ) {

        // Get content data.
        $url_params = $context->get_url_params();
        $query_params = $context->get_query_params();

        // Post preferences.
        $post_type = $url_params['type'];
        $post_name = $url_params['name'];

        // Check if user is logged in
        $preview_mode = current_user_can( 'edit_posts' );

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
        if ( $preview_mode ) {
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

            if (!$preview_mode) {
                unset($post['status']);
            }

            if ( $load_full_post ) {
                array_push( $filtered_posts, $post );
            } else {
                array_push( $filtered_posts, $this->filter_posts( $fields, $post ) );
            }
        }

        return new WP_REST_Response( $load_one ? $filtered_posts[0] : $filtered_posts );

    }

    /**
     * Filter data with seleted fields.
     */
    private function filter_posts($fields, $post) {

        $filtered_post = array();

        // Adds property to filtered data if fields contains key.
        foreach ( $post as $key => $value ) {
            if ( in_array( $key, $fields ) ) {
                $filtered_post[$key] = $value;
            }
        }

        return $filtered_post;

    }
}

function backstage_endpoints() {
	global $backstage_endpoints;

	if( !isset($backstage_endpoints) ) {
		$backstage_endpoints = new BackstageEndpoints();
		$backstage_endpoints->init();
	}

	return $backstage_endpoints;
}

backstage_endpoints();

endif;