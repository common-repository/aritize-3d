<?php

use Aritize3dAdapterController as Products;

if (!defined('ABSPATH')) {
    exit;
}

class Aritize3dDefaultController extends Aritize3dBaseController
{
    /**
     * Endpoint namespace.
     *
     * @var string
     */
    protected $namespace = ARITIZE3D_NAMESPACE . '/v1';

    /**
     * Route base.
     *
     * @var string
     */
    protected $base = '/products';

    protected $product_store;

    public function __construct()
    {
        $this->product_store = new Products();
    }

    /**
     * Register the routes for this class
     *
     * GET /products
     * GET /products/count
     * GET/PUT/DELETE /products/<id>
     *
     * @return void
     */
    public function aritize3d_register_routes()
    {
        # GET /products
        register_rest_route($this->namespace, $this->base,
            array(
                Aritize3dBaseController::METHOD => WP_REST_Server::READABLE,
                Aritize3dBaseController::PERMISSION_CALLBACK => array($this, Aritize3dBaseController::FUNCTION_PERMISSION_CALLBACK),
                Aritize3dBaseController::CALLBACK => array($this, 'aritize3d_get_products'),
            )
        );

        # GET/PUT/DELETE /products/<id>
        register_rest_route($this->namespace, $this->base . '/(?P<id>\d+)', array(
            array(
                Aritize3dBaseController::METHOD => WP_REST_Server::READABLE,
                Aritize3dBaseController::PERMISSION_CALLBACK => array($this, Aritize3dBaseController::FUNCTION_PERMISSION_CALLBACK),
                Aritize3dBaseController::CALLBACK => array($this, 'aritize3d_get_product'),
            ),
            array(
                Aritize3dBaseController::METHOD => WP_REST_Server::CREATABLE,
                Aritize3dBaseController::PERMISSION_CALLBACK => array($this, Aritize3dBaseController::FUNCTION_PERMISSION_CALLBACK),
                Aritize3dBaseController::CALLBACK => array($this, 'aritize3d_create_product_metafield'),
            ),
            array(
                Aritize3dBaseController::METHOD => WP_REST_Server::EDITABLE,
                Aritize3dBaseController::PERMISSION_CALLBACK => array($this, Aritize3dBaseController::FUNCTION_PERMISSION_CALLBACK),
                Aritize3dBaseController::CALLBACK => array($this, 'aritize3d_edit_product'),
            ),
            array(
                Aritize3dBaseController::METHOD => WP_REST_Server::DELETABLE,
                Aritize3dBaseController::PERMISSION_CALLBACK => array($this, Aritize3dBaseController::FUNCTION_PERMISSION_CALLBACK),
                Aritize3dBaseController::CALLBACK => array($this, 'aritize3d_delete_product_metafield'),
            )
        ));
    }

    /**
     * Get all products
     *
     * @param $filter
     * @param int $page
     * @return WP_error|WP_REST_Response
     */
    function aritize3d_get_products($filter, $page = 1)
    {
        try {
            $products = $this->product_store->aritize3d_get_product_store($filter, $page);
        } catch (\Throwable $throwable) {
            return $this->aritize3dGenerateErrorResponse(Aritize3dBaseController::STATUS_CODE . Aritize3dBaseController::STATUS_CODE_NOT_FOUND, Aritize3dBaseController::MESSAGE_NOT_FOUND, $throwable->getCode());
        }
        return $this->aritize3dGenerateSuccessResponse($products, Aritize3dBaseController::STATUS_SUCCESS);
    }

    /**
     * Get product by id
     *
     * @param $filter
     * @return WP_error|WP_REST_Response
     */
    function aritize3d_get_product($filter)
    {
        $id = $filter->get_params()['id'];
        try {
            $product = $this->product_store->aritize3d_get_product_by_id($id);
        } catch (Throwable $throwable) {
            return $this->aritize3dGenerateErrorResponse(Aritize3dBaseController::STATUS_CODE . Aritize3dBaseController::STATUS_CODE_NOT_FOUND, Aritize3dBaseController::MESSAGE_NOT_FOUND, $throwable->getCode());
        }
        // Verify id product
        if (empty($product)) {
            return $this->aritize3dGenerateErrorResponse(Aritize3dBaseController::STATUS_CODE . Aritize3dBaseController::INVALID_ID, ucwords(str_replace('_', ' ', Aritize3dBaseController::INVALID_ID)), 404);
        }
        return $this->aritize3dGenerateSuccessResponse($product, Aritize3dBaseController::STATUS_SUCCESS);
    }

