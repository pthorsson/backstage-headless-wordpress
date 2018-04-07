<?php
/*
Plugin Name: Filter Endpoints
Version: 0.1
Description: Filter Endpoints
Author: Patrik Thorsson
*/

function hlwp_filter_endpoints($endpoint) {
    $visibleEndpoints = [
        "/",
        "/*",
        // "/menus/*",
        // "/wp/v2",
        // "/wp/v2/pages/:parent/*",
    ];

    $cleanedEndpoint = preg_replace("/\(.*?<([a-zA-Z0-9_]{1,})>.*?\)/", ":$1", $endpoint);

    for ($b=0; $b < count($visibleEndpoints); $b++) { 
        if (fnmatch($visibleEndpoints[$b], $cleanedEndpoint)) {
            return true;
        }
    }

    return false;
}

add_filter( 'rest_endpoints', function($endpoints) {

    foreach ( $endpoints as $path => $cb ) {
        if (!hlwp_filter_endpoints($path)) {
            unset($endpoints[$path]);
        }
    }

    return $endpoints;
});