<?php

use InvoiceWM\Components\Offers;

get_header();
$var                   = variables();
$set                   = $var['setting_home'];
$assets                = $var['assets'];
$url                   = $var['url'];
$url_home              = $var['url_home'];
$id                    = get_the_ID();
$title                 = esc_attr( get_the_title() );
$invoice_status        = carbon_get_post_meta( $id, 'invoice_status' );
$post_status           = get_post_status( $id );
$invoice_offers        = carbon_get_post_meta( $id, 'invoice_offers' );
$offers                = $invoice_offers ? Offers::get_offers( 'https://offer.web-mosaica.art/', '', $invoice_offers ) : [];
$payment_methods       = carbon_get_theme_option( 'payment_methods' );
$order_payment_methods = carbon_get_post_meta( $id, 'invoice_pay_methods' );
$price                 = carbon_get_post_meta( $id, 'invoice_sum' );
$currency              = carbon_get_post_meta( $id, 'invoice_currency' ) ?: 'UAH';
$timestamp             = wp_next_scheduled( 'delete_scheduled_post', [ $id ] );
$time                  = date( 'd-m-Y H:i:s', $timestamp );
$current_time          = current_time( 'd-m-Y H:i:s' );
$policy_page_id        = (int) get_option( 'wp_page_for_privacy_policy' );
$bill                  = new \InvoiceWM\core\Bill( $id );
$wayforpay_status      = carbon_get_post_meta( $id, 'wayforpay_transaction_status' ) ?: '';
$payment_attempt_test  = $wayforpay_status != '';
?>
    <section class="head-section section"></section>
    <section class="section bill-section">
        <div class="container">
            <div class="bill-box">
				<?php if ( $invoice_status == 'paid' ) : ?>
                    <div class="bill-box-body">
                        <div class="bill-box__icon">
                       <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 64 64" fill="none">
  <mask id="mask0_21_583" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="64" height="64">
    <rect width="64" height="64" fill="#D9D9D9"/>
  </mask>
  <g mask="url(#mask0_21_583)">
    <path d="M25.4668 41.3747L48.5028 18.3387C48.8992 17.9418 49.3632 17.7392 49.8948 17.7307C50.4263 17.7223 50.899 17.9249 51.3128 18.3387C51.7265 18.7525 51.9334 19.2276 51.9334 19.7641C51.9334 20.3009 51.7265 20.7763 51.3128 21.1901L27.1541 45.3901C26.6719 45.8718 26.1094 46.1127 25.4668 46.1127C24.8241 46.1127 24.2617 45.8718 23.7794 45.3901L12.6461 34.2567C12.2497 33.8598 12.0541 33.3887 12.0594 32.8434C12.0643 32.2985 12.2737 31.8192 12.6874 31.4054C13.1012 30.9916 13.5763 30.7847 14.1128 30.7847C14.6497 30.7847 15.125 30.9916 15.5388 31.4054L25.4668 41.3747Z"
          fill="#219653"/>
  </g>
