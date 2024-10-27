<?php

function save_aritize3d_data($post_id, $data)
{
    if ($post_id < 1 || !isset($post_id)) {
        wp_send_json_error();
    }
    return add_post_meta($post_id, '_aritize3d_data', $data, true);
}

function get_aritize3d_meta_data($post_id) {
    return get_post_meta($post_id, '_aritize3d_data');
}
