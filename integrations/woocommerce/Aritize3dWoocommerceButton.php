<?php

/*
 * this class handling add meta box of page admin Add New Product
 * and check status attribute meta field show button to page Product Single
 */

class Aritize3dWoocommerceButton
{
    const NONCE = 'integration-woocommerce';
    private static $initiated = false;

    const WOO_POSITION_VALUE = array(
        'woocommerce_before_single_product_summary',
        'woocommerce_single_product_summary',
        'woocommerce_before_add_to_cart_form',
        'woocommerce_after_add_to_cart_form',
        'woocommerce_before_add_to_cart_button',
        'woocommerce_after_add_to_cart_button',
        'woocommerce_before_add_to_cart_quantity',
        'woocommerce_after_add_to_cart_quantity',
        'woocommerce_before_single_variation',
        'woocommerce_after_single_variation',
        'woocommerce_before_variations_form',
        'woocommerce_after_variations_form',
        'woocommerce_single_variation',
        'woocommerce_before_add_to_cart_quantity',
        'woocommerce_after_add_to_cart_quantity',
        'woocommerce_product_meta_start',
        'woocommerce_product_meta_end',
        'woocommerce_after_single_product_summary',
        'woocommerce_share',
    );
    const INTEGRATION_CLI_CLASS = 'Aritize3DIntegrationCLI';

    const POST_TYPES = 'product';


    public static function init()
    {
        if (!self::$initiated) {
            self::init_hooks();
        }
    }

    public static function init_hooks()
    {
        add_action('add_meta_boxes', array('Aritize3dWoocommerceButton', 'woocommerce_product_custom_aritize3d_meta_box'));
        add_action('add_meta_boxes', array(self::INTEGRATION_CLI_CLASS, 'aritize3d_product_custom_content_meta_box'));
        add_action('woocommerce_before_single_product', array(self::INTEGRATION_CLI_CLASS, 'aritize3d_custom_fields_display'));
        add_action('save_post', array(self::INTEGRATION_CLI_CLASS, 'aritize3d_product_save_meta_field_data'));
    }

    /*
     * The code for add metabox to page add new product woo
     */
    public static function woocommerce_product_custom_aritize3d_meta_box()
    {
        if (!function_exists('aritize3d_product_create_meta_box')) {
            Aritize3DIntegrationCLI::aritize3d_product_create_meta_box(Aritize3dWoocommerceButton::POST_TYPES);
        }
    }

    public static function aritize3d_woo_position_value() {
        foreach (self::WOO_POSITION_VALUE as $key => $value) {
            $result[ucfirst(str_replace('_', ' ', str_replace('woocommerce_', '', $value)))] = $value;
        }
        return $result;
    }
}
