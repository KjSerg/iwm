<?php
namespace InvoiceWM\models;
class BillModel {
	public static function get_bills( $args = [] ): array {
		$show_posts   = get_option( 'posts_per_page' );
		$page         = filter_input( INPUT_GET, 'page_number', FILTER_SANITIZE_NUMBER_INT );
		$default_args = [
			'post_type'      => 'bill',
			'posts_per_page' => $show_posts,
			'post_status'    => 'publish',
			'paged'          => $page,
		];
		$query_args   = wp_parse_args( $args, $default_args );

		return get_posts( $query_args );
	}
}