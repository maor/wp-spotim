<?php

class SpotIM_API_Base {
	private $auth_data;

	const API_BASE_URL = 'https://staging-import.spot.im/';

	public static $mock_endpoints = array(
		'spot' => 'http://jsonstub.com/spot',
		'register_conversation' => 'http://jsonstub.com/register_conversation',
	);

	public static $mock_headers = array(
		'JsonStub-User-Key' 	=> '98badab5-42ad-4384-9cf8-f4a4f5db8ad8',
		'JsonStub-Project-Key' 	=> '321443fb-2921-45eb-b973-f90c02f2f200',
		'Content-Type' 			=> 'application/json',
	);

	private static $ok_codes = array( 200, 304 );

	public function __construct() {}

	public function request( $endpoint, $payload, $do_auth = true, $json_decode = true ) {
		$request_url = self::get_full_request_url( $endpoint );

		$req_headers = self::$mock_headers;
		$req_body 	 = @json_encode( $payload );

		// do we need to authenticate for this API method?
		if ( $do_auth ) {
			$req_headers['spotim-token'] = $this->get_auth( 'spot_token' );
		}

		// this object will contain all request/response data
		$request = new SpotIM_HTTP_Request( $request_url, 'post', array(
			'headers' => $req_headers,
			'body' => $req_body
		) );

		return $request;
	}

	public static function is_response_ok( &$request ) {
		return (
			! is_wp_error( $request )
			&& in_array( wp_remote_retrieve_response_code( $request ), self::$ok_codes )
		);
	}

	public function get_auth( $key = '' ) {
		if ( is_null( $this->auth_data ) ) {
			$this->auth_data = get_option( SPOTIM_AUTH_OPTION );
		}
		var_dump(3,SPOTIM_AUTH_OPTION, get_option('spotim_auth'), $this->auth_data); die;

		return array_key_exists( $key, $this->auth_data ) ? $this->auth_data[ $key ] : false;
	}

	public static function get_full_request_url( $endpoint ) {
		$_debug_mode_on = apply_filters( 'spotim_debug', false );

		$endpoint = str_replace('/', '', $endpoint);

		return $_debug_mode_on ? self::$mock_endpoints[$endpoint] : self::API_BASE_URL . untrailingslashit( $endpoint );
	}
}