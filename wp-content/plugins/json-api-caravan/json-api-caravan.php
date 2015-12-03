<?php


define('DAYE_VERSION', '1.8');

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

define('JSON_API_CARAVAN_HOME', dirname(__FILE__));



if (!is_plugin_active('json-api/json-api.php')) {

    add_action('admin_notices', 'pim_draw_notice_json_api');

    return;

}



//add_filter('json_api_controllers', 'pimJsonApiController');

//add_filter('json_api_user_controller_path', 'setUserControllerPath');

//add_action('init', 'json_api_user_checkAuthCookie', 100);

add_filter('json_api_encode', 'my_encode_kittens');

load_plugin_textdomain('json-api-caravan', false, basename(dirname(__FILE__)) . '/languages');



function pim_draw_notice_json_api() {

    echo '<div id="message" class="error fade"><p style="line-height: 150%">';

    _e('<strong>JSON API Caravan</strong></a> requires the JSON API plugin to be activated. Please <a href="wordpress.org/plugins/json-api/‎">install / activate JSON API</a> first.', 'json-api-user');

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


function pimJsonApiController($aControllers) {

    $aControllers[] = 'User';

    return $aControllers;

}



function setUserControllerPath($sDefaultPath) {

    return dirname(__FILE__) . '/controllers/User.php';

}

function json_api_user_checkAuthCookie($sDefaultPath) {
    global $json_api;

    if ($json_api->query->cookie) {
      $user_id = wp_validate_auth_cookie($json_api->query->cookie, 'logged_in');
      if ($user_id) {
        $user = get_userdata($user_id);

        wp_set_current_user($user->ID, $user->user_login);
      }
    }
}