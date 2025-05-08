<?php
namespace InvoiceWM\Components;
class Offers{
	public static function get_offers( $external_site_url = 'https://offer.web-mosaica.art/', $search = '', $ids = '' ) {
		$url = trailingslashit( $external_site_url ) . 'wp-json/CommerciaOffer/v1/pages/';

		if ( ! empty( $search ) ) {
			$url = add_query_arg( 'search', urlencode( $search ), $url );
		}
		if ( ! empty( $ids ) ) {
			$url = add_query_arg( 'ids', urlencode( $ids ), $url );
		}
		$key  = 'get_pages_from_external_site_' . md5( $url );
		$data = get_transient( $key );
		if ( $data !== false ) {
			return $data;
		}
		error_log( 'get_pages_from_external_site: ' . $url );
		$response = wp_remote_get( $url );

		if ( is_wp_error( $response ) ) {
			return [];
		}
		set_transient( $key, json_decode( wp_remote_retrieve_body( $response ), true ), 3600 );

		return json_decode( wp_remote_retrieve_body( $response ), true );
	}
}