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

    function __construct() {
        // Do nothing here
    }

    public function init() {
        $this->settings = array(
            'dir' => plugin_dir_path( __FILE__ ),
            'url' => plugin_dir_url( __FILE__ )
        );

        // Admin pages
        $this->_include( 'admin-pages/page_cors.php' );
        $this->_include( 'admin-pages/page_endpoints.php' );

        add_action( 'admin_menu', array( $this, 'add_admin_menu' ), 5 );
        add_action( 'init', array( $this, 'load_assets' ) , 5 );
    }

    private function _include( $path ) {
        include_once( $this->settings['dir'] . $path );
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
    public function load_assets() {
        wp_register_script( 'backstage-script', $this->settings['url'] . 'admin-pages/js/backstage-script.js', array('jquery'), '1.0.0' );
		wp_enqueue_script( 'backstage-script' );

        wp_register_style( 'backstage-style', $this->settings['url'] . 'admin-pages/css/backstage-style.css', false, '1.0.0' );
		wp_enqueue_style( 'backstage-style' );
    }

    public function load_config($name) {
        
    }

    public function save_config($name, $data) {

    }
}

function backstage() {
	global $backstage;

	if( !isset($backstage) ) {
		$backstage = new Backstage();
		$backstage->init();
	}

	return $backstage;
}

backstage();

endif;