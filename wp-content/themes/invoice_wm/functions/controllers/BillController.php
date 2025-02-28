<?php
namespace InvoiceWM\controller;

use InvoiceWM\models\BillModel;
use InvoiceWM\views\Bills;

class BillController {
	public static function render_bills(): void {
		$show_posts = get_option( 'posts_per_page' );
		$page       = filter_input( INPUT_GET, 'page_number', FILTER_SANITIZE_NUMBER_INT ) ?: 1;
		$filters    = [
			'status'   => $_GET['status'] ?? '',
			'currency' => $_GET['currency'] ?? '',
		];
		foreach ( $filters as $key => $value ) {
			$filters[ $key ] = sanitize_text_field( $value );
		}
		$bills = BillModel::get_bills( $page, $show_posts, $filters );
		$data  = new Bills();
		$data->render( $bills['bills'], $page, $bills['max_pages'] );
	}
}

?>
