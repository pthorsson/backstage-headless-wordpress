<?php

/**
 * Placeholder function for determining the frontend origin.
 * @TODO Determine the headless client's URL based on the current environment.
 *
 * @return str Frontend origin URL, i.e., http://localhost:3000.
 */

function get_frontend_origin() {
	$origins = array(/*<*/'http://localhost:3000'/*>*/);
	$http_origin = $_SERVER['HTTP_ORIGIN'];

	return in_array($http_origin, $origins) ? $http_origin : $origins[0];
}

