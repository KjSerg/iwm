<?php
namespace InvoiceWM\models;
class BillModel {
	public static function get_bills( $paged = 1, $posts_per_page = 10,  $filters = []): array {
		$meta_query = ['relation' => 'AND'];
		$tax_query  = [];
		$query_args = [
			'post_type'      => 'bill',
			'posts_per_page' => $posts_per_page,
			'post_status'    => 'publish',
			'paged'          => $paged
		];
		if (!empty($filters['status'])) {
			$meta_query[] = [
				'key'     => '_invoice_status',
				'value'   => sanitize_text_field($filters['status']),
				'compare' => '='
			];
		}
		if (!empty($filters['currency'])) {
			$meta_query[] = [
				'key'     => '_invoice_currency',
				'value'   => sanitize_text_field($filters['currency']),
				'compare' => '='
			];
		}
		if (count($meta_query) > 1) {
			$query_args['meta_query'] = $meta_query;
		}
		if (!empty($tax_query)) {
			$query_args['tax_query'] = $tax_query;
		}

		$query = new \WP_Query($query_args);

		return [
			'bills' => $query->posts,
			'max_pages' => $query->max_num_pages
		];
	}
}