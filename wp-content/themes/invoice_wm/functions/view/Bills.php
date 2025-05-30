<?php

namespace InvoiceWM\views;
class Bills {
	public function render( $bills, $paged, $max_pages ): void {
		if ( ! empty( $bills ) ) : ?>
            <div class="archive-table-body container-js">
				<?php foreach ( $bills as $bill ) : $id = $bill->ID;
					$selected_currency = carbon_get_post_meta( $id, 'invoice_currency' );
					$invoice_status = carbon_get_post_meta( $id, 'invoice_status' ) ?: 'not_paid';
					$link = get_the_permalink( $id );
                    $link_cls = '';
					$post_status = get_post_status( $id );
                    $cls = $invoice_status . ' ' . $post_status;
					if ( $invoice_status == 'not_paid' && $post_status == 'publish' ) {
						$link = admin_url( 'admin-ajax.php' ) . '?action=get_edit_invoice_form&id=' . $id;
						$link_cls = 'edit-invoice-link';
					}
					?>
                    <form class="archive-table-row form-js no-reset <?php echo  $cls ?>"
                          id="archive-table-controls-<?php echo $id ?>" novalidate method="post">
                        <div class="archive-table-column">
                            <div class="archive-table__date"><?php echo get_the_date( 'd-m', $id ); ?></div>
                            <div class="archive-table__date"><?php echo get_the_date( 'H:i', $id ); ?></div>
                        </div>
                        <div class="archive-table-column">
                            <a href="<?php echo esc_attr( $link ) ?>"
                               class="archive-table-row__title <?php echo $link_cls ?> ">
								<?php echo esc_html( $bill->post_title ) ?>
                            </a>
                        </div>
                        <div class="archive-table-column">
                            <div class="archive-table-controls ">
                                <label>
                                    <input type="number" name="invoice_sum"
                                           class="trigger-form-js"
                                           min="1"
                                           value="<?php echo carbon_get_post_meta( $id, 'invoice_sum' ) ?>">
                                </label>
                                <label>
                                    <select name="invoice_currency" class="trigger-form-js">
										<?php if ( $currency = get_invoice_currency() ) {
											foreach ( $currency as $currency_code => $currency ) {
												$attr  = $selected_currency == $currency_code ? ' selected="selected"' : '';
												$inner = esc_html( $currency );
												$attr  = esc_attr( $attr );
												echo "<option $attr >$inner</option>";
											}
										} ?>
                                    </select>
                                </label>
                                <a href="<?php echo get_the_permalink( $id ) ?>" class="copy-link">
									<?php _s( _i( 'copy' ) ) ?>
                                </a>
                            </div>
                            <input type="hidden" name="action" value="change_bill">
                            <input type="hidden" name="bill_id" value="<?php echo $id ?>">
							<?php wp_nonce_field( 'change_bill', 'true_nonce', true, true ); ?>
                        </div>
                    </form>
				<?php endforeach; ?>
            </div>
			<?php $this->render_pagination( $paged, $max_pages ); ?>
		<?php else : ?>
            <div class="archive-table-row">
                <div class="archive-table-column">
                    <div class="archive-table-row__title">
                        Рахунків не знайдено.
                    </div>
                </div>
                <div class="archive-table-column"></div>
            </div>
		<?php endif;
	}

	private function render_pagination( $paged, $max_pages ): void {
		if ( $max_pages <= 1 ) {
			return;
		}
		$current = max( 1, $paged );
		if ( $max_pages <= $current ) {
			return;
		}
		$next = $current + 1;
		?>
        <div class="pagination-wrapper pagination-js">
            <a class="circle-button next-post-link"
               href="<?php echo site_url(); ?>?page_number=<?php echo $next ?>"><svg
                        width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M9 0H7V7H0V9H7V16H9V9H16V7H9V0Z"
                                          fill="#C9CBE4"></path>
                                </svg>
            </a>
        </div>
		<?php
	}
}