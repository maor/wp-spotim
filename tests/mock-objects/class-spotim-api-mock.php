<?php

class SpotIM_API_Mock extends SpotIM_API_Base {
	public function request( $endpoint, $payload, $do_auth = true, $json_decode = true ) {
		$data = ''; // json data

		// empty mock response object
		$request = new SpotIM_HTTP_Request_Mock( '' );

		switch ( $endpoint ) {
			case 'spot':
				$data = '{"spot_id":"sp_123","spot_token":"SECRET"}';
				break;
			case 'register_conversation':
				$data = '{}'; // okay
				break;
		}

		// insert data into response
		$request->result['body'] = $data;
		$request->result['response'] = array(
			'code' => 200,
			'message' => 'OK',
		);

		return $request;
	}
}