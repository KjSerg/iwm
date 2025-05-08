<?php
function wm_translator( $text, $target = 'uk' ) {
	$apiKey            = "97d6e412-937a-4762-855d-4accb9ff36ca:fx";
	$url               = "https://api-free.deepl.com/v2/translate";
	$cacheKey          = 'deepl_translation_' . md5( $text . $target );
	$cacheTime         = DAY_IN_SECONDS;
	$cachedTranslation = get_transient( $cacheKey );
	if ( $cachedTranslation !== false ) {
		return $cachedTranslation;
	}
	$data = http_build_query( [
		"auth_key"    => $apiKey,
		"text"        => $text,
		"target_lang" => strtoupper( $target )
	] );

	$options = [
		"http" => [
			"header"  => "Content-Type: application/x-www-form-urlencoded\r\n",
			"method"  => "POST",
			"content" => $data
		]
	];

	$context  = stream_context_create( $options );
	$response = file_get_contents( $url, false, $context );

	if ( $response === false ) {
		return '';
	}

	$result      = json_decode( $response, true );
	$translation = $result["translations"][0]["text"] ?? '';
	set_transient( $cacheKey, $translation, $cacheTime );

	return $translation;
}

