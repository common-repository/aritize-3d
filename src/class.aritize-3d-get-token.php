<?php

/**
 * @param $plugin_name
 * @return array|string[]
 */
function aritize3d_get_token() {
    global $wpdb;

    $tablename = $wpdb->prefix.'aritize3d_api_keys';
    $result = $wpdb->get_results("SELECT * FROM $tablename");

    if (!empty($result)) {
        return (array)$result[0];
    }
    return array(
        'key_id' => '',
        'token' => '',
        'plugin_name' => '',
        'last_access' => ''
    );
}

/**
 * get token query database and return results
 * @param $token
 * @return mixed
 */
function aritize3d_get_data_token(){
    global $wpdb;
    $tablename = $wpdb->prefix.'aritize3d_api_keys';
    $result = $wpdb->get_results("SELECT `token_secret` FROM $tablename");
    return $result[0]->token_secret;
}

