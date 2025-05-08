<?php
function the_page(): void {
	get_header();
	$var      = variables();
	$set      = $var['setting_home'];
	$assets   = $var['assets'];
	$url      = $var['url'];
	$url_home = $var['url_home'];
	$id       = get_the_ID();
	?>
    <section class="head-section section">
        <div class="container">
            <div class="head-row">
                <div class="head__title">
					<?php echo get_the_title() ?>
                </div>
            </div>

        </div>
    </section>
    <section class="section text-section">
        <div class="container">
            <div class="text">
				<?php the_post();
				the_content(); ?>
            </div>
        </div>
    </section>
	<?php
	get_footer();
}

function the_payment_methods( $pay_methods = [] ): void {
	if ( ! $payment_methods = carbon_get_theme_option( 'payment_methods' ) ) {
		return;
	}

	?>
    <div class="form-list">
        <span class="form-label-head">Метод оплати</span>
		<?php foreach ( $payment_methods as $method ): ?>
            <label class="form-list-item">
                <input type="checkbox" name="method[]" value="<?php echo esc_attr( $method['title'] ) ?>"
					<?php echo in_array( esc_attr( $method['title'] ), $pay_methods ) ? 'checked' : ''; ?>
                >
                <span class="icon"></span>
                <span class="text"><?php echo esc_attr( $method['title'] ) ?></span>

            </label>
		<?php endforeach; ?>
    </div>
	<?php
}