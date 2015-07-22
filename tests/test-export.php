<?php

class ExportTest extends WP_SpotIM_TestCase {

	protected $_exported_data;
	protected $_top_level_comments;
	protected $_post_id;

	protected $_random_authors = array(
		'ishaythestud@example.com' => 'Ishay Gee',
		'maorlv@example.com' => 'Maor Cee',
		'princeharry@example.com' => 'Prince Harry',
		'doge@example.com' => 'Doge, Inc.',
		'brokenexample.com' => 'Broken Email',
		'alenby@example.com' => '', // empty name
	);

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

		// get all comments
		$messages = $this->__get_exporter_instance( $this->_post_id )->aggregate_messages();

		// is this message here at all?
		$this->assertArrayHasKey( $comment_id, $messages );

		$exported_comment = $messages[ $comment_id ];

		// is this user anonymous?
		$this->assertTrue( $exported_comment['anonymous'] );
	}

	public function test_same_user_posting_multiple_comments() {
		$_random_author_email = array_rand( $this->_random_authors );
		$_random_author_name = $this->_random_authors[ $_random_author_email ];

		// create multiple comments by same user
		$comments_ids = $this->factory->comment->create_post_comments( $this->_post_id, 2, array(
			'comment_author' => $_random_author_name,
			'comment_author_email' => $_random_author_email,
		) );

		// create an export instance
		$users = $this->__get_exporter_instance( $this->_post_id )->aggregate_users();

		// how many items have this email in them?
		$all_emails = wp_list_pluck( $users, 'email' );

		// number of appearances
		$appearances = array_count_values( $all_emails );

		// needs to appear only once
		$this->assertEquals( 1, $appearances[ $_random_author_email ] );
	}

	public function test_conversation_two_levels_nested_deep() {
		// create post from scratch
		$post_id = $this->factory->post->create();

		// create first comment on top-level
		$first_level_comment_id = $this->factory->comment->create( array(
			'comment_post_ID' => $post_id,
		) );

		// second-level comment
		$second_level_comment_id = $this->factory->comment->create( array(
			'comment_post_ID' 	=> $post_id,
			'comment_parent' 	=> $first_level_comment_id,
		) );

		// third level
		$third_level_comment_id = $this->factory->comment->create( array(
			'comment_post_ID' 	=> $post_id,
			'comment_parent' 	=> $second_level_comment_id,
		) );

		/*
		Tree structure should look like this:
		array(2) {
		  [101]=>
		  array(1) {
		    [0]=>
		    string(3) "102"
		  }
		  [102]=>
		  array(1) {
		    [0]=>
		    string(3) "103"
		  }
		}
		*/
		$exporter_instance = $this->__get_exporter_instance( $post_id );
		$tree = (array) $exporter_instance->get_tree();

		// there should be 2 items in the tree (because the 3rd is empty)
		$this->assertCount( 2, $tree );
		
		// check if 1st comment has the 2nd level comment in it
		$this->assertTrue( array_key_exists( $first_level_comment_id, $tree ) && current( $tree[ $first_level_comment_id ] ) == $second_level_comment_id );
		// ... same for second
		$this->assertTrue( array_key_exists( $second_level_comment_id, $tree ) && current( $tree[ $second_level_comment_id ] ) == $third_level_comment_id );
		// as for the third, it should exist in tree, but be orphan
		$this->assertArrayHasKey( $second_level_comment_id, $tree );
		// make sure third level does not exist (because it should be empty, since it has no children under it)
		$this->assertTrue( ! array_key_exists( $third_level_comment_id, $tree ) );

		// last but not least, check 'comments_ids' to make sure it has only the top level comment
		$comments_ids = array_values( wp_list_pluck( $exporter_instance->get_top_level_comments(), 'comment_ID' ) );
		$this->assertEquals( $comments_ids, array( $first_level_comment_id ) );
	}

	public function test_tree_should_be_hash() {
		$this->assertInstanceOf( 'stdClass', $this->__get_exporter_instance( $this->_post_id )->get_tree() );
	}

	private function __get_exporter_instance( $post_id = false ) {
		$post_id = ( $post_id ) ? $post_id : $this->_post_id;
		return new SpotIM_Export_Conversation( $post_id );
	}

	/*
	 * This function nests up to 2 levels deep
	 */
	private function __util_create_nested_comments_randomly( $post_id ) {
		$_random_authors = $this->_random_authors;

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