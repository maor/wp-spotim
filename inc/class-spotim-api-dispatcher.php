<?php

class SpotIM_API_Dispatcher {
	private $request_object;

	public function initiate_setup() {
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
		$response = (array) $this->_get_request_object()->request( 'spot', $request_data, false )->execute()->response();

		// if spot_id/token exist in response, update option.
		if ( array_key_exists( 'spot_id', $response ) && array_key_exists( 'spot_token', $response ) ) {
			// save the spot_id and spot_token
			update_option( SPOTIM_AUTH_OPTION, $response );
			return true;
		}

		return false;
	}

	public function register_conversation( $post_id = false ) {
		// check if post exists first
		if ( ! is_string( get_post_status( $post_id ) ) )
			return false;

		$request_data = array(
			'post_id' => $post_id,
			'site_url' => get_permalink( $post_id ),
		);

		$response = $this->_get_request_object()->request( 'register_conversation', $request_data )->execute();

		// check if HTTP 200 OK
		if ( $response->is_response_ok() ) {
			// save flag so we don't process this post twice
			update_post_meta( $post_id, '_spotim_conversation_registered', 1 );
			return true;
		}

		// request failed
		return false;
	}

	private function _get_request_object() {
		if ( ! is_null( $this->request_object ) )
			return $this->request_object;

		$req_object_class = apply_filters( 'spotim_api_request_object_class', 'SpotIM_API_Base' );
		return new $req_object_class;
	}
}