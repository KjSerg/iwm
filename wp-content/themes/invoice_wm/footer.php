<?php
$var      = variables();
$set      = $var['setting_home'];
$assets   = $var['assets'];
$url      = $var['url'];
$url_home = $var['url_home'];
$user_id  = get_current_user_id();
$views    = 0;
$id       = get_the_ID();

?>
</main>
<?php if ( $user_id && is_singular( 'bill' ) ): ?>
    <footer class="footer footer--admin">
        <div class="container">
            <div class="footer-container">
                <div class="footer-column">
                    Всього переглядів:
                    <strong>
                        <a class="send-request"
                           href="<?php echo admin_url( 'admin-ajax.php' ) . '?action=get_views&post_id=' . $id; ?>">
							<?php echo get_number_views( $id ); ?>
                        </a>
                    </strong>
                </div>
                <div class="footer-column">
                    Переглядають зараз: <strong
                            class="live-users-count"><?php echo get_number_viewing( $id ); ?></strong>
                </div>
            </div>
        </div>
    </footer>
    <div id="modal-new-invoice" class="modal-window modal-new-invoice">
        <div class="modal-window__title modal__title text-center">Новий рахунок</div>
        <form method="post" novalidate class="form-js form form-new-invoice" id="new-invoice">
            <label class="form-label">
                <span class="form-label-head">Назва або заголовок</span>
                <input type="text" name="post_title" required>
            </label>
            <label class="form-label">
                <span class="form-label-head">Ціна</span>
                <input type="number" min="1" name="price" required>
            </label>
			<?php if ( $currency = get_invoice_currency() ): ?>
                <label class="form-label">
                    <span class="form-label-head">Валюта</span>
                    <select name="currency" class="">
						<?php
						foreach ( $currency as $currency_code => $currency ) {
							$inner = esc_html( $currency );
							echo "<option>$inner</option>";
						}
						?>
                    </select>
                </label>
			<?php endif; ?>
			<?php if ( function_exists( 'pll_languages_list' ) ):
				if ( $languages = pll_languages_list() ):
					?>
                    <label class="form-label">
                        <span class="form-label-head">Мова</span>
                        <select name="lang" class="">
							<?php
							foreach ( $languages as $language ) {
								$inner = esc_html( $language );
								echo "<option>$inner</option>";
							}
							?>
                        </select>
                    </label>
				<?php endif; ?>
			<?php endif; ?>
            <label class="form-label">
                <span class="form-label-head">Опис</span>
                <textarea name="text"></textarea>
            </label>

            <div class="form-list">
                <span class="form-label-head">Опис</span>
                <label class="form-list-item">
                    <input type="checkbox" name="method[]" value="wayforpay" checked>
                    <span class="icon"></span>
                    <span class="text">wayforpay</span>

                </label>
                <label class="form-list-item">
                    <input type="checkbox" name="method[]" value="whitepay" checked>
                    <span class="icon"></span>
                    <span class="text">whitepay</span>
                </label>
            </div>
            <div class="form-label">
                <label for="autocomplete-offer" class="form-label-head">Офери</label>
                <div class="form-autocomplete">
                    <input type="hidden" name="selected_offers">
                    <input type="text" placeholder="Введіть назву комерційної пропозиції" id="autocomplete-offer"
                           class="autocomplete-offer">
                    <div class="form-list form-autocomplete-list" style="display:none;"></div>
                </div>
            </div>
            <div class="modal-buttons">
                <a href="#" class="button button--light close-fancybox-modal">Відміна</a>
                <button class="button">Створити</button>
            </div>
            <input type="hidden" name="action" value="new_invoice">
			<?php wp_nonce_field( 'new_invoice', 'true_nonce', true, true ); ?>
        </form>
    </div>
    <div id="modal-edit-invoice" class="modal-window modal-edit-invoice">
        <div class="modal-window__title modal__title text-center">Редагувати рахунок</div>
        <div class="modal-edit-invoice-container"></div>
    </div>
    <div id="modal-views" class="modal-window modal-views">
        <div class="modal-views-container"></div>
    </div>
<?php endif; ?>
<div id="dialog" class="modal-window modal-window-notice">
    <div class="modal-window__title modal__title"></div>
    <div class="modal-window__text modal__text"></div>
</div>
<div class="preloader" style="">
    <img src="<?php echo esc_url( $assets . 'img/loading.gif' ); ?>" alt="loading.gif">
</div>
<script>
    var adminAjax = '<?php echo $var['admin_ajax']; ?>';
</script>
<?php wp_footer(); ?>
</body>
</html>
