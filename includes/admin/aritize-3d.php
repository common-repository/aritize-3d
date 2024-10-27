<?php

//phpcs:disable VariableAnalysis
// There are "undefined" variables here because they're defined in the code that includes this file as a template.

?>
<div id="threedy-plugin-container">
    <div class="container">
        <div class="threedy-layout">
            <div class="threedy-logo"></div>
            <h3 class="threedy-title"><?php esc_html(__('Welcome to ARitize3D', 'aritize-3d')); ?></h3>
            <div class="threedy-button">
                <?php echo aritize3d_load_gotoapp_button() ?>
            </div>
        </div>
    </div>
</div>