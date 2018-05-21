<?php

if( ! class_exists('BackstageConfig') ) :

class BackstageConfig {

    private $method;

    private $defaultOptions = array(
        'cors' => array(
            'enabled' => false,
            'origins' => array()
        ),
        'endpoints' => array()
    );

    function __construct() {
        // Do nothing here
    }

    public function init() {

    }

    public function load($name) {
        $option = get_option( 'backstage_' . $name );
        return $option ? $option : $this->defaultOptions[$name];
    }

    public function save($name, $data) {
        update_option( 'backstage_' . $name, $data );
    }
}

function backstage_config() {
	global $backstage_config;

	if( !isset($backstage_config) ) {
		$backstage_config = new BackstageConfig();
		$backstage_config->init();
	}

	return $backstage_config;
}

backstage_config();

endif;