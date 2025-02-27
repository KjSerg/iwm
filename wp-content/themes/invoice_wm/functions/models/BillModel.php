<?php
namespace InvoiceWM\models;
class BillModel {
	public static function get_bills( $paged = 1, $posts_per_page = 10): array {
		$default_args = [
			'post_type'      => 'bill',
			'posts_per_page' => $posts_per_page,
			'post_status'    => 'publish',
			'paged'          => $paged
		];
		$query = new \WP_Query($default_args);

		return [
			'bills' => $query->posts,
			'max_pages' => $query->max_num_pages
		];
	}
}