<?php

class Aritize3DGeneralSetting
{
    /**
     * Constant declaration
     */
    const WP_VERIFY = 'wp_verify';
    const ARITIZE3D_AUTH0_DOMAIN  = 'auth0_domain';
    const TOKEN  = 'token';
    const TOKEN_SECRET = 'token_secret';
    const KEY_ID = 'key_id';
    const DEFAULT_POSITION = '3dview_button_position_wysiwyg';

    public static function init()
    {
        add_action('admin_post', array(__CLASS__, 'aritize3d_save'));
    }

    public static function aritize3d_save()
    {
        $verify = self::aritize3d_has_valid_nonce();
        $verify_nonce = $verify[self::WP_VERIFY];
        $verify_tab = $verify['tab'];

        if ($verify_tab === "general_setting" && $verify_nonce == "1") {
            return self::aritize_general_setting();
        } elseif ($verify_tab === "generate_token" && $verify_nonce == "1") {
            return self::aritize3d_generate_token();
        } elseif ($verify_tab === "customize_css" && $verify_nonce == "1") {
            return self::aritize_customize_css();
        }

        return self::aritize3d_redirect_page();
    }

    protected static function aritize_customize_css() {
        $file = plugin_dir_path(ARITIZE3D_PLUGIN_DIR ). ARITIZE3D_NAMESPACE . '/assets/customize-style-embed-button/customize.css';
        $action_update = !empty($_POST['aritize3d-update-customize-css']) ? sanitize_text_field($_POST['aritize3d-update-customize-css']) : '';
        $action_revert = !empty($_POST['restore-customize-css']) ? sanitize_text_field($_POST['restore-customize-css']) : '';
        if (!empty($action_update) && $action_update) {
            $data = sanitize_textarea_field($_POST['custom-css']);
            if (preg_match('/<script|\?/', $data)) {
                return self::aritize3d_redirect_page();
            }
            file_put_contents($file, $data);

            return self::aritize3d_redirect_page();
        } elseif (!empty($action_revert) && $action_revert) {
            $css_default = file_get_contents(plugin_dir_path(ARITIZE3D_PLUGIN_DIR ). ARITIZE3D_NAMESPACE . '/assets/customize-style-embed-button/main.css');
            file_put_contents($file, $css_default);

            return self::aritize3d_redirect_page();
        }

    }

    protected static function aritize_general_setting() {
        global $wpdb;

        if (!self::aritize3d_has_valid_nonce() && current_user_can('manage_options')) {
            return self::aritize3d_redirect_page();
        }

        // Get table name
        $table = $wpdb->prefix.'aritize3d_api_keys';
        $info_token = aritize3d_get_token();
        $auth_domain = sanitize_text_field($_POST[self::ARITIZE3D_AUTH0_DOMAIN]);
        $token = $info_token[self::TOKEN];
        $token_secret = $info_token[self::TOKEN_SECRET];
        $default_position = sanitize_text_field($_POST[self::DEFAULT_POSITION]);
        $data = [
            self::TOKEN => $token,
            self::TOKEN_SECRET => $token_secret,
            self::ARITIZE3D_AUTH0_DOMAIN => $auth_domain,
            'last_access' => date('Y-m-d H:i:s'),
            '3dview_button_position_wysiwyg' => $default_position
        ];
        $key_token_id = sanitize_text_field($_POST[self::KEY_ID]);
        if ($key_token_id && $auth_domain) {
            $result = $wpdb->update($table, $data, array(self::KEY_ID => $key_token_id), array('%s', '%s', '%s', '%s'), array('%d'));

            if ($result > 0) {
                $wpdb->flush();
            }
        }

        return self::aritize3d_redirect_page();
    }

