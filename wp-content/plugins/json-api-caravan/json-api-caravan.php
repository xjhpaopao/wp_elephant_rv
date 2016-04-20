<?php

/*

  Plugin Name: JSON API CARAVAN

  Plugin URI: http://www.parorrey.com/solutions/json-api-user/

  Description: Extends the JSON API for RESTful user registration, authentication, password reset, Facebook Login, user meta and BuddyPress Profile related functions. A Pro version is also available.

  Version: 1.9.1

  Author: Nidaye

  Author URI: http://www.parorrey.com/

  License: GPLv3

 */
define('DAYE_VERSION', '1.8');

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

define('JSON_API_CARAVAN_HOME', dirname(__FILE__));



if (!is_plugin_active('json-api/json-api.php')) {

    add_action('admin_notices', 'ndy_draw_notice_json_api');

    return;

}



add_filter('json_api_controllers', 'ndyJsonApiController');

add_filter('json_api_noname_controller_path', 'setNonameControllerPath');


//add_filter('json_api_encode', 'my_encode_kittens');

load_plugin_textdomain('json-api-caravan', false, basename(dirname(__FILE__)) . '/languages');



function ndy_draw_notice_json_api() {

    echo '<div id="message" class="error fade"><p style="line-height: 150%">';

    _e('<strong>JSON API Caravan</strong></a> requires the JSON API plugin to be activated. Please <a href="wordpress.org/plugins/json-api/‎">install / activate JSON API</a> first.', 'json-api-CARAVAN');

    echo '</p></div>';

}

function encode_custome_fields($response) {
    if (isset($response['posts'])) {
        foreach ($response['posts'] as $post) {
            my_add_kittens($post); // Add kittens to each post
        }
    } else if (isset($response['post'])) {
        my_add_kittens($response['post']); // Add a kittens property
    }

    return $response;
}

function my_add_kittens(&$post) {
    $post->kittens = 'Kittens!';
}


function ndyJsonApiController($aControllers) {

    $aControllers[] = 'Noname';

    return $aControllers;

}



function setNonameControllerPath($sDefaultPath) {

    return dirname(__FILE__) . '/controllers/noname.php';

}

function initTagForProduct()
{
    register_taxonomy_for_object_type('post_tag', 'product');
}
add_action( 'init', 'initTagForProduct', 10 );