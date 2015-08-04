<?php
/**
 * SpotIM API Webhooks class
 *
 * Handles requests to the /webhooks endpoint
 *
 * @author   Spot.IM
 * @category API
 * @package  SpotIM/API
 * @since    2.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class SpotIM_API_General extends SpotIM_API_Resource {

	/** @var string $base the route base */
	protected $base = '/general';

	/**
	 * Register the routes for this class
	 *
	 * @since 2.2
	 * @param array $routes
	 * @return array
	 */
	public function register_routes( $routes ) {

		# GET /general/get_conversation/<id>
		$routes[ $this->base . '/get_conversation/(?P<id>\d+)' ] = array(
			array( array( $this, 'get_conversation' ), SpotIM_API_Server::READABLE ),
		);

		# POST /general/sync/
		$routes[ $this->base . '/sync' ] = array(
			array( array( $this, 'sync' ), SpotIM_API_Server::CREATABLE | SpotIM_API_Server::ACCEPT_DATA ),
		);

		return $routes;
	}

	/**
	 * Get the webhook for the given ID
	 *
	 * @since 2.2
	 * @param int $id webhook ID
	 * @param array $fields
	 * @return array
	 */
	public function get_conversation( $id, $fields = null ) {
		$id = $this->validate_request( $id, 'post', 'read' );

		if ( is_wp_error( $id ) ) {
			return $id;
		}

		$_G = $this->server->params['GET']; // GET

		$offset = array_key_exists( 'offset', $_G ) ? $_G['offset'] : 0;
		$count  = array_key_exists( 'count', $_G ) ? $_G['count'] : 50;

		$result = array();

		$exporter_instance = new SpotIM_Export_Conversation( $id, $count, $offset );
		$post_result = $exporter_instance->export();

		if ( $post_result && ! $exporter_instance->is_empty() ) {
			$result = $post_result;
		}

		return array(
			'metadata' => array(
				'left_comments' => $exporter_instance->total_comments_count->approved - $exporter_instance->get_comment_count(),
			),
			'conversation' => $result,
		);
	}

	/**
	 * Sync.
	 *
	 * @since 2.2
	 * @param array $data
	 * @return array
	 */
	public function sync( $data ) {
		
	}
}
