<?php

class SpotIM_HTTP_Request_Mock extends SpotIM_HTTP_Request {
	public function execute() {
		return $this;
	}
}