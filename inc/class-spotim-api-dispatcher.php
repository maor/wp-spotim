<?php

class SpotIM_API_Dispatcher {
	private $request_object;

	public function initiate_setup( $request_object = false ) {
		$current_user = wp_get_current_user();

		// grab all the data needed to setup
		$request_data = array(
			'request_token' 	=> 'somerandomstring',
			'blog_name'			=> get_bloginfo( 'name' ),
			'blog_url'			=> get_bloginfo( 'url' ),
			'blog_owner_name' 	=> $current_user->display_name,
			'blog_owner_id' 	=> $current_user->ID,
			'blog_owner_email' 	=> get_bloginfo( 'admin_email' ),
		);

		// get request object
		$response = $this->_get_request_object()->request( 'spot', $request_data );

		// if spot_id/token exist in response, update option.
		if ( array_key_exists( 'spot_id', $response ) && array_key_exists( 'spot_token', $response ) ) {
			// save the spot_id and spot_token
			$spotim_instance = spotim_instance();
			return update_option( $spotim_instance::AUTH_OPTION, (array) $response );
		}

		return false;
	}

	public function register_conversation( $post_id = false ) {

	}

	private function _get_request_object() {
		if ( ! is_null( $this->request_object ) )
			return $this->request_object;

		$req_object_class = apply_filters( 'spotim_api_request_object_class', 'SpotIM_API_Base' );
		return new $req_object_class;
	}
}