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
     * [Response] CORS GET request
     */
    private function cors_get() {
        global $backstage_config;

        $data = $backstage_config->load( 'cors' );

        wp_send_json( $data, 200 );
    }

    /**
     * [Response] CORS POST request
     */
    private function cors_post() {
        global $backstage_config;

        $data = array();

        $data['enabled'] = ($_POST['enabled'] === 'true');
        $data['origins'] = isset($_POST['origins']) ? $_POST['origins'] : array();

        $backstage_config->save( 'cors', $data );

        wp_send_json( $data, 200 );
    }

    /**
     * [Response] Endpoints GET request
     */
    private function endpoints_get() {
        $data = array( 'omg' => 'endpoints_get' );
        wp_send_json( $data, 200 );
    }

    /**
     * [Response] Endpoints POST request
     */
    private function endpoints_post() {
        $data = array( 'omg' => 'endpoints_post' );
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