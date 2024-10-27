<?php

/**
 * render 'a' element that brings some $params to 3D app
 * @return string
 */
function aritize3d_load_gotoapp_button()
{
    //get data from db with current plugin name
    $result = aritize3d_get_token();
    $token_data = $result;
    $params = '';
    $base_url = get_bloginfo('url');
    //generate button
    $params .= 'access_token=' . $token_data['token'];
    $params .= '&market=wpecommerce';
    $params .= '&shop=' . $base_url;
    if (!$token_data['token']) {
        return '<p style="font-size: 18px">' . esc_html(__('Access token required, please ', 'aritize-3d')) . '</p><a type="button"
        style="color: #0d45ff !important;
            background-color: transparent !important;
            border: none !important;
            text-decoration: none;
            border-radius: unset !important;
            padding: unset !important;
            display: unset !important;
            margin: unset !important;
            "
        href="' . esc_html(sanitize_text_field('/wp-admin/admin.php?page=aritize-3d-settings')) . '">' . esc_html(__('Go setting page to config', 'aritize-3d')) . '</a>';
    }
    $auth_domain = $result['auth0_domain'];
    return '<a style="padding: 20px 30px;
    flex-direction: row;
    justify-content: center;
    display: flex;" type="button" href="https://www.nextechar.com/aritize-3d" target="_blank">' .
    '<div>
        New Account Sign-up 
        </div>
    </a>
    <a style="padding: 20px 30px;
    flex-direction: row;
    justify-content: center;
    display: flex;
    margin-top: 20px;" type="button" href="' . esc_html(sanitize_text_field($auth_domain . '?' . $params)) . '" target="_blank">' .
    '<div>
        Existing Account Login
        </div>
    </a>';
}
