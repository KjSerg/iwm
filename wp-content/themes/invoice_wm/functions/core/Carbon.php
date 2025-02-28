<?php

namespace InvoiceWM\core;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class Carbon {

	private static ?self $instance = null;

//	private array $labels;
//	private array $screens_labels;
	private array $fields_without_button;

	private function __construct() {
		$this->initialize_labels();
		$this->initialize_actions();
		$this->initialize_filters();
	}

	public static function get_instance(): self {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function initialize_actions(): void {
		add_action( 'after_setup_theme', [ $this, 'load_carbon_fields' ] );
		add_action( 'carbon_fields_register_fields', [ $this, 'register_theme_options' ] );
		add_action( 'carbon_fields_register_fields', [ $this, 'attach_fields_to_bill' ] );
	}

	private function initialize_filters(): void {
		add_filter( 'crb_media_buttons_html', [ $this, 'filter_media_buttons_html' ], 10, 2 );
	}

	public function register_theme_options(): void {
		Container::make( 'theme_options', 'Theme Settings' )
		         ->set_page_parent( 'options-general.php' )
		         ->add_fields( array(
			         Field::make( 'image', 'logo' )->set_required(),
		         ) );
	}

	public function filter_media_buttons_html( string $html, string $field_name ): ?string {
		if ( in_array( $field_name, $this->fields_without_button, true ) ) {
			return null;
		}

		return $html;
	}

	private function initialize_labels(): void {
//		$this->labels = [
//			'plural_name'   => 'items',
//			'singular_name' => 'item',
//		];
//
//		$this->screens_labels = [
//			'plural_name'   => 'screens',
//			'singular_name' => 'screen',
//		];
//
//		$this->types_labels = [
//			'plural_name'   => 'types',
//			'singular_name' => 'type',
//		];

		$this->fields_without_button = [ 'text', 'subtitle', 'title' ];
	}

	public function attach_fields_to_bill(): void {
		Container::make( 'post_meta', 'Data' )
		         ->where( 'post_type', '=', 'bill' )
		         ->add_fields( [
			         Field::make( 'text', 'invoice_sum' )->set_width( 50 )
			              ->set_required()
			              ->set_attribute( 'type', 'number' ),
			         Field::make( 'select', 'invoice_currency' )->set_width( 50 )
			              ->set_required()
			              ->set_options( 'get_invoice_currency' ),
			         Field::make( 'select', 'invoice_status' )->set_width( 50 )
			              ->set_required()
			              ->set_options( [
				              'not_paid' => __( 'Not paid' ),
				              'paid'     => __( 'Paid' ),
			              ] ),
			         Field::make( 'multiselect', 'invoice_pay_methods' )->set_width( 50 )
			              ->set_required()
			              ->set_options( [
				              'wayforpay' => 'wayforpay',
				              'whitepay'  => 'whitepay',
			              ] ),
			         Field::make( 'text', 'invoice_offers' )
		         ] );
	}

	public function load_carbon_fields(): void {
		if ( ! class_exists( 'Carbon_Fields\Carbon_Fields' ) ) {
			get_template_part( 'vendor/autoload' );
			\Carbon_Fields\Carbon_Fields::boot();
		}
	}

}

Carbon::get_instance();