<?php
use WP_REST_Response as Response;
use WP_Error as ResponseError;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Aritize3dBaseController extends WP_REST_Controller
{
    const STATUS_SUCCESS = 200;
    const STATUS_CODE = "aritize3d_rest_product_";
    const MESSAGE = 'message';
    const SUCCESS_MESSAGE = 'Success';
    const ARITIZE3D_DATA = '_aritize3d_data';
    const INVALID_ID = 'invalid_id';
    const METHOD = 'methods';
    const PERMISSION_CALLBACK = 'permission_callback';
    const CALLBACK = 'callback';
    const FUNCTION_PERMISSION_CALLBACK = 'aritize3dcheckAuthenToken';
    const STATUS_CODE_NOT_FOUND = 'not_found';
    const MESSAGE_NOT_FOUND = 'Not Found.';

    /**
     * Generate success response
     *
     * @param array $data
     * @return \WP_REST_Response
     */
    protected function aritize3dGenerateSuccessResponse($data, $status_code)
    {
        return new Response(
            $data,
            $status_code
        );
    }

    /**
     * Generate error response
     *
     * @param string $data
     * @return \WP_error
     */
    protected function aritize3dGenerateErrorResponse($code, $message, $status_code)
    {
        return new ResponseError(
            $code,
            $message,
            [
                'status' => $status_code
            ]
        );
    }

    /**
     * get request call query function and compare results
     * @return bool
     */
    public function aritize3dcheckAuthenToken(){
        $header = apache_request_headers();
        if(!array_key_exists('Authorization', $header) || $header['Authorization'] == '')
        {
            return false;
        }
        $client_token = sanitize_text_field($header['Authorization']);
        $data_token = aritize3d_get_data_token();
        $token_decrypt = Aritize3DGeneralSetting::aritize3d_decrypt($data_token,ARITIZE3D_KEY_SECRET);
        if($token_decrypt === $client_token){
            return true;
        }
        return false;
    }
}