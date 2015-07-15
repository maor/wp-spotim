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
			$post_result = ( new SpotIM_Export_Conversation( $post_id ) )->export();

			// if post was successfully processed, and guaranteed to have comments
			if ( $post_result )
				$conversations_bucket[ $post_id ] = $post_result;
		}

		return $conversations_bucket;
	}
}

function spot_generate_json() {
	$result = array();

	$instance = new SpotIM_Export();
	$result = $instance->start();

	wp_send_json( $result );
	wp_die();
}
add_action( 'wp_ajax_spot-generate-json', 'spot_generate_json' );