<?php

class SpotIM_Export_Conversation {
	public $post_id;
	private $comments;
	private $object;

	public function __construct( $post_id ) {
		$this->post_id = $post_id;

		// cache comments
		$comments = get_comments( apply_filters( 'spotim_comment_query_args', array(
			'post_id' => $post_id,
			'type' => 'comment', // include only comments, no trackbacks/pingbacks
		) ) );

		foreach ( $comments as $comment ) {
			$this->comments[ $comment->comment_ID ] = $comment;
		}

		return $this;
	}

	public function filter_children( $parent_id ) {
		$collection = array();

		foreach ($this->comments as $comment) {
			if ( $comment->comment_parent == $parent_id )
				$collection[] = $comment;
		}

		return $collection;
	}

	public static function filter_parents($comment) {
		return $comment->comment_parent == 0;
	}

	public function export() {
		// Do we even have any comments to begin with?
		if ( empty($this->comments) )
			return false;

		// solidify object
		$this->object = new stdClass;
		$o = &$this->object; // pointer

		// set URL of post
		$o->site_url = get_permalink( $this->post_id );
		
		// then get all comment IDs
		$o->comments_ids = array_values( wp_list_pluck( $this->get_top_level_comments(), 'comment_ID' ) );

		// first, we need the tree
		$o->tree = $this->get_tree();

		// aggregate all comments (messages)
		$o->messages = $this->aggregate_messages();

		// last but not least, users.
		$o->users = $this->aggregate_users();

		return apply_filters( 'spotim_conversation_ready', $o );
	}

	private function aggregate_users() {
		$users = array();

		foreach ( $this->comments as $comment ) {
			if ( ! trim($comment->comment_author_email) ) {
				// this comment has no email behind it. pick a random user ID to associate with.
				$_orderby_fields = array(
					'ID',
					'display_name',
					'name',
					'email',
					'registered',
				);

				$_order_options = array(
					'ASC',
					'DESC',
				);

				$users_query = get_users( array(
					'number' => 1,
					'orderby' => $_orderby_fields[ rand( 0, count($_orderby_fields) - 1 ) ],
					'order' => $_order_options[ rand( 0, count($_order_options) - 1 ) ],
				) );

				if ( empty($users_query) )
					continue;

				$user = current( $users_query );

				$users[ $user->ID ] = array(
					'user_name' => apply_filters( 'get_comment_author', $comment->comment_author, $comment->comment_ID, $comment ),
					'display_name' => apply_filters( 'get_comment_author', $comment->comment_author, $comment->comment_ID, $comment ),
					'email' => $comment->comment_author_email,
				);
			} else {
				$users[ $comment->comment_author_email ] = array(
					'user_name' => apply_filters( 'get_comment_author', $comment->comment_author, $comment->comment_ID, $comment ),
					'display_name' => apply_filters( 'get_comment_author', $comment->comment_author, $comment->comment_ID, $comment ),
					'email' => $comment->comment_author_email,
				);
			}
		}

		return $users;
	}

	private function aggregate_messages() {
		$comments = array();
		$comment_ids = array_keys( $this->object->tree );

		foreach ( $comment_ids as $comment_id ) {
			$comment = $this->comments[ $comment_id ];

			$comments[ $comment->comment_ID ] = array(
				'user_id' => $comment->comment_author_email,
				'content' => apply_filters( 'get_comment_text', $comment->comment_content, $comment, array() ),
				'written_at' => strtotime( $comment->comment_date_gmt ),
				'anonymous' => ( '' === trim($comment->comment_author_email) ),
			);
		}
		
		return $comments;
	}

	private function get_tree() {
		$parent_comments = $this->get_top_level_comments();
		$bank = array();

		// take care of figuring out tree
		foreach ($parent_comments as $comment) {
			$this->traverse($comment->comment_ID, $bank);
		}

		return $bank;
	}

	private function get_top_level_comments() {
		return array_filter($this->comments, array($this, 'filter_parents'));
	}

	private function traverse($comment_id, &$bank) {
		$child_comments = $this->filter_children($comment_id);

		// if no comments under this one, we're ending it here
		if ( empty($child_comments) ) {
			$bank[$comment_id] = array();
		} else {
			$bank[$comment_id] = wp_list_pluck( $child_comments, 'comment_ID' );

			// recurse down the tree
			foreach ( $child_comments as $comment )
				$this->traverse($comment->comment_ID, $bank);
		}
	}
}