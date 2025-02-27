<?php

use InvoiceWM\controller\BillController;
\Invoice\settings\SettingsTheme::redirect_to_art();
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
            <div class="head__title">
                Список
            </div>
        </div>
    </section>

    <section class="section archive-section">
        <div class="container">
            <div class="archive-table">
                <div class="archive-table-head archive-table-row">
                    <div class="archive-table-column">
                        <div class="archive-table__title">Рахунки</div>
                    </div>
                    <div class="archive-table-column"></div>
                </div>
				<?php BillController::render_bills(); ?>
            </div>
        </div>
    </section>

<?php get_footer();