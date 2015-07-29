<?php

abstract class SpotIM_API_Base {
	const API_BASE_URL = 'https://staging-import.spot.im/';

	public static $mock_endpoints = array(
		'spot' => 'http://jsonstub.com/spot'
	);

	public static $mock_headers = array(
		'JsonStub-User-Key' 	=> '98badab5-42ad-4384-9cf8-f4a4f5db8ad8',
		'JsonStub-Project-Key' 	=> '321443fb-2921-45eb-b973-f90c02f2f200',
		'Content-Type' 			=> 'application/json',
	);

	private static $ok_codes = array( 200, 304 );

	public function __construct() {}

	public function request( $endpoint, $payload ) {
		$request_url = self::get_full_request_url( $endpoint );

		$res = wp_remote_post( $request_url, array(
			'headers' => self::$mock_headers,
			'body' => @json_encode( $payload )
		) );

		return wp_remote_retrieve_body( $res );
	}

	public static function is_response_ok( &$request ) {
		return (
			! is_wp_error( $request )
			&& in_array( wp_remote_retrieve_response_code( $request ), self::$ok_codes )
		);
	}

	public static function get_full_request_url( $endpoint ) {
		$_debug_mode_on = apply_filters( 'spotim_debug', false );

		$endpoint = str_replace('/', '', $endpoint);

		return $_debug_mode_on ? self::$mock_endpoints[$endpoint] : self::API_BASE_URL . untrailingslashit( $endpoint );
	}
}