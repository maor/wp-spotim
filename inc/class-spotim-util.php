<?php

class SpotIM_Util {
	public static function send_headers( array $headers ) {
		foreach ( (array) $headers as $header => $content ) {
			header( $header . ': ' . $content );
		}
	}
}