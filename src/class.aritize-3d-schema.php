<?php

class Aritize3dSchema {

    public static function init() {
        add_action( 'admin_init', array( __CLASS__, 'aritize3d_create_table') );
    }

    private static function aritize3d_get_schema_create_table() {
        global $wpdb;

        $collate = $wpdb->get_charset_collate();

        $query = "CREATE TABLE {$wpdb->prefix}aritize3d_api_keys (
          key_id BIGINT UNSIGNED NOT NULL auto_increment,
          token varchar(255) NULL default null,
          token_secret varchar(255) NULL default null,
          auth0_domain varchar(255) NULL default null,
          last_access datetime NULL default null,
          3dview_button_position_wysiwyg varchar(255) NULL default null,
          PRIMARY KEY  (key_id),
          KEY token (token),
          KEY token_secret (token_secret)
        ) $collate";

        return $query;
    }

    private static function aritize3d_get_schema_drop_table() {
        global $wpdb;

        $query = "DROP TABLE IF EXISTS {$wpdb->prefix}aritize3d_api_keys";

        return $query;
    }

    private static function callAPI($method, $url, $data)
    {
        $curl = curl_init();
        switch ($method)
        {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);
                if ($data)
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS,  json_encode($data));
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }
        // Optional Authentication:
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

    public static function aritize3d_auto_insert_data() {
        global $wpdb;

        $auth0_domain = ARITIZE3D_AUTH0_DOMAIN;
        $query = "INSERT INTO {$wpdb->prefix}aritize3d_api_keys (auth0_domain) 
                value ('$auth0_domain')";

        return $wpdb->query($query);
    }

    public static function aritize3d_create_table() {
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        return dbDelta(self::aritize3d_get_schema_create_table());
    }


    public static function aritize3d_delete_table() {
        global $wpdb;

        $result = aritize3d_get_token();
        $auth0_domain = $result['auth0_domain'];
        $base_url = get_bloginfo('url');
        $method = "POST";
        $url = $auth0_domain."/ecommerce-api/auth/wpecommerce-uninstall";
        $data = array("shop" => $base_url);
 
        self::callAPI($method, $url, $data);

        $wpdb->query(self::aritize3d_get_schema_drop_table());
    }

}
