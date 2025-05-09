<?php

namespace InvoiceWM\core;

use InvoiceWM\Components\Offers;

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

		add_action( 'wp_ajax_nopriv_new_invoice', [ $this, 'new_invoice' ] );
		add_action( 'wp_ajax_new_invoice', [ $this, 'new_invoice' ] );

		add_action( 'wp_ajax_nopriv_get_edit_invoice_form', [ $this, 'get_edit_invoice_form' ] );
		add_action( 'wp_ajax_get_edit_invoice_form', [ $this, 'get_edit_invoice_form' ] );

		add_action( 'wp_ajax_nopriv_edit_invoice', [ $this, 'edit_invoice' ] );
		add_action( 'wp_ajax_edit_invoice', [ $this, 'edit_invoice' ] );

		add_action( 'wp_ajax_nopriv_track_live_users', [ $this, 'track_live_users' ] );
		add_action( 'wp_ajax_track_live_users', [ $this, 'track_live_users' ] );

		add_action( 'wp_ajax_nopriv_remove_live_user', [ $this, 'remove_live_user' ] );
		add_action( 'wp_ajax_remove_live_user', [ $this, 'remove_live_user' ] );

		add_action( 'wp_ajax_nopriv_get_views', [ $this, 'get_views' ] );
		add_action( 'wp_ajax_get_views', [ $this, 'get_views' ] );

		add_action( 'wp_ajax_nopriv_save_payment_method', [ $this, 'save_payment_method' ] );
		add_action( 'wp_ajax_save_payment_method', [ $this, 'save_payment_method' ] );
	}

	public function save_payment_method(): void {
		$id             = filter_input( INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT );
		$payment_method = filter_input( INPUT_POST, 'payment_method' );
		if ( ! $payment_method ) {
			$this->send_error( 'error empty value' );
		}
		if ( ! get_post( $id ) ) {
			$this->send_error( 'order not found' );
		}
		carbon_set_post_meta( $id, 'invoice_pay_method', $payment_method );
		$this->send_response( [
			'success'            => true,
			'invoice_pay_method' => carbon_get_post_meta( $id, 'invoice_pay_method' )
		] );
	}

	public function get_views(): void {
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			$this->send_error( 'User error' );
		}
		$post_id = isset( $_GET['post_id'] ) ? intval( $_GET['post_id'] ) : 0;
		if ( ! $post_id ) {
			$this->send_error( 'Invalid request' );
		}
		$views     = carbon_get_post_meta( $post_id, 'bill_views' ) ?: [];
		$views_num = count( $views );
		$viewing   = get_number_viewing( $post_id );
		$html      = "
        <div class='modal-window__title modal__title text-center'>Всього переглядів: $views_num</div>
        <div class='modal-window__text modal__text text-center'>Переглядають зараз: $viewing</div>
        ";
		if ( $views ) {
			$html .= '<div class="views">';
			foreach ( $views as $view ) {
				$ip      = $view['ip'];
				$client  = $view['client'];
				$time    = $view['time'];
				$ip_city = $view['ip_city'];
				$string  = $ip;
				if ( $ip_city ) {
					$string .= ' <br> ' . $ip_city;
				}
				if ( $ip && $client && $time ) {
					$html .= '<div class="views-row">';
					$html .= "<div class='views-column'>$time</div>";
					$html .= "<div class='views-column'>$string</div>";
					$html .= "<div class='views-column'>$client</div>";
					$html .= '</div>';
				}

			}
			$html .= '</div>';
		}
		$this->send_response( [
			'views_html' => $html
		] );
	}

	public function remove_live_user(): void {
		$post_id    = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
		$session_id = isset( $_POST['session_id'] ) ? sanitize_text_field( $_POST['session_id'] ) : '';

		if ( ! $post_id || ! $session_id ) {
			$this->send_error( 'Invalid request' );
		}

		$user_key   = 'live_users_' . $post_id;
		$live_users = get_transient( $user_key );

		if ( $live_users && isset( $live_users[ $session_id ] ) ) {
			unset( $live_users[ $session_id ] );
			set_transient( $user_key, $live_users, 60 );
		}

		wp_send_json_success( [ 'message' => 'User removed' ] );
	}

	public function track_live_users(): void {
		if ( is_bot() ) {
			$this->send_error( 'Invalid request' );
		}

		if ( ! session_id() ) {
			session_start();
		}
		$post_id    = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
		$session_id = isset( $_POST['session_id'] ) ? sanitize_text_field( $_POST['session_id'] ) : '';
		if ( ! $post_id || ! $session_id ) {
			$this->send_error( 'Invalid request' );
		}
		$user_key     = 'live_users_' . $post_id;
		$current_time = time();
		$live_users   = get_transient( $user_key );
		if ( ! $live_users ) {
			$live_users = [];
		}
		if ( ! get_current_user_id() ) {
			$live_users[ $session_id ] = $current_time;
		}
		foreach ( $live_users as $session => $timestamp ) {
			if ( $current_time - $timestamp > 30 ) {
				unset( $live_users[ $session ] );
			}
		}
		set_transient( $user_key, $live_users, 60 );
		wp_send_json_success( [ 'users' => count( $live_users ) ] );
	}

	function edit_invoice(): void {
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			$this->send_error( 'User error' );
		}
		$nonce = filter_input( INPUT_POST, 'true_nonce' );
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'edit_invoice' ) ) {
			$this->send_error( 'Error request nonce' );
		}
		$id = filter_input( INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT );
		if ( ! $id ) {
			$this->send_error( 'ID error' );
		}
		$invoice = get_post( $id );
		if ( ! $invoice ) {
			$this->send_error( 'ID error' );
		}
		$invoice_status = carbon_get_post_meta( $id, 'invoice_status' ) ?: 'not_paid';
		if ( $invoice_status == 'paid' ) {
			$this->send_error( 'Вже оплачено!' );
		}
		$post_title      = filter_input( INPUT_POST, 'post_title' );
		$price           = filter_input( INPUT_POST, 'price', FILTER_VALIDATE_INT );
		$currency        = filter_input( INPUT_POST, 'currency' );
		$lang            = filter_input( INPUT_POST, 'lang' );
		$text            = filter_input( INPUT_POST, 'text' );
		$methods         = filter_input( INPUT_POST, 'method', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$selected_offers = filter_input( INPUT_POST, 'selected_offers' );
		if ( ! $post_title ) {
			$this->send_error( 'Введіть заголовок' );
		}
		if ( ! $price ) {
			$this->send_error( 'Введіть ціну' );
		}
		if ( ! $methods ) {
			$this->send_error( 'Оберіть метод оплати' );
		}
		$selected  = $selected_offers ? explode( ',', $selected_offers ) : [];
		$selected  = $selected ? array_map( 'intval', $selected ) : [];
		$post_data = array(
			'ID'           => $id,
			'post_type'    => 'bill',
			'post_title'   => $post_title,
			'post_status'  => 'publish',
			'post_content' => $text,
		);
		$_id       = wp_update_post( $post_data );
		$_post     = get_post( $_id );
		if ( ! $_post ) {
			$this->send_error( 'Error inserting post' );
		}
		carbon_set_post_meta( $_id, 'invoice_status', 'not_paid' );
		carbon_set_post_meta( $_id, 'invoice_sum', $price );
		carbon_set_post_meta( $_id, 'invoice_currency', $currency );
		carbon_set_post_meta( $_id, 'invoice_pay_methods', $methods );
		carbon_set_post_meta( $_id, 'invoice_pay_method', $methods[0] );
		carbon_set_post_meta( $_id, 'invoice_offers', $selected_offers );
		if ( function_exists( 'pll_set_post_language' ) ) {
			pll_set_post_language( $_id, $lang );
		}
		$this->send_response( [
			'id'  => $_id,
			'url' => get_the_permalink( $_id ),
		] );
	}

	function get_edit_invoice_form(): void {
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			$this->send_error( 'User error' );
		}
		$id = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );
		if ( ! $id ) {
			$this->send_error( 'ID error' );
		}
		$invoice = get_post( $id );
		if ( ! $invoice ) {
			$this->send_error( 'ID error' );
		}
		$invoice_status = carbon_get_post_meta( $id, 'invoice_status' ) ?: 'not_paid';
		if ( $invoice_status == 'paid' ) {
			$this->send_error( 'Вже оплачено!' );
		}
		$currency_selected = carbon_get_post_meta( $id, 'invoice_currency' );
		$pay_methods       = carbon_get_post_meta( $id, 'invoice_pay_methods' ) ?: [];
		$invoice_offers    = carbon_get_post_meta( $id, 'invoice_offers' ) ?: '';
		$offers            = $invoice_offers ? Offers::get_offers( 'https://offer.web-mosaica.art/', '', $invoice_offers ) : [];
		ob_start();
		?>
        <form method="post" novalidate class="form-js form form-edit-invoice no-reset" id="edit-invoice">
        <label class="form-label">
            <span class="form-label-head">Назва або заголовок</span>
            <input type="text" name="post_title" required value="<?php echo esc_attr( get_the_title( $id ) ) ?>">
        </label>
        <label class="form-label">
            <span class="form-label-head">Ціна</span>
            <input type="number" min="1" name="price" required
                   value="<?php echo esc_attr( carbon_get_post_meta( $id, 'invoice_sum' ) ) ?>">
        </label>
		<?php if ( $_currencies = get_invoice_currency() ): ?>
            <label class="form-label">
                <span class="form-label-head">Валюта</span>
                <select name="currency" class="">
					<?php
					foreach ( $_currencies as $currency_code => $currency ) {
						$inner = esc_html( $currency );
						$attr  = $currency_code == $currency_selected ? ' selected="selected"' : '';
						echo "<option $attr >$inner</option>";
					}
					?>
                </select>
            </label>
		<?php endif; ?>
		<?php if ( function_exists( 'pll_languages_list' ) ):
			$post_language = pll_get_post_language( $id );
			if ( $languages = pll_languages_list() ):
				?>
                <label class="form-label">
                    <span class="form-label-head">Мова</span>
                    <select name="lang" class="">
						<?php
						foreach ( $languages as $language ) {
							$inner = esc_html( $language );
							$attr  = $post_language == $language ? ' selected="selected"' : '';
							echo "<option $attr >$inner</option>";
						}
						?>
                    </select>
                </label>
			<?php endif; ?>
		<?php endif; ?>
        <label class="form-label">
            <span class="form-label-head">Опис</span>
            <textarea name="text"><?php echo esc_attr( strip_tags( get_content_by_id( $id ) ) ) ?></textarea>
        </label>
		<?php the_payment_methods( $pay_methods ) ?>
        <div class="form-label">
            <label for="autocomplete-offer" class="form-label-head">Офери</label>
            <div class="form-autocomplete">
                <input type="hidden" name="selected_offers" value="<?php echo esc_attr( $invoice_offers ) ?>">
                <input type="text" placeholder="Введіть назву комерційної пропозиції" id="autocomplete-offer"
                       class="autocomplete-offer">
                <div class="form-list form-autocomplete-list"
                     style="<?php echo ! $offers ? 'display:none;' : ''; ?>">
					<?php
					if ( $offers ) {
						foreach ( $offers as $_id => $item ) {
							$attr = 'checked';
							?>
                            <label class="form-list-item">
                                <input type="checkbox" name="offer_id[]" class="offer-checkbox"
									<?php echo $attr ?>
                                       value="<?php echo $_id ?>">
                                <span class="icon"></span>
                                <span class="text"><?php echo $item['title'] ?></span>
                            </label>
							<?php
						}
					}
					?>
                </div>
            </div>
        </div>
        <div class="modal-buttons">
            <a href="#" class="button button--light close-fancybox-modal">Відміна</a>
            <button class="button">Редагувати</button>
        </div>
        <input type="hidden" name="action" value="edit_invoice">
        <input type="hidden" name="id" value="<?php echo esc_attr( $id ) ?>">
		<?php wp_nonce_field( 'edit_invoice', 'true_nonce', true, true ); ?>
        </form><?php
		$content = ob_get_clean();
		$this->send_response( [
			'edit_form_html' => $content
		] );
	}

	function new_invoice(): void {
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			$this->send_error( 'User error' );
		}
		$nonce = filter_input( INPUT_POST, 'true_nonce' );
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'new_invoice' ) ) {
			$this->send_error( 'Error request nonce' );
		}
		$post_title      = filter_input( INPUT_POST, 'post_title' );
		$price           = filter_input( INPUT_POST, 'price', FILTER_VALIDATE_INT );
		$currency        = filter_input( INPUT_POST, 'currency' );
		$lang            = filter_input( INPUT_POST, 'lang' );
		$text            = filter_input( INPUT_POST, 'text' );
		$methods         = filter_input( INPUT_POST, 'method', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$selected_offers = filter_input( INPUT_POST, 'selected_offers' );
		if ( ! $post_title ) {
			$this->send_error( 'ВВедіть заголовок' );
		}
		if ( ! $price ) {
			$this->send_error( 'Введіть ціну' );
		}
		if ( ! $methods ) {
			$this->send_error( 'Оберіть метод оплати' );
		}
		$selected  = $selected_offers ? explode( ',', $selected_offers ) : [];
		$selected  = $selected ? array_map( 'intval', $selected ) : [];
		$post_data = array(
			'post_type'    => 'bill',
			'post_title'   => $post_title,
			'post_status'  => 'publish',
			'post_content' => $text,
		);
		$_id       = wp_insert_post( $post_data );
		$_post     = get_post( $_id );
		if ( ! $_post ) {
			$this->send_error( 'Error inserting post' );
		}
		carbon_set_post_meta( $_id, 'invoice_status', 'not_paid' );
		carbon_set_post_meta( $_id, 'invoice_sum', $price );
		carbon_set_post_meta( $_id, 'invoice_currency', $currency );
		carbon_set_post_meta( $_id, 'invoice_pay_methods', $methods );
		carbon_set_post_meta( $_id, 'invoice_offers', $selected_offers );
		if ( function_exists( 'pll_set_post_language' ) ) {
			pll_set_post_language( $_id, $lang );
		}
		$life_time_bill = carbon_get_theme_option( 'life_time_bill' ) ?: 5;
		$deletion_time  = HOUR_IN_SECONDS * 24 * intval( $life_time_bill );
		CustomCron::schedule_post_deletion( $_id, $deletion_time );
		$this->send_response( [
			'id'  => $_id,
			'url' => get_the_permalink( $_id ),
		] );
	}

	public function get_offers_html(): void {
		$val      = filter_input( INPUT_POST, 'val' );
		$selected = filter_input( INPUT_POST, 'selected' );
		if ( ! $val ) {
			$this->send_error( 'error empty value' );
		}
		$selected = $selected ? explode( ',', $selected ) : [];
		$selected = $selected ? array_map( 'intval', $selected ) : [];
		$res      = Offers::get_offers( 'https://offer.web-mosaica.art/', $val );
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

	private function send_error( string $message, string $text = '' ): void {
		$this->send_response( [
			'type'     => 'error',
			'msg'      => $message,
			'msg_text' => $text,
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
