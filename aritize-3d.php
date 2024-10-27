<?php
/*
Plugin Name:  ARitize 3D
Plugin URI:   https://www.nextechar.com/aritize-3d
Description:  ARitize 3D’s patent-pending technology leverages Artificial Intelligence (AI) to enhance the building of quality 3D models from simple 2D photos at scale for ecommerce websites.
Version:      1.0.2
Author: Nextech AR Solutions
Author URI: https://www.nextechar.com
License: GPLv2 or later
Text Domain: aritize-3d
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

define( 'ARITIZE3D_VERSION', '1.0.2' );
define( 'ARITIZE3D_NAMESPACE', 'aritize-3d' );
define( 'ARITIZE3D_API_VERSION', 'v1' );
define( 'ARITIZE3D_MINIMUM_WP_VERSION', '5.0' );
define( 'ARITIZE3D_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ARITIZE3D_VIEW_ADMIN', 'admin' );
define( 'ARITIZE3D_VIEW_FRONT_END', 'front-end' );
define( 'ARITIZE3D_KEY_SECRET', 'aritize-3d-secret');
define( 'ARITIZE3D_ENCRYPTION_METHOD', 'AES-128-CBC');
define('ARITIZE3D_AUTH0_DOMAIN', 'https://ecommerce-api.nextechar.com');

define('ARITIZE3D_WOOCOMMERCE', 'Aritize3DWooCommerce');
define('ARITIZE3D_EASY_DIGITAL_DOWNLOAD', 'Aritize3DEasyDigitalDownload');
define('ARITIZE3D_WP_E_COMMERCE', 'Aritize3DWpEcommerce');

define('ARITIZE3D_APIBASE', 'https://quick-ar-test.threedy.ai');
define('ARITIZE3D_QD_APIBASE', 'https://quick-design-test.threedy.ai');
define('ARITIZE3D_THREEDY_TESTING', '//cdn-quick-ar-test.threedy.ai/latest/threedy.js');
define('ARITIZE3D_THREEDY_PRODUCTION', '//cdn-quick-ar.threedy.ai/latest/threedy.js');
define('ARITIZE3D', 'Aritize3d');
define('ARITIZE3D_SCHEMA', 'Aritize3dSchema');

//Get informattion token
require_once(ARITIZE3D_PLUGIN_DIR . 'src/class.aritize-3d-get-token.php');
// register class REST APIs
require_once( ARITIZE3D_PLUGIN_DIR . 'includes/rest-api/Aritize3dBaseController.php' );
require_once( ARITIZE3D_PLUGIN_DIR . 'includes/rest-api/v1/Aritize3dDefaultController.php' );
require_once( ARITIZE3D_PLUGIN_DIR . 'includes/rest-api/Aritize3dAdapterController.php' );

// register class Dependent Plugin
require_once( ARITIZE3D_PLUGIN_DIR . 'class.aritize-3d-dependent-plugin.php' );
// register integrations for ARitize 3D
require_once( ARITIZE3D_PLUGIN_DIR . 'class.aritize-3d-integration-cli.php' );

require_once( ARITIZE3D_PLUGIN_DIR . 'class.aritize-3d.php' );
// create record save data from aritize-3d app
require_once( ARITIZE3D_PLUGIN_DIR . 'src/class.aritize-3d-meta-data.php' );

//create table aritize-3d after active
require_once( ARITIZE3D_PLUGIN_DIR . 'src/class.aritize-3d-schema.php' );

//Register general setting page
require_once (ARITIZE3D_PLUGIN_DIR . 'src/class.aritize-3d-general-setting.php');

//Register embed metafield page
require_once 'integrations/aritize-3d-embed-metafield.php';
require_once 'integrations/aritize-3d-custom-css.php';

if( !function_exists('is_plugin_active') ) {
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

// Hook Plugin
register_activation_hook( __FILE__, array( ARITIZE3D_SCHEMA, 'aritize3d_create_table' ) );
register_activation_hook( __FILE__, array( ARITIZE3D_SCHEMA, 'aritize3d_auto_insert_data' ) );
register_deactivation_hook( __FILE__, array( ARITIZE3D_SCHEMA, 'aritize3d_delete_table' ) );

add_action('init', array('Aritize3DIntegrationCLI', 'init'));
add_action( 'init', array( ARITIZE3D, 'init' ) );
add_action( 'init',  array( ARITIZE3D_SCHEMA, 'init' ));
add_action( 'init', array('Aritize3DGeneralSetting', 'init') );

if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
    require_once( ARITIZE3D_PLUGIN_DIR . 'src/class.aritize-3d-notice.php' );
    if (!Aritize3dDependentPlugin::$current_plugin_name){
        add_action( 'admin_init', 'aritize_deactivate_plugin_now' );
    } else {
        require_once( ARITIZE3D_PLUGIN_DIR . 'class.aritize-3d-core.php' );
        add_action( 'init', array(ARITIZE3D, 'embed_setting_link') );
        add_action( 'init', array( 'Aritize3dCore', 'init' ) );
    }

    // register function generate button in aritize plugin dashboard
    require_once( ARITIZE3D_PLUGIN_DIR . 'src/class.aritize-3d-gotoapp-button.php' );
}
