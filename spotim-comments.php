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
Version: 1.0.3
Author URI: http://maorchasen.com/
*/

require_once 'inc/class-spotim-export.php';
require_once 'inc/class-spotim-export-conversation.php';
require_once 'inc/class-spotim-admin.php';
require_once 'inc/class-spotim-util.php';
require_once 'inc/class-spotim-frontend.php';

class WP_SpotIM {
	private static $_instance = null;

	protected function __construct() {
		$this->admin = new SpotIM_Admin;

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
		$class = __CLASS__;

		if ( is_null( self::$_instance ) )
			self::$_instance = new $class;

		return self::$_instance;
	}

	public function register_ajax_handlers() {
		add_action( 'wp_ajax_spot-generate-json', array( 'SpotIM_Export', 'generate_json' ) );
	}
}

add_action( 'plugins_loaded', array( 'WP_SpotIM', 'instance' ) );
