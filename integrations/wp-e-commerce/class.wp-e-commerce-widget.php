<?php

/*
 * this class handling add meta box of page admin Add New Product
 * and check status attribute meta field show button to page Product Single
 */

class Aritize3dEcommerceButton
{
    const NONCE = 'integration-ecommerce';
    private static $initiated = false;

    const ECOMMERCE_SINGLE_PRODUCT_FORM_FIELDS_END = 'wpsc_product_form_fields_end';
    const ECOMMERCE_PRODUCT_ADDONS = 'wpsc_product_addons';
    const INTEGRATION_CLI_CLASS = 'Aritize3DIntegrationCLI';

    const POST_TYPES = 'wpsc-product';

    public static $positions = array(
        'Single product end form fields' => self::ECOMMERCE_SINGLE_PRODUCT_FORM_FIELDS_END,
        'Single product addons' => self::ECOMMERCE_PRODUCT_ADDONS
    );

    public static function init()
    {
        if (!self::$initiated) {
            self::init_hooks();
        }
    }

    public static function init_hooks()
    {
        add_action('add_meta_boxes', array('Aritize3dEcommerceButton', 'aritize3d_ecommerce_product_custom_meta_box'));
        add_action('add_meta_boxes', array(self::INTEGRATION_CLI_CLASS, 'aritize3d_product_custom_content_meta_box'));
        add_action('wpsc_product_addons', array(self::INTEGRATION_CLI_CLASS, 'aritize3d_custom_fields_display'));
        add_action('save_post', array(self::INTEGRATION_CLI_CLASS, 'aritize3d_product_save_meta_field_data'));
    }

    /*
     * The code for add metabox to page add new product woo
     */
    public static function aritize3d_ecommerce_product_custom_meta_box()
    {
        if (!function_exists('aritize3d_product_create_meta_box')) {
            Aritize3DIntegrationCLI::aritize3d_product_create_meta_box(Aritize3dEcommerceButton::POST_TYPES);
        }
    }
}