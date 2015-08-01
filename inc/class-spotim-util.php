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
}