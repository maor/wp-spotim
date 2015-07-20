<?php

class ExportTest extends WP_SpotIM_TestCase {

	protected $_exported_data;

	protected $_top_level_comments;

	protected $_post_id;


	public function setUp() {
		parent::setUp();

		$this->_post_id = $post_id = $this->factory->post->create();

		// let's associate comments to this new post
		$this->_top_level_comments = $this->__util_create_nested_comments_randomly( $post_id );

		$instance = new SpotIM_Export();
		$exported_data = $instance->start( $post_id );

		// save a local version of the current one
		$this->_exported_data = current( $exported_data );
	}

	public function test_top_level_comments_exist_in_comments_ids() {
		// assert the count of TL comments
		$this->assertCount( 5, $this->_exported_data->comments_ids );

		// check if both arrays are equal (top level comment IDs 
		// are in place and properly exported)
		$this->assertEquals( $this->_top_level_comments, $this->_exported_data->comments_ids );
	}

	public function test_anonymous_user_comment() {
		// create an anonymous comment
		$comment_id = $this->factory->comment->create( array(
			'comment_post_ID' => $this->_post_id,
			'comment_author' => 'Anonymous',
		) );

		// create an export instance
		$export_instance = new SpotIM_Export_Conversation( $this->_post_id );

		// get all comments
		$messages = $export_instance->aggregate_messages();

		// is this message here at all?
		$this->assertArrayHasKey( $comment_id, $messages );

		$exported_comment = $messages[ $comment_id ];

		// is this user anonymous?
		$this->assertTrue( $exported_comment['anonymous'] );
	}

	/*
	 * This function nests up to 2 levels deep
	 */
	private function __util_create_nested_comments_randomly( $post_id ) {
		$_random_authors = array(
			'ishaythestud@example.com' => 'Ishay Gee',
			'maorlv@example.com' => 'Maor Cee',
			'princeharry@example.com' => 'Prince Harry',
			'doge@example.com' => 'Doge, Inc.',
			'brokenexample.com' => 'Broken Email',
			'alenby@example.com' => '', // empty name
		);

		// create some top-level comments first
		$comments_ids = $this->factory->comment->create_post_comments( $post_id, 5 );

		// loop through these and create nested comments for *some*
		foreach ( $comments_ids as $comment_id ) {
			$sub_comments_ids = array();
			$_rand = rand( 1, 2 );
			$_args = array(
				'comment_parent' => $comment_id
			);

			$_random_author_email = array_rand( $_random_authors );
			$_random_author_name = $_random_authors[ $_random_author_email ];

			if ( $_rand === 1 ) {
				// create one
				$sub_comments_ids = $this->factory->comment->create_post_comments( $post_id, 1, wp_parse_args( array(
					'comment_author' => $_random_author_name,
					'comment_author_email' => $_random_author_email,
				), $_args ) );
			} elseif ( $_rand === 2 ) {
				// create many
				$sub_comments_ids = $this->factory->comment->create_post_comments( $post_id, rand( 1, 5 ), wp_parse_args( array(
					'comment_author' => $_random_author_name,
					'comment_author_email' => $_random_author_email,
				), $_args ) );
			}

			// add third-level-deep comments
			foreach ( $sub_comments_ids as $sub_level_comment_id ) {
				// .. for some of these
				if ( rand( 0, 1 ) === 1 ) {

					$this->factory->comment->create_post_comments( $post_id, rand( 1, 3 ), wp_parse_args( array(
						'comment_author' => $_random_author_name,
						'comment_author_email' => $_random_author_email,
						'comment_parent' => $sub_level_comment_id // <<< !
					), $_args ) );
				}
			}
		}

		return $comments_ids;
	}
}