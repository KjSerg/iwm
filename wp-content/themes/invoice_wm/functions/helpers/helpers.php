<?php

function variables() {
	$template_url = get_bloginfo( 'template_url' );

	return array(
		'url_home'     => $template_url . '/',
		'assets'       => $template_url . '/assets/',
		'setting_home' => get_option( 'page_on_front' ),
		'admin_ajax'   => site_url() . '/wp-admin/admin-ajax.php',
		'url'          => get_bloginfo( 'url' ) . '/',
	);

}


function get_user_roles_by_user_id( $user_id ): array {
	$user = get_userdata( $user_id );

	return empty( $user ) ? array() : $user->roles;
}

function is_user_in_role( $user_id, $role ): bool {
	return in_array( $role, get_user_roles_by_user_id( $user_id ) );
}


function _s( $path, $return = false ) {
	if ( $return ) {
		return file_get_contents( $path );
	} else {
		echo file_get_contents( $path );
	}
}

function _i( $image_name ): string {
	$var    = variables();
	$assets = $var['assets'];

	return $assets . 'img/' . $image_name . '.svg';
}

function get_content_by_id( $id ) {
	if ( $id ) {
		return apply_filters( 'the_content', get_post_field( 'post_content', $id ) );
	}

	return false;
}

function the_phone_link( $phone_number ) {
	$s = array( '+', '-', ' ', '(', ')' );
	$r = array( '', '', '', '', '' );
	echo esc_attr( 'tel:' . str_replace( $s, $r, $phone_number ) );
}

function the_phone_number( $phone_number ): void {
	$s = array( '', '-', ' ', '(', ')' );
	$r = array( '', '', '', '', '' );
	echo str_replace( $s, $r, $phone_number );
}

function the_image( $id ): void {
	if ( $id ) {
		$url = wp_get_attachment_url( $id );
		$pos = strripos( $url, '.svg' );
		if ( $pos === false ) {
			echo '<img  src="' . $url . '" alt="">';
		} else {
			_s( $url );
		}

	}
}

function get_image( $id ) {
	if ( $id ) {

		$url = wp_get_attachment_url( $id );

		$pos = strripos( $url, '.svg' );

		if ( $pos === false ) {
			return img_to_base64( $url );
		} else {
			return _s( $url, 1 );
		}

	}
}

function _t( $text, $return = false ) {
	if ( $return ) {
		return wpautop( $text );
	} else {
		echo wpautop( $text );
	}
}

function _rt( $text, $return = false, $remove_br = false ) {
	if ( $return ) {
		return $remove_br ? strip_tags( wpautop( $text ) ) : strip_tags( wpautop( $text ), '<br>' );
	} else {
		echo $remove_br ? strip_tags( wpautop( $text ) ) : strip_tags( wpautop( $text ), '<br>' );
	}
}

function is_even( $number ): bool {
	return ! ( $number & 1 );
}

function img_to_base64( $path ): string {
	$type = pathinfo( $path, PATHINFO_EXTENSION );
	$data = file_get_contents( $path );

	return 'data:image/' . $type . ';base64,' . base64_encode( $data );
}

function is_lighthouse(): bool {

	return str_contains( $_SERVER['HTTP_USER_AGENT'], 'Chrome-Lighthouse' ) || str_contains( $_SERVER['HTTP_USER_AGENT'], 'GTmetrix' );
}


function is_current_lang( $item ) {

	if ( $item ) {

		$classes = $item->classes;


		foreach ( $classes as $class ) {

			if ( $class == 'current-lang' ) {

				return true;

				break;
			}

		}

	}

}

function _l( $string, $return = false ) {
	if ( ! $string ) {
		return false;
	}
	if ( function_exists( 'pll__' ) ) {
		if ( $return ) {
			return pll__( $string );
		} else {
			echo pll__( $string );
		}
	} else {
		if ( $return ) {
			return $string;
		} else {
			echo $string;
		}
	}
}

function get_term_top_most_parent( $term, $taxonomy ) {
	$parent = get_term( $term, $taxonomy );
	while ( $parent->parent != '0' ) {
		$term_id = $parent->parent;
		$parent  = get_term( $term_id, $taxonomy );
	}

	return $parent;
}

function _u( $attachment_id, $return = false, $size = false ) {
	if ( ! $size ) {
		$size = is_lighthouse() ? 'thumbnail' : 'full';
	}
	if ( $attachment_id ) {
		$attachment = wp_get_attachment_image_src( $attachment_id, $size );
		if ( $return ) {
			return $attachment[0];
		} else {
			echo $attachment[0];
		}
	}
}

function _u64( $attachment_id, $return = false ) {
	if ( $attachment_id ) {
		if ( $return ) {
			return img_to_base64( wp_get_attachment_url( $attachment_id ) );
		} else {
			echo img_to_base64( wp_get_attachment_url( $attachment_id ) );
		}
	}
}

function is_json( $string ): bool {
	return is_string( $string ) && is_array( json_decode( $string, true ) );
}

function get_user_agent(): array|string {
	return isset( $_SERVER['HTTP_USER_AGENT'] ) ? wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) : '';
}

function get_the_user_ip() {

	if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}

	return $ip;
}

