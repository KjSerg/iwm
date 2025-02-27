<?php
function invoice_wm_scripts(): void {
	wp_enqueue_style( 'invoice_wm-main-css', get_template_directory_uri() . '/assets/css/app.css', array(), '1.0.0' );
	wp_enqueue_script( 'invoice_wm-scripts-js', get_template_directory_uri() . '/assets/js/app.js', array(), '1.0.0', true );
	wp_localize_script( 'ajax-script', 'AJAX', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
}

add_action( 'wp_enqueue_scripts', 'invoice_wm_scripts' );

get_template_part( 'functions/core/Carbon' );
get_template_part( 'functions/features/Columns' );
get_template_part( 'functions/helpers/helpers' );
get_template_part( 'functions/settings/SettingsTheme' );