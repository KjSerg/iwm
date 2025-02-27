<?php
\Invoice\settings\SettingsTheme::redirect_to_art();
get_header();
$var      = variables();
$set      = $var['setting_home'];
$assets   = $var['assets'];
$url      = $var['url'];
$url_home = $var['url_home'];
$id       = get_the_ID();
?>
    <div class="head-section section">
        <div class="container">
            <div class="head__title">
                Список
            </div>
        </div>
    </div>
<?php
get_footer();