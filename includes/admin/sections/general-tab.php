<?php
$token_info = aritize3d_get_token();
?>
<h1><?php esc_html_e('General', 'aritize-3d');?></h1>
<form action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>" method="POST">
    <div id="universal-message-container">
        <input type="hidden" name="key_id" value="<?php echo isset($token_info['key_id']) ? esc_attr( sanitize_text_field( $token_info['key_id'] ) ) : ''; ?>">
        <div class="options">
            <table>
                <tbody>
                <tr>
                    <th scope="row" class="titledesc" colspan="2" style="text-align: left">
                        <label><?php esc_html_e('Threedy portal domain', 'aritize-3d') ?></label>
                    </th>
                    <td class="forminp">
                        <input type="text"
                               maxlength="200"
                               size="40"
                               id="auth0_domain"
                               name="auth0_domain"
                               value="<?php echo isset($token_info['auth0_domain']) ? esc_attr( sanitize_text_field( $token_info['auth0_domain']) ) : ''; ?>"
                        />
                        <p class="description"><?php echo esc_html("(Default value https://ecommerce-api.nextechar.com, do not change this value)")?></p>
                        <?php
                            wp_nonce_field( 'general-setting-save', 'general-setting-message' );
                        ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row" class="titledesc" colspan="2" style="text-align: left">
                        <label><?php esc_html_e('Default embed button position', 'aritize-3d') ?></label>
                    </th>
                    <td>
                        <?php
                        $default_position = $token_info['3dview_button_position_wysiwyg'];
                        do_action('add_position_select_box', $default_position, Aritize3dDependentPlugin::$current_plugin_class);
                        ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <input type="submit" class="button button-primary" value="<?php esc_html_e('Update', 'aritize-3d') ?>">
</form>