<?php
$var                    = variables();
$set                    = $var['setting_home'];
$assets                 = $var['assets'];
$url                    = $var['url'];
$url_home               = $var['url_home'];
?>
</main>
<div class="preloader" style="">
    <img src="<?php echo esc_url( $assets . 'img/loading.gif' ); ?>" alt="loading.gif">
</div>
<script>
    var adminAjax = '<?php echo $var['admin_ajax']; ?>';
</script>
<?php wp_footer(); ?>
</body>
</html>
