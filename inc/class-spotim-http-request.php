<?php

/* This class represents/abstracts a single request */
class SpotIM_HTTP_Request {
	public $args;
	public $url;
	public $http_method;
	public $result = false;
	private $ok_codes = array( 200, 304 );

	public function __construct( $url, $http_method = 'post', $args = array() ) {
		$this->url = $url;
		$this->http_method = in_array( $http_method, array( 'get', 'post' ) ) ? $http_method : 'post';
		$this->args = $args = wp_parse_args( $args, array(
			'headers' => array(),
			'body' => '',
		) );

		return $this;
	}

	public function set_args( $args ) {
		return $this->args = wp_parse_args( $args, $this->args );
	}

	public function set_url( $url ) {
		return $this->url = $url;
	}

	public function execute() {
		// check if there's a http_method for this type of request here
		$method_function = '_' . $this->http_method . '_request';

		if ( ! method_exists( $this, $method_function ) )
			return false;

		$this->result = $this->$method_function();

		return $this;
	}

	public function response( $decode_json = true ) {
		$response_body = wp_remote_retrieve_body( $this->result );

		if ( $decode_json )
			$response_body = json_decode( $response_body );

		return $response_body;
	}

	public function is_response_ok() {
		return (
			! is_wp_error( $this->result )
			&& in_array( $this->response_http_code(), $this->ok_codes )
		);
	}

	public function response_http_code() {
		return wp_remote_retrieve_response_code( $this->result );
	}

	public function response_headers() {
		return wp_remote_retrieve_headers( $this->result );
	}

	private function _post_request() {
		return wp_remote_post( $this->url, $this->args );
	}

	private function _get_request() {
		return wp_remote_get( $this->url, $this->args );
	}
}