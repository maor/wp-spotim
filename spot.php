<?php
/**
 * @package Spot
 * @version 1.6
 */
/*
Plugin Name: SPOT
Plugin URI: http://google.com
Description: Spot. THat's it.
Author: Maor Chasen
Version: 0.1
Author URI: http://maorchasen.com/
*/

require_once 'inc/class-spotim-export-conversation.php';
require_once 'inc/class-spotim-admin.php';
require_once 'inc/class-spotim-util.php';

class WP_SpotIM {
	private static $_instance = null;

	protected function __construct() {
		$this->admin = new SpotIM_Admin;
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
}

class SpotIM_Export {
	private $comments = false;
	private $comments_processed = array();

	public function __construct() {}

	public function start( $post_ids = false ) {
		$conversations_bucket = array();

		if ( ! $post_ids ) {
			$post_ids = get_posts( apply_filters( 'spotim_post_query_args', array(
				'fields' => 'ids',
				'posts_per_page' => -1,
			) ) );
		}

		foreach ( (array) $post_ids as $post_id ) {
			$exporter_instance = new SpotIM_Export_Conversation( $post_id );
			$post_result = $exporter_instance->export();

			// if post was successfully processed, and guaranteed to have comments
			if ( $post_result )
				$conversations_bucket[ $post_id ] = $post_result;
		}

		return $conversations_bucket;
	}
}

add_action( 'plugins_loaded', array( 'WP_SpotIM', 'instance' ) );

function spot_generate_json() {
	$result = array();

	$instance = new SpotIM_Export();
	$result = $instance->start();

	$filename = apply_filters( 'spotim_json_download_filename', sprintf( 'spotim-export-%s.json', date_i18n( 'd-m-Y_h-i', time() ) ) );

	// do headers
	SpotIM_Util::send_headers( array(
		'Content-Disposition' 	=> "attachment; filename=$filename",
		'Pragma' 				=> 'no-cache',
		'Expires' 				=> '0',
		'Content-Type'			=> 'application/json; charset=' . get_option( 'blog_charset' ),
	) );

	require_once 'inc/class-spotim-jsonpretty.php';

	$json_pretty = new SpotIM_JsonPretty;
	echo $json_pretty->prettify( json_encode( $result ) );
	wp_die();
}
add_action( 'wp_ajax_spot-generate-json', 'spot_generate_json' );