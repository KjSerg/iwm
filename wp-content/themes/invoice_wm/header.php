<?php
get_header();
$logo = carbon_get_theme_option( 'logo' );
$url  = site_url();
$id = get_the_ID();
$body_attr = is_singular('bill') ? 'data-post-id="'.$id.'"' : '';
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="msapplication-TileColor" content="#1F223F">
    <meta name="theme-color" content="#062058">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
          rel="stylesheet">
    <title><?php wp_title(); ?></title>
	<?php wp_head(); ?>
</head>
<body <?php echo $body_attr; ?> <?php body_class(); ?>>
<header class="header">
    <div class="container">
        <div class="header-container">
            <div class="header-container__logo">
                <a href="<?php echo $url; ?>" class="logo header__logo">
                    <img
                            src="<?php _u( carbon_get_theme_option( 'logo' ) ) ?>"
                            alt="<?php bloginfo( 'name' ); ?>"
                    >
                </a>
            </div>

        </div>
    </div>
</header>
<main class="content">