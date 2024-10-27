<?php
if (!defined('ABSPATH')) {
    exit;
}
// register class woocommerce resource
require_once( ARITIZE3D_PLUGIN_DIR . 'integrations/woocommerce/src/Aritize3dWoocommerceResource.php' );

use Aritize3dWoocommerceResource as Woo;

class Aritize3dAdapterController
{
    /**
     * Declare constants
     */
    const  MODEL = 'model';
    const  QUICK_AR = 'quickAR';

    protected $woocommerce_resource;

    protected $meta_key = '_aritize3d_data';

    public function __construct()
    {
        $this->woocommerce_resource = new Woo();
    }

    /**
     * @param $id
     * @return array
     */
    public function aritize3d_get_product_by_id($id)
    {
        $product = array();
        if (Aritize3dDependentPlugin::$current_plugin_name == ARITIZE3D_WOOCOMMERCE) {
            return $this->woocommerce_resource->aritize3d_get_product($id);
        }
        return $product;
    }

    public function aritize3d_get_product_store($filter, $page)
    {
        $products = array();
        if (Aritize3dDependentPlugin::$current_plugin_name == ARITIZE3D_WOOCOMMERCE) {
            $products = $this->woocommerce_resource->aritize3d_get_products($filter, $page);
        }
        return $products;
    }

    public function aritize3d_update_meta_field($product_id, $meta_value)
    {
        $current_meta_value = get_aritize3d_meta_data($product_id)[0];
        if(isset($meta_value[self::MODEL])) {
            $current_meta_value[self::MODEL] = $meta_value[self::MODEL];
            update_post_meta($product_id, $this->meta_key, $current_meta_value);
        } else {
            $current_meta_value[self::QUICK_AR] = $meta_value[self::QUICK_AR];
            update_post_meta($product_id, $this->meta_key, $current_meta_value);
            return array(
                self::QUICK_AR => $meta_value[self::QUICK_AR]['edges'][0]['node']['value']
            );
        }
        return [];
    }

    public function aritize3d_delete_meta_field($product_id)
    {
        return delete_post_meta($product_id, $this->meta_key);
    }

    public function aritize3d_create_meta_field($product_id, $meta_value)
    {
        if(isset($meta_value[self::MODEL])) {
            delete_post_meta($product_id, $this->meta_key);
            return array(
                'id' => save_aritize3d_data($product_id, $meta_value)
            );
        } else if (isset($meta_value[self::QUICK_AR])) {
            $current_meta_value = get_aritize3d_meta_data($product_id)[0];
            $current_meta_value[self::QUICK_AR] = $meta_value[self::QUICK_AR];
            update_post_meta($product_id, $this->meta_key, $current_meta_value);
            return array(
                self::QUICK_AR => $meta_value[self::QUICK_AR]['edges'][0]['node']['value']
            );
        }
        return [];
    }

}