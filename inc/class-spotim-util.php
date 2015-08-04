<?php

class SpotIM_Util {
	public static function send_headers( array $headers ) {
		foreach ( (array) $headers as $header => $content ) {
			header( $header . ': ' . $content );
		}
	}

	/**
	 * Checks if a coversation was processed with register_conversation
	 */
	public static function is_conversation_processed( $post_id ) {
		return ( get_post_meta( $post_id, '_spotim_conversation_registered', true ) == '1' );
	}

	public static function get_comment_by_spotim_id( $spotim_comment_id ) {
		$query = get_comments( array(
			'meta_key' 		=> 'spotim_comment_id',
			'meta_value' 	=> $spotim_comment_id,
		) );

		return ( count( $query ) === 1 ) ? current( $query ) : false;
	}
}