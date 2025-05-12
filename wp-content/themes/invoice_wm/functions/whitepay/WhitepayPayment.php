<?php

namespace InvoiceWM\pay;

class WhitepayPayment {
	private $apiUrl = 'https://api.whitepay.com';
	private $apiToken;
	private $currencyId = '44415cca-78a8-4df9-bc19-2409ab859a17'; // Наприклад, USDT
	private $network = 'TRX'; // Наприклад, Tron network


	public function __construct() {
		$this->apiToken   = carbon_get_theme_option( 'whitepay_key' );
		$this->currencyId = carbon_get_theme_option( 'whitepay_currency' );
		$this->network    = carbon_get_theme_option( 'whitepay_network' );
	}

	private function test(): void {
		if ( ! $this->apiToken ) {
			error_log( 'apiToken empty' );
			throw new \InvalidArgumentException( "apiToken empty" );
		}
		if ( ! $this->currencyId ) {
			error_log( 'currencyId empty' );
			throw new \InvalidArgumentException( "currencyId empty" );
		}
		if ( ! $this->network ) {
			error_log( 'network empty' );
			throw new \InvalidArgumentException( "network empty" );
		}
	}

	/**
	 * Створює замовлення для оплати
	 *
	 * @param string $orderId Унікальний номер замовлення
	 * @param string $description Опис замовлення
	 * @param float $amount Сума замовлення
	 *
	 * @return array Результат запиту (order_id або помилка)
	 */
	public function createPayment( string $orderId, string $description, float $amount ): array {
		$this->test();
		$endpoint = '/v1/orders';
		$data     = [
			'amount'            => $amount,
			'currency_id'       => $this->currencyId,
			'external_order_id' => $orderId,
			'description'       => $description,
			'method'            => 'WALLET',
			'network'           => $this->network
		];

		$response = $this->makeRequest( 'POST', $endpoint, $data );
		carbon_set_post_meta( $orderId, 'whitepay_log', json_encode( $response ) );
		if ( isset( $response['order']['id'] ) ) {
			return [
				'success'  => true,
				'order_id' => $response['order']['id']
			];
		}

		return [
			'success' => false,
			'error'   => $response['message'] ?? 'Failed to create payment'
		];
	}

	/**
	 * Виконує HTTP-запит до API Whitepay
	 *
	 * @param string $method HTTP-метод (GET, POST тощо)
	 * @param string $endpoint Ендпоінт API
	 * @param array $data Дані для запиту (для POST)
	 *
	 * @return array Декодована JSON-відповідь
	 */
	private function makeRequest( string $method, string $endpoint, array $data = [] ): array {
		$this->test();
		$url     = $this->apiUrl . $endpoint;
		$headers = [
			'Authorization: Bearer ' . $this->apiToken,
			'Content-Type: application/json',
			'Accept: application/json'
		];

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );

		if ( $method === 'POST' ) {
			curl_setopt( $ch, CURLOPT_POST, true );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $data ) );
		}

		$response = curl_exec( $ch );
		$httpCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
		curl_close( $ch );

		$result = json_decode( $response, true );

		if ( $httpCode >= 400 ) {
			return [
				'message'     => $result['message'] ?? 'API request failed',
				'status_code' => $httpCode
			];
		}

		return $result ?: [];
	}

	public function checkPaymentStatus( string $orderId ): array {
		$this->test();
		$endpoint = "/v1/orders/{$orderId}";
		$response = $this->makeRequest( 'GET', $endpoint );

		return isset( $response['status'] ) ? [
			'success' => true,
			'status'  => $response['status']
		] : [
			'success' => false,
			'error'   => $response['message'] ?? 'Failed to check status'
		];
	}

	public function renderForm( string $order_ID, $safety = false, $button_text = '' ): void {
		$this->test();
		$button_text = $button_text ?: _l( 'ПЕРЕЙТИ ДО ОПЛАТИ', 1 );
		$attr        = 'action="' . site_url( 'wp-admin/admin-ajax.php' ) . '"';
		$button_attr = 'class="button button__checkout"';
		if ( $safety ) {
			$attr        = 'data-action="' . site_url( 'wp-admin/admin-ajax.php' ) . '" disabled';
			$button_attr = 'class="button button__checkout not-active" disabled';
		}
		?>
        <form method="post" class="checkout-form bill-checkout form-js" novalidate id="whitepay" <?php echo $attr; ?>>
            <input type="hidden" name="action" value="create_whitepay_payment">
            <input type="hidden" name="order_id" value="<?php echo $order_ID; ?>">
            <button <?php echo $button_attr; ?>><?php echo esc_html( $button_text ) ?></button>
			<?php wp_nonce_field( 'create_whitepay_payment', 'true_nonce', true, true ); ?>
        </form>
		<?php
	}
}