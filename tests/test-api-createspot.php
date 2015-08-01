<?php

include 'mock-objects/class-spotim-api-mock.php';
include 'mock-objects/class-spotim-http-request-mock.php';

class Test_API_CreateSpot extends WP_SpotIM_TestCase {

	private $spotim_instance;

	public function setUp() {
		parent::setUp();

		// add filter for the mock API class
		$this->spotim_instance = spotim_instance();
	}

	public function test_did_recieve_spot_id_token() {
		// check if it was saved correctly in options table
		$option = get_option( SPOTIM_AUTH_OPTION );

		// check that spot_id + spot_token exist in DB
		$this->assertEquals( array( 'spot_id', 'spot_token' ), array_keys( $option ) );
	}

	public function test_register_conversation() {
		// create some post to register conversation with
		$post_id = $this->factory->post->create();

		$this->assertTrue( $this->spotim_instance->api->register_conversation( $post_id ) );

		// check if the meta has indeed been added
		$this->assertEquals( '1', get_post_meta( $post_id, '_spotim_conversation_registered', true ) );
	}
}