</svg>
                        </div>
                        <div class="bill__title">
                            <p style="text-align: center"><?php _l( 'Оплата успішна' ) ?></p>
                        </div>
                        <div class="bill__description text">
                            <p style="text-align: center"><?php _l( 'Дякуємо! Ваш платіж було успішно проведено' ) ?></p>
                        </div>
                    </div>
				<?php else: ?>
					<?php if ( $post_status == 'publish' ): ?>
						<?php if ( ! $payment_attempt_test ): ?>
                            <div class="bill-box-head">
                                <div class="bill__title">
									<?php _l( 'Оплата послуг' ) ?>
                                </div>
                                <div class="bill__description text">
									<?php the_post();
									the_content(); ?>
                                </div>
								<?php $bill->render_offers(); ?>
                            </div>
                            <div class="bill-box-body">
								<?php $bill->render_payment_methods(); ?>
								<?php $bill->render_price(); ?>
								<?php if ( $policy_page_id ): ?>
                                    <div class="form-consent bill-consent">
                                        <label class="form-consent-box">
                                            <input type="checkbox" class="bill-consent__input" name="consent"
                                                   value="yes">
                                            <span></span>
                                        </label>
                                        <div class="form-consent-text">
                                            <p><?php _l( 'Приймаю умови' ) ?> <a
                                                        href="<?php echo esc_url( get_the_permalink( $policy_page_id ) ) ?>"><?php echo esc_html( get_the_title( $policy_page_id ) ) ?></a>.
                                            </p>
                                        </div>
                                    </div>
									<?php
									$wayforpay = new \InvoiceWM\pay\Wayforpay();
									$wayforpay->set_order( $id );
									$wayforpay->render_form( true );
								else:
									$wayforpay = new \InvoiceWM\pay\Wayforpay();
									$wayforpay->set_order( $id );
									$wayforpay->render_form();
								endif; ?>
                                <div class="bill-finish"
                                     data-time="<?php echo $time; ?>"
                                     data-timestamp="<?php echo $timestamp; ?>">
									<?php echo _l( 'Посилання дійсне ще:', 1 ) . ' ' . get_time_diff( $time, $current_time ); ?>
                                </div>
                            </div>
						<?php else: ?>
                            <div class="bill-box-body">
                                <div class="bill-box__icon">
                       <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 60 60" fill="none">
  <mask id="mask0_21_809" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="60" height="60">
    <rect width="60" height="60" fill="#D9D9D9"/>
  </mask>
  <g mask="url(#mask0_21_809)">
    <path d="M29.8744 35.0959C29.3427 35.0959 28.8975 34.9162 28.5387 34.5566C28.1796 34.1974 28 33.7522 28 33.2209V15.7209C28 15.1897 28.1798 14.7443 28.5394 14.3847C28.899 14.0255 29.3444 13.8459 29.8756 13.8459C30.4073 13.8459 30.8525 14.0255 31.2113 14.3847C31.5704 14.7443 31.75 15.1897 31.75 15.7209V33.2209C31.75 33.7522 31.5702 34.1974 31.2106 34.5566C30.851 34.9162 30.4056 35.0959 29.8744 35.0959ZM29.8744 46.1534C29.3427 46.1534 28.8975 45.9737 28.5387 45.6141C28.1796 45.2545 28 44.8091 28 44.2778C28 43.7462 28.1798 43.3009 28.5394 42.9422C28.899 42.583 29.3444 42.4034 29.8756 42.4034C30.4073 42.4034 30.8525 42.5832 31.2113 42.9428C31.5704 43.3024 31.75 43.7478 31.75 44.2791C31.75 44.8107 31.5702 45.2559 31.2106 45.6147C30.851 45.9739 30.4056 46.1534 29.8744 46.1534Z"
          fill="#C9085F"/>
  </g>
</svg>
                                </div>
                                <div class="bill__title">
                                    <p style="text-align: center"><?php _l( 'Помилка оплати' ) ?></p>
                                </div>
                                <div class="bill__description text">
                                    <p style="text-align: center"><?php _l( 'На жаль, платіж не було здійснено' ) ?></p>
                                </div>
								<?php
								if ( $wayforpay_repay_url = carbon_get_post_meta( $id, 'wayforpay_repay_url' ) ) {
									?>
                                    <a href="<?php echo esc_url( $wayforpay_repay_url ) ?>" class="bill__button button">
										<?php _l( 'Спробувати ще раз' ) ?>
                                    </a>
									<?php
								} else {

									$wayforpay = new \InvoiceWM\pay\Wayforpay();
									$wayforpay->set_order( $id );
									$wayforpay->render_form( false, _l( 'Спробувати ще раз', 1 ) );
								}
								?>
                            </div>
						<?php endif; ?>
					<?php else: ?>
                        <div class="bill-box-body">
                            <div class="bill-box__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 60 60" fill="none">
  <mask id="mask0_21_809" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="60" height="60">
    <rect width="60" height="60" fill="#D9D9D9"/>
  </mask>
  <g mask="url(#mask0_21_809)">
    <path d="M29.8744 35.0959C29.3427 35.0959 28.8975 34.9162 28.5387 34.5566C28.1796 34.1974 28 33.7522 28 33.2209V15.7209C28 15.1897 28.1798 14.7443 28.5394 14.3847C28.899 14.0255 29.3444 13.8459 29.8756 13.8459C30.4073 13.8459 30.8525 14.0255 31.2113 14.3847C31.5704 14.7443 31.75 15.1897 31.75 15.7209V33.2209C31.75 33.7522 31.5702 34.1974 31.2106 34.5566C30.851 34.9162 30.4056 35.0959 29.8744 35.0959ZM29.8744 46.1534C29.3427 46.1534 28.8975 45.9737 28.5387 45.6141C28.1796 45.2545 28 44.8091 28 44.2778C28 43.7462 28.1798 43.3009 28.5394 42.9422C28.899 42.583 29.3444 42.4034 29.8756 42.4034C30.4073 42.4034 30.8525 42.5832 31.2113 42.9428C31.5704 43.3024 31.75 43.7478 31.75 44.2791C31.75 44.8107 31.5702 45.2559 31.2106 45.6147C30.851 45.9739 30.4056 46.1534 29.8744 46.1534Z"
          fill="#C9085F"/>
  </g>
</svg>
                            </div>
                            <div class="bill__title">
                                <p style="text-align: center"><?php _l( 'Посилання застаріло!' ) ?></p>
                            </div>
                        </div>
					<?php endif; ?>
				<?php endif; ?>
            </div>
        </div>
    </section>


<?php get_footer();