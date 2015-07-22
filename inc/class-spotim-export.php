<?php

class SpotIM_Export {
	public function __construct() {}

	public static function start( $post_ids = false ) {
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
			if ( $post_result && ! $exporter_instance->is_empty() )
				$conversations_bucket[ $post_id ] = $post_result;
		}

		return $conversations_bucket;
	}

	public static function generate_json() {
		$result = array();
		$result = self::start();

		$filename = apply_filters( 'spotim_json_download_filename', sprintf( 'spotim-export-%s.json', date_i18n( 'd-m-Y_h-i', time() ) ) );

		// do headers
		SpotIM_Util::send_headers( array(
			'Content-Disposition' 	=> "attachment; filename=$filename",
			'Pragma' 				=> 'no-cache',
			'Expires' 				=> '0',
			'Content-Type'			=> 'application/json; charset=' . get_option( 'blog_charset' ),
		) );

		require_once 'class-spotim-jsonpretty.php';

		$json_pretty = new SpotIM_JsonPretty;
		echo $json_pretty->prettify( json_encode( $result ) );
		wp_die();
	}
}