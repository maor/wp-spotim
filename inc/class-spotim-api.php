<?php
/**
 * SpotIM API
 *
 * Handles SpotIM-API endpoint requests
 *
 * @author      Spot.IM
 * @category    API
 * @package     SpotIM/API
 * @since       2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SpotIM_API' ) ) :

class SpotIM_API {

	/** This is the major version for the REST API and takes
	 * first-order position in endpoint URLs
	 */
	const VERSION = 2;

	/** @var SpotIM_API_Server the REST API server */
	public $server;

	/** @var SpotIM_API_Authentication REST API authentication class instance */
	public $authentication;

	/**
	 * Setup class
	 *
	 * @access public
	 * @since 2.0
	 * @return SpotIM_API
	 */
	public function __construct() {

		// add query vars
		add_filter( 'query_vars', array( $this, 'add_query_vars'), 0 );

		// register API endpoints
		add_action( 'init', array( $this, 'add_endpoint'), 0 );

		// handle REST API requests
		add_action( 'parse_request', array( $this, 'handle_rest_api_requests'), 0 );

		// handle spotim-api endpoint requests
		add_action( 'parse_request', array( $this, 'handle_api_requests' ), 0 );
	}

	/**
	 * add_query_vars function.
	 *
	 * @access public
	 * @since 2.0
	 * @param $vars
	 * @return string[]
	 */
	public function add_query_vars( $vars ) {
		$vars[] = 'spotim-api';
		$vars[] = 'spotim-api-version';
		$vars[] = 'spotim-api-route';
		return $vars;
	}

	/**
	 * add_endpoint function.
	 *
	 * @access public
	 * @since 2.0
	 * @return void
	 */
	public function add_endpoint() {

		// REST API
		add_rewrite_rule( '^spotim-api/v([1-2]{1})/?$', 'index.php?spotim-api-version=$matches[1]&spotim-api-route=/', 'top' );
		add_rewrite_rule( '^spotim-api/v([1-2]{1})(.*)?', 'index.php?spotim-api-version=$matches[1]&spotim-api-route=$matches[2]', 'top' );

		// WC API for payment gateway IPNs, etc
		// add_rewrite_endpoint( 'spotim-api', EP_ALL );
	}


	/**
	 * Handle REST API requests
	 *
	 * @since 2.2
	 */
	public function handle_rest_api_requests() {
		global $wp;

		if ( ! empty( $_GET['spotim-api-version'] ) ) {
			$wp->query_vars['spotim-api-version'] = $_GET['spotim-api-version'];
		}

		if ( ! empty( $_GET['spotim-api-route'] ) ) {
			$wp->query_vars['spotim-api-route'] = $_GET['spotim-api-route'];
		}

		// REST API request
		if ( ! empty( $wp->query_vars['spotim-api-version'] ) && ! empty( $wp->query_vars['spotim-api-route'] ) ) {

			define( 'SpotIM_API_REQUEST', true );
			define( 'SpotIM_API_REQUEST_VERSION', absint( $wp->query_vars['spotim-api-version'] ) );
	
			$this->includes();

			$this->server = new SpotIM_API_Server( $wp->query_vars['spotim-api-route'] );

			// load API resource classes
			$this->register_resources( $this->server );

			// Fire off the request
			$this->server->serve_request();
			exit;
		}
	}

	/**
	 * Include required files for REST API request
	 *
	 * @since 2.1
	 */
	public function includes() {

		// API server / response handlers
		include_once( 'api/class-spotim-api-exception.php' );
		include_once( 'api/class-spotim-api-server.php' );
		include_once( 'api/interface-spotim-api-handler.php' );
		include_once( 'api/class-spotim-api-json-handler.php' );

		// authentication
		include_once( 'api/class-spotim-api-authentication.php' );
		$this->authentication = new SpotIM_API_Authentication();

		include_once( 'api/class-spotim-api-resource.php' );
		include_once( 'api/class-spotim-api-general.php' );

		// allow plugins to load other response handlers or resource classes
		do_action( 'spotim_api_loaded' );
	}

	/**
	 * Register available API resources
	 *
	 * @since 2.1
	 * @param SpotIM_API_Server $server the REST server
	 */
	public function register_resources( $server ) {

		$api_classes = apply_filters( 'spotim_api_classes',
			array(
				'SpotIM_API_General',
			)
		);

		foreach ( $api_classes as $api_class ) {
			$this->$api_class = new $api_class( $server );
		}
	}

	/**
	 * API request - Trigger any API requests
	 *
	 * @access public
	 * @since 2.0
	 * @return void
	 */
	public function handle_api_requests() {
		global $wp;

		if ( ! empty( $_GET['spotim-api'] ) ) {
			$wp->query_vars['spotim-api'] = $_GET['spotim-api'];
		}

		// spotim-api endpoint requests
		if ( ! empty( $wp->query_vars['spotim-api'] ) ) {

			// Buffer, we won't want any output here
			ob_start();

			// No cache headers
			nocache_headers();

			// Get API trigger
			$api = strtolower( esc_attr( $wp->query_vars['spotim-api'] ) );

			// Load class if exists
			if ( class_exists( $api ) ) {
				new $api();
			}

			// Trigger actions
			do_action( 'spotim_api_' . $api );

			// Done, clear buffer and exit
			ob_end_clean();
			die('1');
		}
	}
}

endif;

return new SpotIM_API();
