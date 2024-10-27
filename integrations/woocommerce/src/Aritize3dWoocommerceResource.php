<?php

class Aritize3dWoocommerceResource {

    /**
     * Constant declaration Images
     */
    const IMAGES_KEY = 'images';

    /**
     * @param $filter
     * @param $page
     * @return array
     */
    public function aritize3d_get_products($filter, $page)
    {
        $key_search = trim($filter['search'], ' ');
        if(empty($key_search)){
            return [];
        }
        $args_by_sku = array(
            'post_type' => 'product',
            'posts_per_page' => 10,
            'paged' => $page,
            'orderby' => 'title',
            'order' => 'ASC',
            'paginate' => true,
            'post_status'    => 'publish',
            'sku' => $key_search
        );

        $args_by_title = array(
            'post_type' => 'product',
            'posts_per_page' => 10,
            'paged' => $page,
            'paginate' => true,
            'orderby' => 'title',
            'order' => 'ASC',
            'post_status'    => 'publish',
            's' => $key_search
        );

        $results_by_sku = $this->aritize3d_get_product_by_sku($args_by_sku);
        $results_by_title = $this->aritize3d_get_product_by_title($args_by_title);
        return array_values(array_unique(array_merge($results_by_sku, $results_by_title), SORT_REGULAR));
    }

    public function aritize3d_get_product_by_sku($args) {
        $products_sku = array();
        $query = wc_get_products($args);

        $prod = $query->products;
        foreach ($prod as $key => $product) {
            $products_sku[$key] = $product->get_data();
            // add image url to response
            $images = $this->aritize3d_get_image_url($product);
            $products_sku[$key][self::IMAGES_KEY] = $images;
        }

        return $products_sku;
    }

    public function aritize3d_get_product_by_title($args) {
        $products_by_title = array();
        $query = wc_get_products($args);

        //get products
        $prod = $query->products;
        foreach ($prod as $key => $product) {
            $string = $product->get_data()['name'];
            if (preg_match("/{$args['s']}/i", $string)){
                $products_by_title[$key] = $product->get_data();
                // add image url to response
                $images = $this->aritize3d_get_image_url($product);
                $products_by_title[$key][self::IMAGES_KEY] = $images;
            }
        }

        return $products_by_title;
    }

    public function aritize3d_get_product($id)
    {
        $product = array();
        //get product
        $query = wc_get_product($id);
        if (!$query) {
            return $product;
        }
        $product = $query->get_data();
        // add image url to response
        $images = $this->aritize3d_get_image_url($query);
        $product[self::IMAGES_KEY] = $images;
        return $product;
    }


    protected function aritize3d_get_image_url($product)
    {
        $images = array();
        $attachment_ids = $product->get_gallery_image_ids();
        foreach ($attachment_ids as $attachment_id) {
            $image_url = wp_get_attachment_url($attachment_id);
            $image_alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', TRUE);
            $image_title = get_the_title($attachment_id);
            $image = [
                'id' => $attachment_id,
                'src' => $image_url,
                'name' => $image_title,
                'alt' => $image_alt
            ];
            array_push($images, $image);
        }
        return $images;
    }
}