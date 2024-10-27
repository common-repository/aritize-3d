<?php

class Aritize3dDependentPlugin
{
    /**
     * Path to the plugin file relative to the plugins directory
     */
    const EDD_PLUGIN = 'easy-digital-downloads/easy-digital-downloads.php';
    const WOO_PLUGIN = 'woocommerce/woocommerce.php';
    const WPSC_PLUGIN = 'wp-e-commerce/wp-shopping-cart.php';
    const INTEGRATION = 'integration';
    const BUTTON_CLASS = 'button_class';
    const PLUGIN_NAME = 'plugin_name';

    public static $current_plugin_class;
    public static $current_plugin_name;

    /**
     * @var array[][] $integrations
     * 'integration' => path of integration file
     * 'button_class' => class name of integration file
     */
    public static $integrations = array(
        self::EDD_PLUGIN => [
            self::INTEGRATION => 'EasyDigitalDowload/class.edd-widget.php',
            self::BUTTON_CLASS => 'Aritize3dEddButton',
            self::PLUGIN_NAME => ARITIZE3D_EASY_DIGITAL_DOWNLOAD
        ],
        self::WOO_PLUGIN => [
            self::INTEGRATION => 'woocommerce/Aritize3dWoocommerceButton.php',
            self::BUTTON_CLASS => 'Aritize3dWoocommerceButton',
            self::PLUGIN_NAME => ARITIZE3D_WOOCOMMERCE
        ],
        self::WPSC_PLUGIN => [
            self::INTEGRATION => 'wp-e-commerce/class.wp-e-commerce-widget.php',
            self::BUTTON_CLASS => 'Aritize3dEcommerceButton',
            self::PLUGIN_NAME => ARITIZE3D_WP_E_COMMERCE
        ]
    );
}