    /**
     * Update metafield by product id
     *
     * @param $request
     * @return WP_error|WP_REST_Response
     */
    function aritize3d_edit_product($request)
    {
        $params = $request->get_params();
        $id = (int)$params['id'];
        $metafield_id = $params['metafield_id'];
        $product = $this->product_store->aritize3d_get_product_by_id($id);
        $post_meta = get_metadata_by_mid("post", $metafield_id);
        if (!$product || $post_meta->meta_key != Aritize3dBaseController::ARITIZE3D_DATA || $post_meta->post_id != $id) {
            return $this->aritize3dGenerateErrorResponse(Aritize3dBaseController::STATUS_CODE . Aritize3dBaseController::INVALID_ID, ucwords(str_replace('_', ' ', Aritize3dBaseController::INVALID_ID)), 404);
        }
        if (!isset($params[Aritize3dBaseController::ARITIZE3D_DATA])) {
            return $this->aritize3dGenerateErrorResponse(Aritize3dBaseController::STATUS_CODE . 'meta_field_not_found', 'Meta field not found', 404);
        }
        $meta_value = $params[Aritize3dBaseController::ARITIZE3D_DATA];

        try {
            $res = $this->product_store->aritize3d_update_meta_field($id, $meta_value);

            return $this->aritize3dGenerateSuccessResponse([
                'code' => Aritize3dBaseController::STATUS_CODE . strtolower(Aritize3dBaseController::SUCCESS_MESSAGE),
                Aritize3dBaseController::MESSAGE => Aritize3dBaseController::SUCCESS_MESSAGE,
                'data' => [
                    'value' => $res['quickAR']
                ],
                "id" => $metafield_id
            ], Aritize3dBaseController::STATUS_SUCCESS);
        } catch (Throwable $throwable) {
            return $this->aritize3dGenerateErrorResponse(Aritize3dBaseController::STATUS_CODE . Aritize3dBaseController::STATUS_CODE_NOT_FOUND, Aritize3dBaseController::MESSAGE_NOT_FOUND, $throwable->getCode());
        }
    }

    /**
     * Delete metafield by product id
     *
     * @param $request
     * @return WP_error|WP_REST_Response
     */
    function aritize3d_delete_product_metafield($request)
    {
        $params = $request->get_params();
        $id = (int)$params['id'];
        $product = $this->product_store->aritize3d_get_product_by_id($id);
        if (!$product) {
            return $this->aritize3dGenerateErrorResponse(Aritize3dBaseController::STATUS_CODE . Aritize3dBaseController::INVALID_ID, ucwords(str_replace('_', ' ', Aritize3dBaseController::INVALID_ID)), 404);
        }
        try {
            $this->product_store->aritize3d_delete_meta_field($id);
            return $this->aritize3dGenerateSuccessResponse([
                'code' => Aritize3dBaseController::STATUS_CODE . 'delete_metafield_success',
                Aritize3dBaseController::MESSAGE => Aritize3dBaseController::SUCCESS_MESSAGE,
                'data' => []
            ], Aritize3dBaseController::STATUS_SUCCESS);
        } catch (Throwable $throwable) {
            return $this->aritize3dGenerateErrorResponse(Aritize3dBaseController::STATUS_CODE . Aritize3dBaseController::STATUS_CODE_NOT_FOUND, Aritize3dBaseController::MESSAGE_NOT_FOUND, $throwable->getCode());
        }

    }

    /**
     * Create product metafield
     *
     * @param $request
     * @return WP_error|WP_REST_Response
     */
    function aritize3d_create_product_metafield($request)
    {
        $params = $request->get_params();
        $id = (int)$params['id'];
        $product = $this->product_store->aritize3d_get_product_by_id($id);
        if (empty($product)) {
            return $this->aritize3dGenerateErrorResponse(Aritize3dBaseController::STATUS_CODE . Aritize3dBaseController::INVALID_ID, ucwords(str_replace('_', ' ', Aritize3dBaseController::INVALID_ID)), 404);
        }
        $data = $params[Aritize3dBaseController::ARITIZE3D_DATA];
        try {
            $metafield = $this->product_store->aritize3d_create_meta_field($id, $data);
            $metafield_id = !empty($metafield['id']) ? $metafield['id'] : "";
            $metafield_data = !empty($metafield['quickAR']) ? $metafield['quickAR'] : [];
            if($metafield_id){
                return $this->aritize3dGenerateSuccessResponse([
                    'code' => Aritize3dBaseController::STATUS_CODE . strtolower(Aritize3dBaseController::SUCCESS_MESSAGE),
                    Aritize3dBaseController::MESSAGE => Aritize3dBaseController::SUCCESS_MESSAGE,
                    'data' => [],
                    'id' => $metafield_id
                ], Aritize3dBaseController::STATUS_SUCCESS);
            } else {
                return  $this->aritize3dGenerateSuccessResponse([
                    'code' => Aritize3dBaseController::STATUS_CODE . strtolower(Aritize3dBaseController::SUCCESS_MESSAGE),
                    Aritize3dBaseController::MESSAGE => 'Already save',
                    'data' => [
                        'value' => $metafield_data
                    ]
                ], Aritize3dBaseController::STATUS_SUCCESS);
            }
        } catch (\Throwable $throwable) {
            return $this->aritize3dGenerateErrorResponse(Aritize3dBaseController::STATUS_CODE . Aritize3dBaseController::STATUS_CODE_NOT_FOUND, Aritize3dBaseController::MESSAGE_NOT_FOUND, $throwable->getCode());
        }
    }

}

/**
 * Function to register our new routes from the controller.
 */
function aritize3d_register_class_default_controller()
{
    $controller = new Aritize3dDefaultController();
    $controller->aritize3d_register_routes();
}

add_action('rest_api_init', 'aritize3d_register_class_default_controller');