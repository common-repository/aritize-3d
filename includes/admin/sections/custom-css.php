<?php
$data = ARITIZE3D_PLUGIN_DIR . '/assets/customize-style-embed-button/customize.css';
?>
<h1><?php esc_html_e('Customize stylesheet embed button', 'aritize-3d');?></h1>
<form id="aritize3d-customize-css" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>" method="POST">
    <div id="universal-message-container">
        <?php
        do_action('aritize3d_editor');
        ?>
        <textarea name="custom-css" id="aritize3d-customize-css-button" cols="30" rows="20" style="margin-top: 10px;"><?php echo esc_attr( sanitize_textarea_field( file_get_contents($data) ) )?></textarea>
    </div>
    <div style="margin-top: 15px;">
        <button type="submit" name="aritize3d-update-customize-css" class="button button-primary aritize3d-btn-update" value="update" style="margin-right: 10px"><?php esc_html_e('Update File', 'aritize-3d');?></button>
        <button type="submit" name="restore-customize-css" class="button button-alight btn-restore" value="restore"><?php esc_html_e('Revert to default', 'aritize-3d');?></button>
    </div>
    <?php
    wp_nonce_field( 'aritize3d-customize-css-update', 'aritize3d-customize-css-message' );
    ?>
</form>
<script>
    document.querySelector('.aritize3d-btn-update').addEventListener('click', (e) => {
        var code_content = editor.codemirror.getValue();
        if(!code_content){
            if(!confirm('The CSS of the embed buttons will be deleted.\n Are you sure you want to continue?')){
                e.preventDefault();
                window.location.reload();
            }
        }
    })
</script>