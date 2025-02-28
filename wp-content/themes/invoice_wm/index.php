<?php

use InvoiceWM\controller\BillController;

\Invoice\settings\SettingsTheme::redirect_to_art();
get_header();
$var               = variables();
$set               = $var['setting_home'];
$assets            = $var['assets'];
$url               = $var['url'];
$url_home          = $var['url_home'];
$id                = get_the_ID();
$selected_currency = filter_input( INPUT_GET, 'currency' ) ?: '';
?>
    <section class="head-section section">
        <div class="container">
            <div class="head__title">
                Список
            </div>
        </div>
    </section>

    <section class="section archive-section">
        <div class="container">
            <div class="archive-table-wrapper">
                <div class="archive-table">
                    <div class="archive-table-head archive-table-row">
                        <div class="archive-table-column">
                            <div class="archive-table__date">Дата</div>
                        </div>
                        <div class="archive-table-column">
                            <a href="<?php echo $url ?>" class="archive-table__title">
                                Всі рахунки
                            </a>
                            <a href="<?php echo get__filter_link( 'status', 'paid' ) ?>" class="archive-table__title">
                                Оплачені
                            </a>
                            <a href="<?php echo get__filter_link( 'status', 'not_paid' ) ?>"
                               class="archive-table__title">
                                Не оплачені
                            </a>
                        </div>
                        <div class="archive-table-column">
                            <div class="archive-table-head-controls">
                                <div class="archive-table-head__title">Ціна</div>
                                <form method="get" class="archive-table-head__title">
									<?php
									if ( $_GET ) {
										foreach ( $_GET as $key => $value ) {
											$key   = esc_attr( $key );
											$value = esc_attr( $value );
											echo '<input type="hidden" name="' . $key . '" value="' . $value . '">';
										}
									}
									?>
                                    <label>
                                        <select name="currency" class="trigger-form-js">
                                            <option value="">Валюта</option>
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
                    </div>
					<?php BillController::render_bills(); ?>
                </div>
            </div>
        </div>
    </section>

<?php get_footer();