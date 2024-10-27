<?php

class Aritize3dCore {
    const NONCE = 'aritize-3d-update-key';
    private static $initiated = false;

    public static $manage_options = 'manage_options';

    public static $admin_menu = 'admin_menu';

    public static function init() {
        if ( ! self::$initiated ) {
            self::init_hooks();
        }
    }

    public static function init_hooks() {
        add_action( 'admin_init', array( 'Aritize3dCore', 'admin_init' ) );
    }

    public static function admin_init() {
        load_plugin_textdomain( 'aritize-3d' );
    }

    public function aritize3d_add_menu()
    {
        add_menu_page (
            'Aritize 3D',
            'Aritize 3D',
            Aritize3dCore::$manage_options,
            ARITIZE3D_NAMESPACE,
            array( $this, 'show_aritize3d_plugin_page' ),
            '',
            '60'
        );
    }

    public function aritize3d_add_submenu()
    {
        add_submenu_page(
            ARITIZE3D_NAMESPACE,
            'My App',
            'My App',
            Aritize3dCore::$manage_options,
            'my-app',
            array( $this, 'show_aritize3d_plugin_page' )
        );

        add_submenu_page(
            ARITIZE3D_NAMESPACE,
            'Settings',
            'Settings',
            Aritize3dCore::$manage_options,
            'aritize-3d-settings',
            array( $this, 'show_aritize3d_setting_page' )
        );
    }

    public function aritize3d_remove_submenu()
    {
        remove_submenu_page(
            ARITIZE3D_NAMESPACE,
            ARITIZE3D_NAMESPACE
        );
    }

    /**
     * Display a custom menu page
     */
    function show_aritize3d_plugin_page()
    {
        if ( !current_user_can( Aritize3dCore::$manage_options ) )  {
            wp_die( esc_html_e( 'You do not have sufficient permissions to access this page.', 'aritize-3d' ) );
        }
        Aritize3d::aritize3d_view(ARITIZE3D_VIEW_ADMIN, ARITIZE3D_NAMESPACE);
    }

    function show_aritize3d_setting_page()
    {
        if (!current_user_can( Aritize3dCore::$manage_options ) ) {
            wp_die( esc_html_e( 'You do not have sufficient permissions to access this page.', 'aritize-3d' ) );
        }
        Aritize3d::aritize3d_view(ARITIZE3D_VIEW_ADMIN, 'settings');
    }


}

$cls = new Aritize3dCore();
add_action($cls::$admin_menu, array($cls, 'aritize3d_add_menu'));
add_action($cls::$admin_menu, array($cls, 'aritize3d_add_submenu'));
add_action($cls::$admin_menu, array($cls, 'aritize3d_remove_submenu'));

