<?php

include 'mock-objects/class-spotim-api-mock.php';

class API_CreateSpot extends WP_SpotIM_TestCase {

	public function setUp() {
		// add filter for the mock API class
		add_filter( 'spotim_api_request_object_class', array( $this, '_filter_spotim_api_request_object_class' ) );
	}

	public function test_did_recieve_spot_id_token() {
		// run initial plugin setup hook
		$spotim_instance = spotim_instance();
		$result = $spotim_instance->api->initiate_setup();
		
		$this->assertTrue( $result );

		// check if it was saved correctly in options table
		$option = get_option( $spotim_instance::AUTH_OPTION );

		// check that spot_id + spot_token exist in DB
		$this->assertEquals( array( 'spot_id', 'spot_token' ), array_keys( $option ) );
	}

	public function _filter_spotim_api_request_object_class() {
		return 'SpotIM_API_Mock';
	}
}