<?php

if( ! class_exists('BackstageAjax') ) :

class BackstageAjax {

    private $method;

    function __construct() {
        // Do nothing here
    }

    public function init() {
        $this->method = $_SERVER[ 'REQUEST_METHOD' ];

        // Adding ajax actions
        add_action( 'wp_ajax_backstage_cors', array( $this, 'action_cors' ) );
        add_action( 'wp_ajax_backstage_endpoints', array( $this, 'action_endpoints' ) );
    }

    /**
     * [Action] CORS handeling
     */
    public function action_cors() {
        switch ( $this->method ) {
            case 'GET':  $this->cors_get();  break;
            case 'POST': $this->cors_post(); break;
            default:     $this->bad_request();
        }

        wp_die();
    }

    /**
     * [Action] Endpoints handeling
     */
    public function action_endpoints() {
        switch ( $this->method ) {
            case 'GET':  $this->endpoints_get();  break;
            case 'POST': $this->endpoints_post(); break;
            default:     $this->bad_request();
        }

        wp_die();
    }

    /**
     * [Response] Bad request 400
     */
    private function bad_request() {
        wp_send_json_error( 'Bad Request', 400 );
    }

    /**
     * [Endpoint action] CORS GET request
     */
    private function cors_get() {
        global $backstage_config;

        $data = $backstage_config->load( 'cors' );

        wp_send_json( $data, 200 );
    }

    /**
     * [Endpoint action] CORS POST request
     */
    private function cors_post() {
        global $backstage_config;

        $data = array();

        $data['enabled'] = ( $_POST['enabled'] === 'true' );
        $data['origins'] = isset( $_POST['origins'] ) ? $_POST['origins'] : array();

        $backstage_config->save( 'cors', $data );

        wp_send_json( $data, 200 );
    }

    /**
     * [Endpoint action] Endpoints GET request
     */
    private function endpoints_get() {
        global $backstage_config;

        $data = $backstage_config->load( 'endpoints' );

        $data['enabled'] = !isset( $data['enabled'] ) ? false   : $data['enabled'];
        $data['exposed'] = !isset( $data['exposed'] ) ? array() : $data['exposed'];

        $allEndpoints = array();

        foreach ( rest_get_server()->get_routes() as $route => $settings) {

            $endpoint = array(
                'endpoint' => $route,
                'methods' => array()
            );

            for ( $i = 0; $i < count( $settings ); $i++ ) {
                $methodCount = 0;
                $method = '';

                foreach ( $settings[$i]['methods'] as $methodType => $methodEnabled ) {

                    if ( $methodEnabled ) {
                        $method .= ( $methodCount === 0 ? $methodType : '/' . $methodType );
                    }

                    $methodCount++;
                }

                array_push( $endpoint['methods'], $method );
            }

            array_push( $allEndpoints, $endpoint );
        }
        
        $data = array(
            'enabled' => $data['enabled'],
            'exposed' => $data['exposed'],
            'all' => $allEndpoints
        );

        wp_send_json( $data, 200 );
    }

    /**
     * [Endpoint action] Endpoints POST request
     */
    private function endpoints_post() {
        global $backstage_config;

        $data = array();

        $data['enabled'] = ( $_POST['enabled'] === 'true' );
        $data['exposed'] = isset( $_POST['exposed'] ) ? wp_unslash( $_POST['exposed'] ) : array();

        $backstage_config->save( 'endpoints', $data );

        wp_send_json( $data, 200 );
    }

}

function backstage_ajax() {
	global $backstage_ajax;

	if( !isset($backstage_ajax) ) {
		$backstage_ajax = new BackstageAjax();
		$backstage_ajax->init();
	}

	return $backstage_ajax;
}

backstage_ajax();

endif;