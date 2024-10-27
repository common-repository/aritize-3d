<?php
$token_info = aritize3d_get_token();
?>
<h1><?php esc_html_e('REST API', 'aritize-3d');?></h1>
<form id="aritize3d-form-generate-token" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>" method="POST">
    <div id="universal-message-container">
        <input type="hidden" name="type-post"
               value="generate-token">
        <input type="hidden" name="plugin_name" value="<?php echo esc_attr( sanitize_text_field(Aritize3dDependentPlugin::$current_plugin_name) ); ?>">
        <input type="hidden" name="key_id" value="<?php echo isset($token_info['key_id']) ? esc_attr( sanitize_text_field($token_info['key_id']) ) : ''; ?>">
        <div class="options">
            <p>
            <div>
                <label>
                    <?php esc_html_e('Token', 'aritize-3d') ?>
                </label>
                <input type="text"
                       size="40"
                       id="token"
                       value="<?php echo isset($token_info['token']) ? esc_attr( sanitize_text_field( $token_info['token'] ) ) : ''; ?>"
                       disabled/>
                <?php
                wp_nonce_field( 'generate-token-save', 'generate-token-message' );
                ?>
                <input type="hidden" class="popup" value="<?php echo !empty($token_info['token']) ? esc_attr( sanitize_text_field('false')) : esc_attr( sanitize_text_field('true')); ?>">
                <input type="button" class="button-generated button button-primary" value="<?php esc_html_e('Generated', 'aritize-3d') ?>">
            </div>
            </p>
        </div>
    </div>
</form>
<script type="application/javascript">
    jQuery(document).ready(function($) {
        $('.button-generated').click(function () {
            let status_popup = $('.popup').val();
            if (status_popup === 'false') {
                var message = confirm("After generating a new token, navigate to the 3D App from the 3D plugin to ensure everything works correctly. \nAre you sure you want to continue?");
                if (message) {
                    $('#aritize3d-form-generate-token').submit();
                }
            } else {
                $('#aritize3d-form-generate-token').submit();
            }
        })
    });
</script>