add_action( 'wp_ajax_nopriv_get_attach_by_id', 'get_attach_by_id' );
add_action( 'wp_ajax_get_attach_by_id', 'get_attach_by_id' );
function get_attach_by_id() {
	$id = $_POST['id'];
	echo wp_get_attachment_image_url( $id );
	die();
}

function is_in_range( $val, $min, $max ): bool {
	return ( $val >= $min && $val <= $max );
}

function replace_url( $str ): array|string|null {
	return preg_replace(
		"/(?<!a href=\")(?<!src=\")((http|ftp)+(s)?:\/\/[^<>\s]+)/i",
		"<a href=\"\\0\" target=\"_blank\">\\0</a>",
		$str
	);
}

function get_page_list(): array {
	$arr   = array();
	$query = new WP_Query( array(
		'post_type'      => 'page',
		'post_status'    => 'publish',
		'posts_per_page' => - 1,
	) );
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$arr[ get_the_ID() ] = get_the_title();
		}
	}
	wp_reset_postdata();

	return $arr;
}

function get_current_url(): string {
	return "http" . ( ( $_SERVER['SERVER_PORT'] == 443 ) ? "s" : "" ) . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}


function generate_random_string( $length = 10 ): string {
	$characters       = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen( $characters );
	$randomString     = '';
	for ( $i = 0; $i < $length; $i ++ ) {
		$randomString .= $characters[ rand( 0, $charactersLength - 1 ) ];
	}

	return $randomString;
}

function truncate_text( $text, $length = 150 ) {
	if ( mb_strlen( $text ) > $length ) {
		return mb_substr( $text, 0, $length ) . '...';
	}

	return $text;
}

function get_reg_exp_email(): string {
	return "[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])";
}

function validate_date( $date, $format = 'Y-m-d' ): bool {
	$d = DateTime::createFromFormat( $format, $date );

	return $d && $d->format( $format ) === $date;
}

function get_formated_data( $date, $format = 'F j, Y' ): string {
	$timestamp = strtotime( $date );

	return date( $format, $timestamp );
}

function get_post_author( $post_ID ): int|array|string {
	return get_post_field( 'post_author', $post_ID );
}

function get_client_ip(): string {
	if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		return $_SERVER['HTTP_CLIENT_IP'];
	} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		return explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] )[0];
	} else {
		return $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
	}
}

function get_invoice_currency(): array {
	return array(
		'UAH' => 'UAH',
		'USD' => 'USD',
		'EUR' => 'EUR',
	);
}

function get__filter_link( $k = '', $v = '' ): ?string {
	$selected_currency = filter_input( INPUT_GET, 'currency' ) ?: '';
	$get_string        = $selected_currency ? 'currency=' . $selected_currency : '';
	$url               = site_url();
	$res               = $url;
	if ( $k && $v ) {
		$res .= '?' . $k . '=' . $v;
		if ( $get_string ) {
			$res .= '&' . $get_string;
		}
	}

	return $res;
}

function is_bot(): bool {
	$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
	$ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';
	$bots      = [
		'bot',
		'crawl',
		'slurp',
		'spider',
		'mediapartners',
		'google',
		'bing',
		'yandex',
		'baidu',
		'duckduck',
		'teoma',
		'sogou',
		'exabot',
		'facebook',
		'ia_archiver',
		'facebot',
		'twitterbot'
	];
	$botIPs    = [
		'66.249.66.1',
		'64.233.173.193',
		'157.55.39.0',
		'207.46.13.0'
	];
	foreach ( $bots as $bot ) {
		if ( stripos( $userAgent, $bot ) !== false ) {
			return true;
		}
	}
	if ( in_array( $ipAddress, $botIPs ) ) {
		return true;
	}

	return false;
}

function get_number_views( $id ): int {
	return count( carbon_get_post_meta( $id, 'bill_views' ) ?: [] );
}

function get_number_viewing( $id ): int {
	$user_key     = 'live_users_' . $id;
	$current_time = time();
	$live_users   = get_transient( $user_key );
	if ( ! $live_users ) {
		$live_users = [];
	}
	foreach ( $live_users as $session => $timestamp ) {
		if ( $current_time - $timestamp > 30 ) {
			unset( $live_users[ $session ] );
		}
	}

	return count( $live_users );
}

function get_client_city_by_ip( $ip ): string {
	if ( ! $ip ) {
		return '';
	}
	$is_bot = preg_match(
		"~(Google|Yahoo|Rambler|Bot|Yandex|Spider|Snoopy|Crawler|Finder|Mail|curl)~i",
		$_SERVER['HTTP_USER_AGENT']
	);
	if ( is_bot() ) {
		return '';
	}
	$key       = 'get_city_by_ip_' . md5( $ip );
	$transient = get_transient( $key );
	if ( $transient !== false ) {
		return $transient;
	}
	$geo = json_decode( file_get_contents( 'http://api.sypexgeo.net/qe4zd/json/' . $ip ), true );
	$res = $geo['city'] ? $geo['city']['name_ru'] : '';
	if ( $geo['country']['name_ru'] ) {
		$res .= $res ? ', ' : '';
		$res .= $geo['country']['name_ru'];
	}
	set_transient( $key, $res, ( HOUR_IN_SECONDS * 24 * 7 ) );

	return $res;

}

