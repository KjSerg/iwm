<?php

namespace Invoice\features;

class Columns {
	public function __construct() {
		$this->init();
	}

	public function init(): void {
		$this->init_columns();
		$this->init_sortable_columns();
		$this->pre_get_posts_init();
		$this->restrict_manage_posts();
	}

	public function restrict_manage_posts(): void {
		add_action( 'restrict_manage_posts', function ( $post_type ) {
			if ( $post_type !== 'bill' ) {
				return;
			}

			$currencies = [
				'UAH' => 'UAH',
				'USD' => 'USD',
				'EUR' => 'EUR',
			];

			$selected_currency = $_GET['currency_filter'] ?? '';

			echo '<select name="currency_filter">';
			echo '<option value="">All currency</option>';
			foreach ( $currencies as $key => $label ) {
				printf(
					'<option value="%s" %s>%s</option>',
					esc_attr( $key ),
					selected( $selected_currency, $key, false ),
					esc_html( $label )
				);
			}
			echo '</select>';
		}, 10, 1 );

	}

	public function pre_get_posts_init(): void {
		add_action( 'pre_get_posts', function ( $query ) {
			if ( ! is_admin() || ! $query->is_main_query() ) {
				return;
			}
			if ( $orderby = $query->get( 'orderby' ) ) {
				if ( $orderby === 'price' ) {
					$query->set( 'meta_key', '_invoice_sum' );
					$query->set( 'orderby', 'meta_value_num' );
				}
				if ( $orderby === 'currency' ) {
					$query->set( 'meta_key', '_invoice_currency' );
					$query->set( 'orderby', 'meta_value' );
				}
			}
			if ($currency = $_GET['currency_filter'] ?? '') {
				$query->set('meta_query', [
					[
						'key'     => '_invoice_currency',
						'value'   => $currency,
						'compare' => '=',
					],
				]);
			}
		} );
	}


	public function init_columns(): void {
		add_filter( 'manage_edit-bill_sortable_columns', [ $this, 'add_bill_sortable_columns' ], 10, 1 );
	}

	public function init_sortable_columns(): void {
		add_filter( 'manage_edit-bill_columns', [ $this, 'add_bill_columns' ], 10, 1 );
		add_action( 'manage_posts_custom_column', [ $this, 'fill_post_columns' ], 10, 1 );
	}

	public function add_bill_sortable_columns( $columns ) {
		$columns['price']    = 'Price';
		$columns['currency'] = 'Currency';

		return $columns;
	}

	public function add_bill_columns( $my_columns ) {
		$my_columns['price']    = 'Price';
		$my_columns['currency'] = 'Currency';
		$my_columns['status']   = 'Status';

		return $my_columns;
	}

	public function fill_post_columns( $column ): void {
		global $post;
		$ID        = $post->ID;
		$post_type = get_post_type( $ID );
		$price     = '';
		$status    = '';
		$currency  = '';
		if ( $post_type == 'bill' ) {
			$price    = carbon_get_post_meta( $ID, 'invoice_sum' );
			$currency = carbon_get_post_meta( $ID, 'invoice_currency' ) ?: 'UAH';
		}
		switch ( $column ) {
			case 'price':
				echo $price;
				break;
			case 'status':
				echo $status;
				break;
			case 'currency':
				echo $currency;
				break;
		}
	}
}

new Columns();