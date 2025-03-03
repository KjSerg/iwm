<?php
get_header();
$var            = variables();
$set            = $var['setting_home'];
$assets         = $var['assets'];
$url            = $var['url'];
$url_home       = $var['url_home'];
$id             = get_the_ID();
$title          = esc_attr( get_the_title() );
$invoice_status = carbon_get_post_meta( $id, 'invoice_status' );

?>
    <section class="head-section section">
        <div class="container">
            <div class="head-row">
                <div class="head__title">
					<?php echo $title ?>
                </div>
            </div>

        </div>
    </section>

    <section class="section bill-section">
        <div class="container">
			<?php
            if($invoice_status == 'paid'){
                echo 'Успішно оплачено!';
            }else{
	            $wayforpay = new \InvoiceWM\pay\Wayforpay();
	            $wayforpay->set_order( $id );
	            $wayforpay->render_form();
            }

			?>
        </div>
    </section>


<?php get_footer();