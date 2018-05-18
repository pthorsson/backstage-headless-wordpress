<?php

if( ! class_exists('BackstageConfig') ) :

class BackstageConfig {

	private $avaiableConfig = array( 'cors', 'endpoints' );
	private $configDir;
	
	private $configData = array();
	private $defaultConfigData = array();

    function __construct() {
		// Do nothing
	}

	public function initialize() {
		$this->configDir = plugin_dir_path( __FILE__ ) . '../../backstage-json/';

		$this->defaultConfigData['cors'] 	  = '{ "enabled": false, "origins": ["http://localhost"] }';
		$this->defaultConfigData['endpoints'] = '{ "enabled": false, "visible": ["*"] }';
	}

	public function load($name) {
		if (!$configData[$name]) {
			$file_name = $name . '.json';
			$json_files = scandir( $this->configDir );
			$has_file = false;

			for ($i = 0; $i < count($json_files); $i++) {
				if ($json_files[$i] === $file_name) {
					$has_file = true;
				}
			}

			if ($has_file) {
				$json_data = file_get_contents( $this->configDir . $file_name );
			} else {
				$json_data = $this->defaultConfigData[$name];
			}

			$json_data = json_decode($json_data);

			$configData[$name] = $json_data;
		}

		return $configData[$name];
	}

	public function save($name, $data) {
		$configData[$name] = $data;
	}
    
}


function backstage_config() {

	global $backstage_config;

	if( !isset($backstage_config) ) {

		$backstage_config = new BackstageConfig();

		$backstage_config->initialize();

	}

	return $backstage_config;

}


// initialize
backstage_config();


endif; // class_exists check