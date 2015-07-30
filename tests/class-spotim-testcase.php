<?php
/**
 * Tests SpotIM main class
 *
 * @author Spot.IM
 * @author Maor Chasen <maor@maorchasen.com>
 */
class WP_SpotIM_TestCase extends WP_UnitTestCase {

	/**
	 * Holds the plugin base class
	 *
	 * @return void
	 */
	protected $plugin;

	/**
	 * Custom action prefix for test custom triggered actions
	 * @var string
	 */
	protected $action_prefix = 'wp_stream_test_';

	/**
	 * PHP unit setup function
	 *
	 * @return void
	 */
	function setUp() {
		parent::setUp();
		// $this->plugin = $GLOBALS['spot'];

		// accquire auth token + spot id
		add_filter( 'spotim_api_request_object_class', array( $this, '_filter_spotim_api_request_object_class' ) );

		spotim_instance()->api->initiate_setup();
	}

	public function test_recieve_setup_spot_id_token() {
		// run initial plugin setup hook
		$this->assertTrue( spotim_instance()->api->initiate_setup() );
	}

	public function _filter_spotim_api_request_object_class() {
		return 'SpotIM_API_Mock';
	}

	/**
	 * Make sure the plugin is initialized with its global variable
	 *
	 * @return void
	 */
	/*public function test_plugin_initialized() {
		$this->assertFalse( null == $this->plugin );
	}*/
}
