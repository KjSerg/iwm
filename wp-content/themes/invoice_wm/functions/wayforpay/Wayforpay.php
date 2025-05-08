<?php

namespace InvoiceWM\pay;

class Wayforpay {
	private mixed $order_id;
	/**
	 * @var mixed|null
	 */
	private mixed $key;
	/**
	 * @var mixed|null
	 */
	private mixed $account;
	/**
	 * @var mixed|null
	 */
	private mixed $domain;

	public function __construct() {
		$this->set_key();
		$this->set_data();
	}

	public function set_order( $order_id ): void {
		$this->order_id = intval( $order_id );
	}


	public function render_form(): void {
		$this->test();
		$id         = $this->order_id;
		$account    = $this->account;
		$domain     = $this->domain;
		$return_url = get_the_permalink( $id );
		$price      = carbon_get_post_meta( $id, 'invoice_sum' );
		$currency   = carbon_get_post_meta( $id, 'invoice_currency' ) ?: 'UAH';
		$price      = floatval( $price );
		$currency   = strtoupper( esc_attr( $currency ) );
		$title      = esc_attr( get_the_title() );
		$timestamp  = current_time( 'timestamp' );
		$data       = [
			$account,
			$domain,
			"WMI$id",
			$timestamp,
			$price,
			$currency,
			$title,
			1,
			$price
		];
		$string     = implode( ';', $data );
		$key        = $this->key;
		$hash       = hash_hmac( "md5", $string, $key );
		$this->set_signature( $hash );
		?>
        <form method="post" action="https://secure.wayforpay.com/pay" accept-charset="utf-8">
            <input type="hidden" name="merchantAccount" value="<?php echo esc_attr( $account ) ?>">
            <input type="hidden" name="merchantAuthType" value="SimpleSignature">
            <input type="hidden" name="merchantDomainName" value="<?php echo esc_attr( $domain ) ?>">
            <input type="hidden" name="orderReference" value="WMI<?php echo $id ?>">
            <input type="hidden" name="orderDate" value="<?php echo $timestamp ?>">
            <input type="hidden" name="amount" value="<?php echo $price ?>">
            <input type="hidden" name="currency" value="<?php echo $currency ?>">
            <input type="hidden" name="productName[]" value="<?php echo $title ?>">
            <input type="hidden" name="productPrice[]" value="<?php echo $price ?>">
            <input type="hidden" name="productCount[]" value="1">
            <input type="hidden" name="merchantSignature" value="<?php echo $hash ?>">
            <input type="hidden" name="serviceUrl" value="<?php echo esc_attr( $this->get_service_url() ) ?>">
            <input type="hidden" name="returnUrl" value="<?php echo esc_attr( $return_url ) ?>">
            <input type="hidden" name="orderLifetime" value="<?php echo( 3600 * 2 ) ?>">
            <button class="button">Оплатити <?php echo $price . $currency ?></button>
        </form>
		<?php
	}

	private function get_service_url(): string {
		return site_url() . '/wp-json/invoice_wm/v1/wayforpay-payment-webhook/';
	}

	private function set_key(): void {
		$this->key = carbon_get_theme_option( 'wayforpay_key' );
	}

	private function set_data(): void {
		$this->account = carbon_get_theme_option( 'wayforpay_account' );
		$this->domain  = carbon_get_theme_option( 'wayforpay_domain' );
	}

	private function test(): void {
		if ( ! $this->key ) {
			error_log( 'Key empty' );
			throw new \InvalidArgumentException( "Key empty" );
		}
		if ( ! $this->account ) {
			error_log( 'Account empty' );
			throw new \InvalidArgumentException( "Account empty" );
		}
		if ( ! $this->domain ) {
			error_log( 'Domain empty' );
			throw new \InvalidArgumentException( "Domain empty" );
		}
		if ( ! $this->order_id ) {
			error_log( 'Order Empty' );
			throw new \InvalidArgumentException( "Order empty" );
		}
	}

	private function set_signature( bool|string $hash ): void {
		carbon_set_post_meta( $this->order_id, 'wayforpay_signature', $hash );
	}
}