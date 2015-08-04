<?php

include_once 'mock-objects/class-spotim-api-mock.php';
include_once 'mock-objects/class-spotim-http-request-mock.php';

class Test_API_Sync extends WP_SpotIM_TestCase {

	private $spotim_instance;
	private $_post_id;
	private $first_batch_sync;
	private $first_batch_result;

	public function setUp() {
		parent::setUp();

		// add filter for the mock API class
		$this->spotim_instance = spotim_instance();

		// create a post to do the work on
		$this->_post_id = $post_id = $this->factory->post->create();

		// load JSON for main insertions
		$first_batch_data = $this->_read_json_file( 'sync-many-inserts.json' );

		// fire up the sync class
		$this->first_batch_sync = new SpotIM_Sync( $post_id, $first_batch_data );
		$this->first_batch_result = $this->first_batch_sync->sync();
	}

	public function test_sync() {
		// should have stats for 4 comment writes, 1 reply write
		$this->assertEquals( 4, $this->first_batch_result['c+'] );
		$this->assertEquals( 1, $this->first_batch_result['r+'] );

		// check that all 5 comments have been inserted
		$this->assertCount( 5, get_comments( array(
			'post_id' => $this->_post_id
		) ) );
	}

	public function test_sync_edits() {
		$edits_json = $this->_read_json_file( 'sync-many-edits.json' );

		$sync = new SpotIM_Sync( $this->_post_id, $edits_json );
		$results = $sync->sync();

		// got edits for r~ and c~?
		$this->assertEquals( 1, $results['c~'] );
		$this->assertEquals( 1, $results['r~'] );

		$spotim_comment_id_to_test = 'sp_123_456_c_1';

		// make sure the comments were indeed edited
		$comment = SpotIM_Util::get_comment_by_spotim_id( $spotim_comment_id_to_test );

		$this->assertFalse( empty( $comment ) );

		// check if comment's content equals
		$this->assertEquals( $comment->comment_content, $edits_json['messages'][ $spotim_comment_id_to_test ]['content'] );
	}

	public function test_sync_deletes() {
		$edits_json = $this->_read_json_file( 'sync-many-deletes.json' );

		$sync = new SpotIM_Sync( $this->_post_id, $edits_json );
		$results = $sync->sync();

		// got edits for r- and c-?
		$this->assertEquals( 1, $results['c-'] );
		$this->assertEquals( 1, $results['r-'] );

		// after 2 comments removed, we should have 3.
		$this->assertCount( 3, get_comments( array(
			'post_id' => $this->_post_id
		) ) );
	}
}