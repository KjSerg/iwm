<?php
$var      = variables();
$set      = $var['setting_home'];
$assets   = $var['assets'];
$url      = $var['url'];
$url_home = $var['url_home'];
$user_id  = get_current_user_id();
?>
</main>
<div id="dialog" class="modal-window modal-window-notice">
    <div class="modal-window__title modal__title"></div>
    <div class="modal-window__text modal__text"></div>
</div>
<?php if ( $user_id ): ?>
    <div id="modal-new-invoice" class="modal-window modal-new-invoice">
        <div class="modal-window__title modal__title text-center">Новий рахунок</div>
        <form method="post" novalidate class="form-js form form-new-invoice">
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
                    <input type="text" placeholder="Введіть назву комерційної пропозиції" id="autocomplete-offer" class="autocomplete-offer">
                    <div class="form-list form-autocomplete-list" style="display:none;"></div>
                </div>
            </div>
            <div class="modal-buttons">
                <a href="#" class="button button--light">Відміна</a><button class="button">Створити</button>
            </div>
        </form>
    </div>
<?php endif; ?>
<div class="preloader" style="">
    <img src="<?php echo esc_url( $assets . 'img/loading.gif' ); ?>" alt="loading.gif">
</div>
<script>
    var adminAjax = '<?php echo $var['admin_ajax']; ?>';
</script>
<?php wp_footer(); ?>
</body>
</html>
