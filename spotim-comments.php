<?php
/**
 * @package Spot
 * @version 0.1
 */
/*
Plugin Name: Spot.IM
Plugin URI: http://spot.im
Description: Description for wp-spotim should be here
Author: Maor Chasen
Version: 1.0.4
Author URI: http://maorchasen.com/
*/

require_once 'inc/class-spotim-export.php';
require_once 'inc/class-spotim-export-conversation.php';
require_once 'inc/class-spotim-admin.php';
require_once 'inc/class-spotim-util.php';
require_once 'inc/class-spotim-frontend.php';
require_once 'inc/abstract-class-spotim-api-base.php';
require_once 'inc/class-spotim-api-dispatcher.php';

class WP_SpotIM {
	private static $_instance;

	const AUTH_OPTION = 'spotim_auth';

	protected function __construct() {
		$this->admin = new SpotIM_Admin;
		$this->api = new SpotIM_API_Dispatcher;

		// setup AJAX
		$this->register_ajax_handlers();

		// setup front-end
		if ( ! is_admin() ) {
			SpotIM_Frontend::setup();
		}
	}

	/**
	 * @return WP_SpotIM
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self;

		return self::$_instance;
	}

	public function register_ajax_handlers() {
		add_action( 'wp_ajax_spot-generate-json', array( 'SpotIM_Export', 'generate_json' ) );
	}

	public static function activation_hook() {
		// create a spot via API
		self::instance()->api->initiate_setup();
	}
}

function spotim_instance() {
	return WP_SpotIM::instance();
}
add_action( 'plugins_loaded', 'spotim_instance' );

register_activation_hook( __FILE__, array( 'WP_SpotIM', 'activation_hook' ) );