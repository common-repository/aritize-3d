<?php

/**
 * action add radio check box in metafield of admin edit/add product
 */

add_action('add_active_checkbox', function ($product_3dview_active){
    if ($product_3dview_active) {
        ?>
        <input type="radio" name="3dview_active_wysiwyg" checked value="active" id="active-radio">
        <label for="active-radio"> <?php esc_html_e('Active', 'aritize-3d') ?> </label><br>
        <input type="radio" name="3dview_active_wysiwyg" value="deactive" id="inactive-radio">
        <?php
    } else {
        ?>
        <input type="radio" name="3dview_active_wysiwyg" value="active" id="active-radio">
        <label for="active-radio"> <?php esc_html_e('Active', 'aritize-3d') ?></label><br>
        <input type="radio" name="3dview_active_wysiwyg" checked value="deactive" id="inactive-radio">
        <?php
    }
    ?>
    <label for="inactive-radio"><?php esc_html_e('Deactive', 'aritize-3d') ?></label><br>
    <?php
});

/**
 * action add select box in metafield of admin edit/add product
 */
add_action('add_position_select_box', function ($current_button_position, $button_class){
    // add select box save position of button
    ?>
    <div style="width: 100%;margin: 10px 0;">
        <select name="3dview_button_position_wysiwyg" id="button_position">
            <?php
            $current_button_position = empty($current_button_position) ? 'woocommerce_before_add_to_cart_form' : $current_button_position;
            foreach ($button_class::aritize3d_woo_position_value() as $key => $position) {
                if ($current_button_position == $position) {
                    ?>
                    <option value="<?php echo esc_attr( sanitize_key($position)) ?>" selected><?php echo esc_attr( sanitize_text_field($key)) ?></option>
                    <?php
                } else {
                    ?>
                    <option value="<?php echo esc_attr( sanitize_key($position)) ?>"><?php echo esc_attr( sanitize_text_field($key)) ?></option>
                    <?php
                }
            }
            ?>
        </select>
    </div>
    <input type="hidden" name="aritize3d_product_field_nonce" value="<?php echo esc_attr( sanitize_text_field(wp_create_nonce())) ?>">
    <?php
}, 10, 2);
