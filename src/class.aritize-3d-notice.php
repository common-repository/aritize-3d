<?php
function aritize_active_notice()
{
    $class = 'notice notice-error is-dismissible';
    $message = esc_html('Aritize 3D plugin need an activation of dependency ecommerce plugin. (Now support only WooCommerce)', 'aritize-3d');

    printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
}

function aritize_deactivate_plugin_now()
{
    if (function_exists('is_plugin_active')) {
        if (is_plugin_active('aritize-3d/aritize-3d.php')) {
            deactivate_plugins('aritize-3d/aritize-3d.php');
            add_action('admin_notices', 'aritize_active_notice');
            add_action('admin_head', 'aritize_wphelp_hide_notices_wp');
        }
    }
}

function aritize_wphelp_hide_notices_wp()
{
    ?>
    <style>
        .notice.updated {
            display: none;
        }
    </style>
    <?php
}

