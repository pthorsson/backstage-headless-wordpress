<?php

/*
Plugin Name: Backstage API
Description: Take control of your WordPress REST API.
Version: 1.0.0
Author: Patrik Thorsson
Text Domain: backstage
*/

if( ! class_exists('Backstage') ) :

class Backstage {

    private $configData = array();

    public $settings = array();
    public $version = '1.0.0';

    function __construct() {

        // Do nothing here

    }

    public function init() {

        $this->settings = array(
            'dir' => plugin_dir_path( __FILE__ ),
            'url' => plugin_dir_url( __FILE__ )
        );

        // Backstage modules
        $this->include_module( 'lib/config.php' );
        $this->include_module( 'lib/endpoints.php' );

        if ( is_admin() && current_user_can( 'manage_options' ) ) {

            add_action( 'admin_menu', array( $this, 'add_admin_menu' ), 5 );

            $this->register_assets();
    
            // Backstage admin modules
            $this->include_module( 'lib/ajax.php' );
    
            // Admin UI
            $this->include_module( 'admin-pages/page_cors.php' );
            $this->include_module( 'admin-pages/page_endpoints.php' );
            $this->include_module( 'admin-pages/widget_preview.php' );

        } else if ( !is_admin() ) {

            $this->set_cors();
            $this->set_exposed_endpoints();

        }
    }

    private function include_module( $path ) {

        include_once( $this->settings['dir'] . $path );

    }

    private function file( $path ) {

        return $this->settings['url'] . $path;

    }

    /**
     * [Action] Adding admin pages 
     */
    public function add_admin_menu() {

        $parent_slug  = 'backstage-api';
        $capability  = 'manage_options';
        
        add_menu_page( 'Backstage API', 'Backstage API', $capability, $parent_slug, false, 'dashicons-editor-code', '80.015' );
        add_submenu_page( $parent_slug, 'CORS', 'CORS', $capability, $parent_slug . '-cors', 'backstage_admin_page_cors' );
        add_submenu_page( $parent_slug, 'Endpoints', 'Endpoints', $capability, $parent_slug . '-endpoints', 'backstage_admin_page_endpoints' );

        remove_submenu_page( $parent_slug, $parent_slug );

    }

    /**
     * [Action] Load assets
     */
    public function register_assets() {

        // Scripts
        wp_register_script( 'backstage', $this->file( 'admin-pages/js/backstage.js' ), array( 'jquery', 'underscore' ), $this->version );
        wp_register_script( 'backstage-cors', $this->file( 'admin-pages/js/backstage-cors.js' ), array( 'jquery', 'underscore' ), $this->version );
        wp_register_script( 'backstage-endpoints', $this->file( 'admin-pages/js/backstage-endpoints.js' ), array( 'jquery', 'underscore' ), $this->version );

        // Styles
        wp_register_style( 'backstage', $this->file( 'admin-pages/css/backstage.css' ), false, $this->version );
        wp_register_style( 'backstage-cors', $this->file( 'admin-pages/css/backstage-cors.css' ), false, $this->version );
        wp_register_style( 'backstage-endpoints', $this->file( 'admin-pages/css/backstage-endpoints.css' ), false, $this->version );

    }

    /**
     * Enqueue scripts
     */
    public function enqueue_scripts( $scripts ) {

        wp_enqueue_script( 'backstage' );

        for ($i = 0; $i < count( $scripts ); $i++) { 
            wp_enqueue_script( $scripts[$i] );
        }

    }

    /**
     * Enqueue style
     */
    public function enqueue_styles( $styles ) {

        wp_enqueue_style( 'backstage' );

        for ($i = 0; $i < count( $styles ); $i++) { 
            wp_enqueue_style( $styles[$i] );
        }

    }

    private function set_cors() {

        add_action( 'rest_api_init', function() {

            global $backstage_config;
            $options = $backstage_config->load( 'cors' );

            if ( $options['enabled'] ) {

                remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );

                add_filter( 'rest_pre_serve_request', function( $value ) {

                    global $backstage_config;
                    $options = $backstage_config->load('cors');

                    $origins = $options['origins'];

                    if (array_key_exists('HTTP_ORIGIN', $_SERVER)) {
                        $http_origin = $_SERVER['HTTP_ORIGIN'];
                    } else if (array_key_exists('HTTP_REFERER', $_SERVER)) {
                        $http_origin = $_SERVER['HTTP_REFERER'];
                    } else {
                        $http_origin = $_SERVER['REMOTE_ADDR'];
                    }

                    $origin = in_array($http_origin, $origins) ? $http_origin : $origins[0];

                    header( 'Access-Control-Allow-Origin: ' . $origin );
                    header( 'Access-Control-Allow-Methods: GET' );
                    header( 'Access-Control-Allow-Credentials: true' );

                    return $value;

                });
            }

        }, 15 );

    }

    private function set_exposed_endpoints() {

        add_filter( 'rest_endpoints', function($endpoints) {

            global $backstage_config;
            $options = $backstage_config->load( 'endpoints' );

            if ( $options['enabled'] ) {
                foreach ( $endpoints as $path => $cb ) {
                    if ( !in_array( $path, $options['exposed'] ) ) {
                        unset( $endpoints[$path] );
                    }
                }
            }
            
            return $endpoints;

        });

    }

}

function backstage() {
	global $backstage;

	if( !isset($backstage) ) {
        $backstage = new Backstage();
        // $backstage->init();
        add_action( 'init', array( $backstage, 'init' ) );
	}

	return $backstage;
}

backstage();

endif;