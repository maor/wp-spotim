<?php

class SpotIM_API_Dispatcher extends SpotIM_API_Base {
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

		$res = $this->request( 'spot', $request_data );

		$response = json_decode( $res );

		// if spot_id/token exist in response, update option.
		if ( array_key_exists( 'spot_id', $response ) && array_key_exists( 'spot_token', $response ) ) {
			// save the spot_id and spot_token
			$spotim_instance = spotim_instance();
			update_option( $spotim_instance::AUTH_OPTION, $response );
		}
	}
}