<?php

class Aritize3d {
    private static $initiated = false;

    protected static $redirect_after_activation_option = 'aritize3d_redirect_after_activation';

    public static function init() {
        if ( ! self::$initiated ) {
            self::init_hooks();
        }
    }

    public static function init_hooks() {
        add_action( 'admin_enqueue_scripts', array( 'Aritize3d', 'aritize3d_load_resources' ) );
        add_action( 'view', array( 'Aritize3d', 'aritize3d_view' ) );
    }

    public static function embed_setting_link()
    {
        if (function_exists('is_plugin_active')) {
            if (is_plugin_active('aritize-3d/aritize-3d.php')) {
                add_filter( 'plugin_action_links_'.plugin_basename( plugin_dir_path( __FILE__ ) . 'aritize-3d.php'),
                    array( 'Aritize3d', 'aritize3d_plugin_settings_link' ) );
            }
        }
    }

    public static function aritize3d_plugin_settings_link( $links ) {
        $settings_link = '<a href="'.esc_url( 'admin.php?page=aritize-3d-settings' ).'">'.esc_html(__('Settings', 'aritize-3d')).'</a>';
        array_unshift( $links, $settings_link );

        return $links;
    }

    public static function aritize3d_load_resources() {
        wp_register_style( 'style.css', plugin_dir_url( __FILE__ ) . 'assets/style.css', array(), ARITIZE3D_VERSION );
        wp_enqueue_style('style.css');
    }

    public static function aritize3d_view( $view, $name, array $args = array()) {
        wp_register_style( 'customize.css', plugin_dir_url( __FILE__ ) . 'assets/customize-style-embed-button/customize.css', array(), ARITIZE3D_VERSION );
        wp_enqueue_style( 'customize.css');
        $args = apply_filters( 'aritize3d_view_arguments', $args, $name );

        foreach ( $args AS $key => $val ) {
            $meta_data = $val;
        }

        load_plugin_textdomain( 'aritize-3d' );
        if($view == 'admin') {
            $file = ARITIZE3D_PLUGIN_DIR . 'includes/admin/' . $name . '.php';
        } else {
            $file = ARITIZE3D_PLUGIN_DIR . 'includes/front-end/' . $name . '.php';
        }
        include( $file );
    }
}
