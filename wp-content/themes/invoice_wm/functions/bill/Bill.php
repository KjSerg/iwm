<?php

namespace InvoiceWM\core;

use InvoiceWM\Components\Offers;

class Bill {
	/**
	 * @var false|int|mixed
	 */
	private mixed $id;

	public function __construct( $id = false ) {
		$id = $id ?: get_the_ID();
		if ( get_post_type( $id ) !== 'bill' ) {
			error_log( 'post type error' );
			throw new \InvalidArgumentException( "post type error" );
		}
		$this->id = $id;
	}

	public function render_offers(): void {
		$invoice_offers = carbon_get_post_meta( $this->id, 'invoice_offers' );
		$offers         = $invoice_offers ? Offers::get_offers( 'https://offer.web-mosaica.art/', '', $invoice_offers ) : [];
		if ( $offers ) {
			echo '<ul class="bill-offers">';
			foreach ( $offers as $offer ) {
				?>
                <li class="bill-offers-item">
                    <a href="<?php echo esc_url( $offer['link'] ) ?>" target="_blank" rel="nofollow"
                       class="bill-offers-item__link">
                                <span class="bill-offers-item__icon"><svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                          height="24" viewBox="0 0 24 24" fill="none">
  <mask id="mask0_17_494" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="24" height="24">
    <rect width="24" height="24" fill="#D9D9D9"/>
  </mask>
  <g mask="url(#mask0_17_494)">
    <path d="M10.65 18.8553H12.35V17.75H13.7667C14.0079 17.75 14.2098 17.6706 14.3724 17.5118C14.5353 17.3532 14.6167 17.1563 14.6167 16.9211V13.6053C14.6167 13.37 14.5353 13.1731 14.3724 13.0145C14.2098 12.8557 14.0079 12.7763 13.7667 12.7763H10.0833V11.1184H14.6167V9.46053H12.35V8.35526H10.65V9.46053H9.23333C8.99212 9.46053 8.7902 9.53992 8.62757 9.69871C8.46474 9.85732 8.38333 10.0542 8.38333 10.2895V13.6053C8.38333 13.8405 8.46474 14.0374 8.62757 14.196C8.7902 14.3548 8.99212 14.4342 9.23333 14.4342H12.9167V16.0921H8.38333V17.75H10.65V18.8553ZM5.04878 23C4.47626 23 3.99167 22.8066 3.595 22.4197C3.19833 22.0329 3 21.5603 3 21.002V3.99804C3 3.4397 3.19833 2.96711 3.595 2.58026C3.99167 2.19342 4.47626 2 5.04878 2H13.527L20 8.31271V21.002C20 21.5603 19.8017 22.0329 19.405 22.4197C19.0083 22.8066 18.5237 23 17.9512 23H5.04878ZM5.04878 21.3421H17.9512C18.0385 21.3421 18.1184 21.3066 18.1909 21.2357C18.2636 21.165 18.3 21.0871 18.3 21.002V9.01842L12.8033 3.65789H5.04878C4.96152 3.65789 4.88162 3.69335 4.80908 3.76428C4.73636 3.83501 4.7 3.91293 4.7 3.99804V21.002C4.7 21.0871 4.73636 21.165 4.80908 21.2357C4.88162 21.3066 4.96152 21.3421 5.04878 21.3421Z"
          fill="#5261DF"/>
  </g>
</svg></span>
						<?php echo _l( 'Комерційна пропозиція', 1 ) . ': ' . esc_html( $offer['title'] ) ?>
                    </a>
                </li>
				<?php
			}
			echo '</ul>';
		}
	}

	public function render_payment_methods(): void {
		$payment_methods       = carbon_get_theme_option( 'payment_methods' );
		$order_payment_methods = carbon_get_post_meta( $this->id, 'invoice_pay_methods' );
		if ( $payment_methods ): ?>
            <div class="bill-method">
                <div class="bill-method__title">
					<?php _l( 'Виберіть спосіб оплати' ); ?>
                </div>
                <label class="bill-method-box">
                    <select name="payment_method" class="select" id="payment-method">
						<?php foreach ( $payment_methods as $method ) {
							$attr = ! in_array( esc_attr( $method['title'] ), $order_payment_methods ) ? 'disabled' : '';
							echo "<option $attr data-icon=" . esc_attr( _u( $method['image'], 1 ) ) . " value=" . esc_attr( $method['title'] ) . ">" . esc_html( $method['title'] ) . "</option>";
						} ?>
                    </select>
                </label>
            </div>
		<?php endif;
	}

	public function render_price() {
		$price    = carbon_get_post_meta( $this->id, 'invoice_sum' );
		$currency = carbon_get_post_meta( $this->id, 'invoice_currency' ) ?: 'UAH';
		?>
        <div class="bill-price">
            <p><?php _l( 'Сума' ) ?></p>
            <strong><?php echo get_price_formated( $price, $currency ) ?></strong>
        </div>
		<?php
	}


}