<?php

function aritize3d_editor_customize_css_button() {
    // Enqueue code editor and settings for manipulating HTML.
    $settings = wp_enqueue_code_editor( array( 'type' => 'text/css' ) );
    wp_add_inline_script(
        'code-editor',
        sprintf(
            'jQuery( function() { editor = wp.codeEditor.initialize( "aritize3d-customize-css-button", %s ); } );',
            wp_json_encode( $settings )
        )
    );
}
add_action( 'aritize3d_editor', 'aritize3d_editor_customize_css_button' );