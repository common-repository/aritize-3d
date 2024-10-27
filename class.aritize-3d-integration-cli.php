<?php

class Aritize3DIntegrationCLI
{
    private static $initiated = false;

    public static function init()
    {
        if (!self::$initiated) {
            self::init_hooks();
        }
    }

    public static function init_hooks()
    {
        $pluginList = get_option('active_plugins');

        // Integration widget ARitize 3D to plugin
        foreach (Aritize3dDependentPlugin::$integrations as $plugin => $integration) {
            if (in_array($plugin, $pluginList)) {
                require_once(ARITIZE3D_PLUGIN_DIR . 'integrations/' . $integration['integration']);
                add_action('init', array($integration['button_class'], 'init'));
                Aritize3dDependentPlugin::$current_plugin_class = $integration['button_class'];
                Aritize3dDependentPlugin::$current_plugin_name = $integration['plugin_name'];
                break;
            }
        }

    }

    public static function aritize3d_product_create_meta_box($post_type)
    {
        add_meta_box(
            'aritize3d_product_meta_box',
            esc_html('ARitize 3D Button', 'aritize-3d'),
            'product_add_aritize3d_content_meta_box',
            $post_type,
            'side',
            'high'
        );
    }

    /*
     * Add content metabox to page admin new product
     */
    public static function aritize3d_product_custom_content_meta_box()
    {
        if (!function_exists('product_add_aritize3d_content_meta_box')) {
            function product_add_aritize3d_content_meta_box($post)
            {
                require_once 'integrations/aritize-3d-embed-metafield.php';

                $product_3dview_active = get_post_meta($post->ID, '_bhww_3dview_active_wysiwyg', true) == 'active';
                $current_button_position = get_post_meta($post->ID, '_bhww_3dview_button_position_wysiwyg', true);
                if(!$current_button_position){
                    $current_button_position = aritize3d_get_token()['3dview_button_position_wysiwyg'];
                }
                do_action('add_position_select_box', $current_button_position, Aritize3dDependentPlugin::$current_plugin_class);
                do_action('add_active_checkbox', $product_3dview_active);
            }
        }
    }

    /*
     * Check function exits and Call function save field data
     */
    public static function aritize3d_product_save_meta_field_data($post_id)
    {
        if (!function_exists('aritize3d_save_meta_data')) {
            self::aritize3d_save_meta_data($post_id);
        }
    }

    /*
     * Save data field to database
     */
    public static function aritize3d_save_meta_data($post_id)
    {
        $prefix = '_bhww_';
        /*
         * Check if our nonce is set.
         */
        if (!isset($_POST['aritize3d_product_field_nonce'])) {
            return $post_id;
        }
        $nonce = sanitize_text_field($_REQUEST['aritize3d_product_field_nonce']);
        /*
         * Verify that the nonce is valid.
         */
        if (!wp_verify_nonce($nonce)) {
            return $post_id;
        }
        /*
         * If this is an autosave, our form has not been submitted, so we don't want to do anything.
         */
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }
        /*
         * Check the user's permissions.
         */
        if (('product' == sanitize_text_field($_POST['post_type']) && !current_user_can('edit_product', $post_id)) || !current_user_can('edit_post', $post_id)
        ) {
            return $post_id;
        }
        /*
         * Sanitize user input and update the meta field in the database.
         */
        update_post_meta($post_id, $prefix . '3dview_active_wysiwyg', wp_kses_post(sanitize_text_field($_POST['3dview_active_wysiwyg'])));
        update_post_meta($post_id, $prefix . '3dview_button_position_wysiwyg', wp_kses_post(sanitize_text_field($_POST['3dview_button_position_wysiwyg'])));
    }

    /*
     * Display Button On Product Single Page
     */
    public static function aritize3d_custom_fields_display()
    {
        global $post;
        $post_id = $post->ID;
        $product = get_post_meta($post_id);
        $_aritize_3D_button_view = !empty($product['_bhww_3dview_active_wysiwyg']) ? $product['_bhww_3dview_active_wysiwyg'][0] == 'active' : 'inactive';
        $current_button_position = !empty($product['_bhww_3dview_button_position_wysiwyg']) ? $product['_bhww_3dview_button_position_wysiwyg'][0] : '';
        if ($_aritize_3D_button_view) {
            add_action($current_button_position, array('Aritize3DIntegrationCLI', 'aritize3d_show_button_frontend'), 30);
        }
    }


    public static function aritize3d_show_button_frontend() {
        global $post;
        $meta_data = get_aritize3d_meta_data($post->ID);
        Aritize3d::aritize3d_view(ARITIZE3D_VIEW_FRONT_END, 'aritize-3d-embed-button', compact('meta_data'));
    }
}

Aritize3DIntegrationCLI::init();