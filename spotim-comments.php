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

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

final class WP_SpotIM {
	private static $_instance;

	const AUTH_OPTION = 'spotim_auth';

	/**
	 * @return WP_SpotIM
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self;
			self::$_instance->setup_constants();
			self::$_instance->includes();
			self::$_instance->dependencies();
		}

		return self::$_instance;
	}

	private function dependencies() {
		$this->admin = new SpotIM_Admin;
		$this->api = new SpotIM_API_Dispatcher;

		// setup AJAX
		$this->register_default_hooks();

		// setup front-end
		if ( ! is_admin() ) {
			SpotIM_Frontend::setup();
		}
	}

	/**
	 * Setup plugin constants
	 *
	 * @access private
	 * @since 1.0.4
	 * @return void
	 */
	private function setup_constants() {
		// Plugin version
		if ( ! defined( 'SPOTIM_AUTH_OPTION' ) ) {
			define( 'SPOTIM_AUTH_OPTION', 'spotim_auth' );
		}
	}

	private function includes() {
		require_once 'inc/spotim-core-functions.php';
		require_once 'inc/class-spotim-export.php';
		require_once 'inc/class-spotim-export-conversation.php';
		require_once 'inc/class-spotim-admin.php';
		require_once 'inc/class-spotim-util.php';
		require_once 'inc/class-spotim-frontend.php';
		require_once 'inc/class-spotim-http-request.php';
		require_once 'inc/class-spotim-api-base.php';
		require_once 'inc/class-spotim-api-dispatcher.php';

		$this->spotim_api = include( 'inc/class-spotim-api.php' );
	}

	public function register_default_hooks() {
		// AJAX for JSON Export
		add_action( 'wp_ajax_spot-generate-json', array( 'SpotIM_Export', 'generate_json' ) );

		// create_spot, For when post is published
		// $this->api->register_conversation(1173); die;
		add_action( 'publish_post', array( $this->api, 'register_conversation' ) );
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