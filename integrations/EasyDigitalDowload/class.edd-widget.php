<?php

/*
 * this class handling add meta box of page admin Add New Product
 * and check status attribute meta field show button to page Product Single
 */

class Aritize3dEddButton
{
    private static $initiated = false;

    const EDD_PURCHASE_LINK_TOP = 'edd_purchase_link_top';
    const EDD_PURCHASE_LINK_END = 'edd_purchase_link_end';
    const INTEGRATION_CLI_CLASS = 'Aritize3DIntegrationCLI';

    const POST_TYPES = 'download';

    public static $positions = array(
        'Purchase Link Top' => self::EDD_PURCHASE_LINK_TOP,
        'Purchase Link End' => self::EDD_PURCHASE_LINK_END
    );


    public static function init()
    {
        if (!self::$initiated) {
            self::init_hooks();
        }
    }

    public static function init_hooks()
    {
        add_action('add_meta_boxes', array('Aritize3dEddButton', 'edd_product_custom_aritize3d_meta_box'));
        add_action('add_meta_boxes', array(self::INTEGRATION_CLI_CLASS, 'aritize3d_product_custom_content_meta_box'));
        add_action('the_post', array(self::INTEGRATION_CLI_CLASS, 'aritize3d_custom_fields_display'));
        add_action('save_post', array(self::INTEGRATION_CLI_CLASS, 'aritize3d_product_save_meta_field_data'));
    }

    /*
     * The code for add metabox to page add new product edd
     */
    public static function edd_product_custom_aritize3d_meta_box()
    {

        if (!function_exists('aritize3d_product_create_meta_box')) {
            Aritize3DIntegrationCLI::aritize3d_product_create_meta_box(Aritize3dEddButton::POST_TYPES);
        }
    }
}