    protected static function aritize3d_generate_token() {
        global $wpdb;

        if (!self::aritize3d_has_valid_nonce() && current_user_can('manage_options')) {
            return self::aritize3d_redirect_page();
        }

        // Get table name
        $table = $wpdb->prefix.'aritize3d_api_keys';

        //Generate token
        $token = sanitize_text_field('aritize3d_'.self::aritize3d_token_rand());

        $info_token = aritize3d_get_token();

        //Push data to array
        $data = array(
            self::TOKEN => $token,
            self::TOKEN_SECRET => self::aritize3d_encrypt($token, ARITIZE3D_KEY_SECRET),
            self::ARITIZE3D_AUTH0_DOMAIN => $info_token[self::ARITIZE3D_AUTH0_DOMAIN] ?? '',
            'last_access' => date('Y-m-d H:i:s')
        );
        if (!empty(sanitize_text_field($_POST['type-post']))) {
            $key_token_id = sanitize_text_field($_POST[self::KEY_ID]);
            $result = $wpdb->update($table, $data, array(self::KEY_ID => $key_token_id), array('%s', '%s', '%s', '%s'), array('%d'));

            if ($result > 0) {
                $wpdb->flush();
            }
        }

        return self::aritize3d_redirect_page();
    }

    /**
     * @return array|bool
     */
    public static function aritize3d_has_valid_nonce()
    {
        $general_setting_message = isset($_POST['general-setting-message']) ? sanitize_text_field($_POST['general-setting-message']) : '';
        $general_token_message = isset($_POST['generate-token-message']) ? sanitize_text_field($_POST['generate-token-message']) : '';
        $aritize3d_customize_css_message = isset($_POST['aritize3d-customize-css-message']) ? sanitize_text_field($_POST['aritize3d-customize-css-message']) : '';

        if ($general_setting_message != null) {
            $field = sanitize_text_field(wp_unslash($_POST['general-setting-message']));
            $action = 'general-setting-save';
            $result = [
                'tab' => 'general_setting',
                self::WP_VERIFY => wp_verify_nonce($field, $action),
            ];

            return $result;
        }

        if ($general_token_message != null) {
            $field  = sanitize_text_field(wp_unslash( $_POST['generate-token-message'] ));
            $action = 'generate-token-save';
            $array = [
                'tab' => 'generate_token',
                self::WP_VERIFY => wp_verify_nonce($field, $action),
            ];

            return $array;
        }

        if ($aritize3d_customize_css_message != null) {
            $field  = sanitize_text_field(wp_unslash( $_POST['aritize3d-customize-css-message'] ));
            $action = 'aritize3d-customize-css-update';
            $array = [
                'tab' => 'customize_css',
                self::WP_VERIFY => wp_verify_nonce($field, $action),
            ];

            return $array;
        }

        return false;
    }

    // Redirect page
    private static function aritize3d_redirect_page() {
        $wp_http_referer = sanitize_text_field($_POST['_wp_http_referer']);
        if (! isset( $wp_http_referer ) ) {
            $wp_http_referer = wp_login_url();
        }

        $url = sanitize_text_field(
            wp_unslash( $wp_http_referer )
        );

        //Redirect back to the admin page.
        wp_safe_redirect( urldecode( $url ) );
        exit;
    }

    /**
     * @return string
     */
    private static function aritize3d_token_rand() {
        return sha1( uniqid() );
    }

    /**
     * @param $data
     * @param $key
     * @return string
     */
    public static function aritize3d_encrypt($data, $key) {
        $plaintext = $data;
        $ivlengt = openssl_cipher_iv_length($cipher = ARITIZE3D_ENCRYPTION_METHOD);
        $iv = openssl_random_pseudo_bytes($ivlengt);
        $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $key, true);
        $ciphertext = base64_encode($iv . $hmac . $ciphertext_raw);

        return $ciphertext;
    }

    /**
     * @param $data
     * @param $key
     * @return false|string
     */
    public static function aritize3d_decrypt($data, $key) {
        $decode = base64_decode($data);
        $ivlen = openssl_cipher_iv_length($cipher = ARITIZE3D_ENCRYPTION_METHOD);
        $iv = substr($decode, 0, $ivlen);
        $hmac = substr($decode, $ivlen, $sha2len = 32);
        $ciphertext_raw = substr($decode, $ivlen + $sha2len);
        $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, $key,  true);
        if (hash_equals($hmac, $calcmac))
        {
            return $original_plaintext;
        }
    }
}