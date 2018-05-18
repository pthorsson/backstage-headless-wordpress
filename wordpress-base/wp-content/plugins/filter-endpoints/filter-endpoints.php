<?php
/*
Plugin Name: Filter Endpoints
Version: 0.1
Description: Filter Endpoints
Author: Patrik Thorsson
*/

function hlwp_is_visible($endpoint) {
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

// add_filter( 'rest_endpoints', function($endpoints) {
//     foreach ( $endpoints as $path => $cb ) {
//         if (!hlwp_is_visible($path)) {
            
//             // unset($endpoints[$path]);
//         }
//     }
//     return $endpoints;
// });

add_action('admin_menu', 'hlwp_endpoint_filter');

class HLWPEndpointFilter {

}
 
function hlwp_endpoint_filter(){
    add_menu_page( 'Filter endpoints page', 'Filter endpoints', 'manage_options', 'filter-endpoints-plugin', 'hlwp_endpoint_filter_init' );
}
 
function hlwp_endpoint_filter_init() {
?>
    <h2>Endpoint filter</h2>
    <p>Here you can filter which endpoints to expose in the API.</p>
<?php
}