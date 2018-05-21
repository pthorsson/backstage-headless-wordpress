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

        // Admin pages
        $this->include_module( 'admin-pages/page_cors.php' );
        $this->include_module( 'admin-pages/page_endpoints.php' );

        add_action( 'admin_menu', array( $this, 'add_admin_menu' ), 5 );
        add_action( 'init', array( $this, 'load_assets' ) , 5 );

        // Backstage modules
        $this->include_module( 'lib/ajax.php' );
        $this->include_module( 'lib/config.php' );
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
    public function load_assets() {
        wp_register_script( 'backstage-lib', $this->file('admin-pages/js/backstage-lib.js'), array('jquery'), $this->version );
        wp_register_script( 'backstage-cors', $this->file('admin-pages/js/backstage-cors.js'), array('jquery'), $this->version );
        wp_register_script( 'backstage-endpoints', $this->file('admin-pages/js/backstage-endpoints.js'), array('jquery'), $this->version );

        wp_register_style( 'backstage-cors', $this->file('admin-pages/css/backstage-cors.css'), false, $this->version );
        wp_register_style( 'backstage-endpoints', $this->file('admin-pages/css/backstage-endpoints.css'), false, $this->version );
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