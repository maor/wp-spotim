<?php

class SpotIM_API_Mock extends SpotIM_API_Base {
	public function request( $endpoint, $payload, $json_decode = true ) {
		$data = ''; // json data

		switch ( $endpoint ) {
			case 'spot':
				$data = '{"spot_id":"sp_123","spot_token":"SECRET"}';
				break;
		}

		if ( $json_decode )
			$data = json_decode( $data );

		return $data;
	}
}