<?php

namespace InvoiceWM\core;
class Ajax {
	private static ?self $instance = null;

	private function __construct() {
		$this->initialize();
	}

	public static function get_instance(): self {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function initialize(): void {
		add_action( 'wp_ajax_nopriv_change_bill', [ $this, 'change_bill' ] );
		add_action( 'wp_ajax_change_bill', [ $this, 'change_bill' ] );

		add_action( 'wp_ajax_nopriv_get_offers_html', [ $this, 'get_offers_html' ] );
		add_action( 'wp_ajax_get_offers_html', [ $this, 'get_offers_html' ] );
	}

	public function get_offers_html(): void {
		$val      = filter_input( INPUT_POST, 'val' );
		$selected = filter_input( INPUT_POST, 'selected' );
		if ( ! $val ) {
			$this->send_error( 'error empty value' );
		}
		$selected = $selected ? explode( ',', $selected ) : [];
		$selected = $selected ? array_map( 'intval', $selected ) : [];
		$res      = self::get_pages_from_external_site( 'https://offer.web-mosaica.art/', $val );
		if ( $res ) {
			foreach ( $res as $id => $item ) {
				$attr = in_array( $id, $selected ) ? 'checked' : '';
				?>
                <label class="form-list-item">
                    <input type="checkbox" name="offer_id[]" class="offer-checkbox"
						<?php echo $attr ?>
                           value="<?php echo $id ?>">
                    <span class="icon"></span>
                    <span class="text"><?php echo $item['title'] ?></span>
                </label>
				<?php
			}
		}
		die();
	}

	public static function get_pages_from_external_site( $external_site_url, $search = '' ) {
		$url = trailingslashit( $external_site_url ) . 'wp-json/CommerciaOffer/v1/pages/';

		if ( ! empty( $search ) ) {
			$url = add_query_arg( 'search', urlencode( $search ), $url );
		}

		$response = wp_remote_get( $url );

		if ( is_wp_error( $response ) ) {
			return [];
		}

		return json_decode( wp_remote_retrieve_body( $response ), true );
	}

	public function change_bill(): void {
		$response = [];
		$nonce    = filter_input( INPUT_POST, 'true_nonce' );
		$user_id  = get_current_user_id();
		if ( ! $user_id ) {
			$this->send_error( 'User error' );
		}
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'change_bill' ) ) {
			$this->send_error( 'Error request nonce' );
		}
		$id               = filter_input( INPUT_POST, 'bill_id', FILTER_SANITIZE_NUMBER_INT ) ?: 5;
		$invoice_sum      = filter_input( INPUT_POST, 'invoice_sum', FILTER_SANITIZE_NUMBER_INT );
		$invoice_currency = filter_input( INPUT_POST, 'invoice_currency' );
		$post_title       = filter_input( INPUT_POST, 'post_title' ) ?: '';
		if ( ! $id || ! get_post( $id ) ) {
			$this->send_error( 'Error bill_id' );
		}
		if ( ! $invoice_sum || $invoice_currency == '' ) {
			$this->send_error( 'Error $invoice_sum or $invoice_currency' );
		}
		carbon_set_post_meta( $id, 'invoice_sum', $invoice_sum );
		carbon_set_post_meta( $id, 'invoice_currency', $invoice_currency );
		$_id = wp_update_post( [
			'ID' => $id,
		] );
		$this->send_response( [
			'result'           => 'success',
			'id'               => $id,
			'res'              => $_id,
			'invoice_sum'      => $invoice_sum,
			'invoice_currency' => $invoice_currency,
		] );
	}

	private function send_error( string $message ): void {
		$this->send_response( [
			'type'     => 'error',
			'msg'      => $message,
			'msg_text' => $message,
		] );
	}

	private function send_response( array $response ): void {
		echo json_encode( $response );
		wp_die();
	}

	public static function my_handle_attachment( $file_handler, $post_id = 0, $set_thu = false ): \WP_Error|int {
		if ( $_FILES[ $file_handler ][ carbon_get_theme_option( 'error_string_2' ) ] !== UPLOAD_ERR_OK ) {
			__return_false();
		}

		require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
		require_once( ABSPATH . "wp-admin" . '/includes/file.php' );
		require_once( ABSPATH . "wp-admin" . '/includes/media.php' );

		return media_handle_upload( $file_handler, $post_id );
	}


}

Ajax::get_instance();
