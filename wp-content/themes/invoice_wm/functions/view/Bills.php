<?php

namespace InvoiceWM\views;
class Bills {
	public function render( $bills ): void {
		if ( ! empty( $bills ) ) : ?>

			<?php foreach ( $bills as $bill ) : $id = $bill->ID;
				$selected_currency = carbon_get_post_meta( $id, 'invoice_currency' );
				?>
                <div class="archive-table-row">
                    <div class="archive-table-column">
                        <div class="archive-table-row__title">
							<?php echo esc_html( $bill->post_title ); ?>
                        </div>
                    </div>
                    <div class="archive-table-column">
                        <form class="archive-table-controls form-js" novalidate method="post">
                            <label>
                                <input type="number" name="invoice_sum"
                                       min="1"
                                       value="<?php echo carbon_get_post_meta( $id, 'invoice_sum' ) ?>">
                            </label>
                            <label>
                                <select name="invoice_currency">
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
                        </form>
                    </div>
                </div>
			<?php endforeach; ?>
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
}