<?php

class Test_WP_SpotIM extends WP_UnitTestCase {
	public function test_get_instance() {
		$instance = spotim_instance();
		$this->assertInstanceOf( 'WP_SpotIM', $instance );
	}
}