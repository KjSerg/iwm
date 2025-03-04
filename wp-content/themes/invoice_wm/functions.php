<?php
function invoice_wm_scripts(): void {
	wp_enqueue_style( 'invoice_wm-main-css', get_template_directory_uri() . '/assets/css/app.css', array(), '1.0.0' );
	wp_enqueue_script( 'invoice_wm-scripts-js', get_template_directory_uri() . '/assets/js/app.js', array(), '1.0.0', true );
	wp_localize_script( 'ajax-script', 'AJAX', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
}

add_action( 'wp_enqueue_scripts', 'invoice_wm_scripts' );

get_template_part( 'functions/core/Carbon' );
get_template_part( 'functions/core/Ajax' );
get_template_part( 'functions/features/Columns' );
get_template_part( 'functions/helpers/helpers' );
get_template_part( 'functions/settings/SettingsTheme' );
get_template_part( 'functions/wayforpay/Wayforpay' );
require_once get_template_directory() . '/functions/view/Bills.php';
require_once get_template_directory() . '/functions/models/BillModel.php';
require_once get_template_directory() . '/functions/controllers/BillController.php';

add_action( 'rest_api_init', function () {
	register_rest_route( 'invoice_wm/v1', '/wayforpay-payment-webhook/', [
		'methods'  => 'POST',
		'callback' => 'wayforpay_payment_webhook_callback',
	] );
} );

function wayforpay_payment_webhook_callback( WP_REST_Request $request ) {
	$payload   = file_get_contents( "php://input" );
	$obj       = json_decode( $payload, true );
	$order     = $obj['orderReference'];
	$signature = $obj['merchantSignature'];
	if ( ! $order ) {
		error_log( "Error $order" );
		http_response_code( 400 );
		exit( "Error $order" );
	}
	$order_id = intval( str_replace( 'WMI', '', $order ) );
	if ( ! $order_id || ! get_post( $order_id ) ) {
		http_response_code( 400 );
		error_log( "Error $order_id" );
		exit( "Error $order_id" );
	}
	carbon_set_post_meta( $order_id, 'wayforpay_log', $payload );
	$key                 = carbon_get_theme_option( 'wayforpay_key' );
	$amount              = $obj['amount'] ?? 0;
	$currency            = $obj['currency'] ?? '';
	$email               = $obj['email'] ?? '';
	$phone               = $obj['phone'] ?? '';
	$transactionStatus   = $obj['transactionStatus'] ?? '';
	$repayUrl            = $obj['repayUrl'] ?? '';
	$authCode            = $obj['authCode'] ?? '';
	$cardPan             = $obj['cardPan'] ?? '';
	$reasonCode          = $obj['reasonCode'] ?? '';
	$data                = [
		carbon_get_theme_option( 'wayforpay_account' ),
		$order,
		$amount,
		$currency,
		$authCode,
		$cardPan,
		$transactionStatus,
		$reasonCode
	];
	$string              = implode( ';', $data );
	$wayforpay_signature = hash_hmac( "md5", $string, $key );
	if ( $wayforpay_signature != $signature ) {
		http_response_code( 400 );
		error_log( "Error signature: $signature" );
		exit( "Error signature: $signature" );
	}

	carbon_set_post_meta( $order_id, 'wayforpay_amount', $amount );
	carbon_set_post_meta( $order_id, 'wayforpay_currency', $currency );
	carbon_set_post_meta( $order_id, 'wayforpay_email', $email );
	carbon_set_post_meta( $order_id, 'wayforpay_phone', $phone );
	carbon_set_post_meta( $order_id, 'wayforpay_transaction_status', $transactionStatus );
	carbon_set_post_meta( $order_id, 'wayforpay_repay_url', $repayUrl );
	$invoice_status = $transactionStatus == 'Approved' ? 'paid' : 'not_paid';
	carbon_set_post_meta( $order_id, 'invoice_status', $invoice_status );
	$time   = current_time( 'timestamp' );
	$d      = [ $order, 'accept', $time ];
	$string = implode( ';', $d );
	$hash   = hash_hmac( "md5", $string, $key );
	$res    = [
		'orderReference' => $order,
		'status'         => 'accept',
		'time'           => $time,
		'signature'      => $hash,
	];
	http_response_code( 200 );
	echo json_encode( $res );
}