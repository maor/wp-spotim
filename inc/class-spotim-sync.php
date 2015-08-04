<?php

class SpotIM_Sync {

	private $data;
	private $post_id;
	private $counters = array();

	public function __construct( $post_id, $data ) {
		$this->post_id = $post_id;
		$this->data = $data;
	}

	public function sync() {
		// run through first steps
		$process_order = array(
			// reply edit/remote
			'r~',
			'r-',
			// comment edit/remove
			'c~',
			'c-',
			// additions
			'c+',
			'r+',
		);

		$events = &$this->data['events'];

		foreach ( $process_order as $process_mark ) {
			if ( empty( $events[ $process_mark ] ) )
				continue;

			foreach ( $events[ $process_mark ] as $spotim_comment_id ) {
				// figure out if this is a commment or a reply
				$this->comment( $process_mark, $spotim_comment_id );
			}
		}

		return $this->counters;
	}

	private function comment( $action, $spotim_comment_id ) {
		// if comment does not exist in "messages" hash, exit.
		// but only if these are WRITE methods
		$write_methods = array( 'c+', 'c~', 'r+', 'r~' );

		if ( in_array( $action, $write_methods ) ) {
			if ( ! array_key_exists( $spotim_comment_id, $this->data['messages'] ) )
				return false;

			$comment = &$this->data['messages'][ $spotim_comment_id ];
			$comment_user = $this->get_comment_user( $comment['user_id'] );

			if ( empty( $comment ) || empty( $comment_user ) )
				return false;
		}


		switch ( $action ) {
			// writes
			case 'c+':
			case 'r+':
				$parent_comment_id = 0;

				// check if this comment is a reply and has a comment
				if ( 'reply' == $comment['type'] && isset( $comment['parent_id'] ) ) {
					$parent_comment_id = $this->find_comment_id_by_spotim_id( $comment['parent_id'] );
				}

				// do not insert duplicates
				if ( $this->find_comment_id_by_spotim_id( $spotim_comment_id ) ) {
					// there is a duplicate, don't process
					return false;
				}

				// this comment must not have parents, insert it directly
				$inserted_comment_id = wp_insert_comment( array(
					'comment_post_ID' 		=> $this->post_id,
					'comment_author' 		=> $comment_user['display_name'],
					'comment_author_email' 	=> $comment_user['email'],
					'comment_content' 		=> $comment['content'],
					'comment_date' 			=> floor( $comment['timestamp'] ),
					'comment_parent' 		=> $parent_comment_id,
					'comment_approved' 		=> 1,
				) );

				if ( $inserted_comment_id ) {
					add_comment_meta( $inserted_comment_id, 'spotim_comment_id', $comment['id'] );
					add_comment_meta( $inserted_comment_id, 'spotim_comment_user', $comment_user );
				}
				break;

			// deletions
			case 'c-':
			case 'r-':
				// need to find the comment by spotim_comment_id
				$local_comment_id = $this->find_comment_id_by_spotim_id( $spotim_comment_id );

				if ( $local_comment_id ) {
					/**
					 * Bypass trash and delete comment forever?
					 *
					 * @since 1.0.4
					 *
					 * @param bool  $force_delete Whether to force deletion or not
					 */
					wp_delete_comment( $local_comment_id, apply_filters( 'spotim_sync_force_delete', true ) );
				}
				break;

			// edits
			case 'c~':
			case 'r~':
				// need to find the comment by spotim_comment_id
				$local_comment_id = $this->find_comment_id_by_spotim_id( $spotim_comment_id );

				if ( ! $local_comment_id )
					return false;

				wp_update_comment( array(
					'comment_ID' 		=> $local_comment_id,
					'comment_content' 	=> $comment['content'],
				) );
				break;
		}

		// update stats
		++$this->counters[ $action ];
	}

	private function get_comment_user( $user_slug ) {
		if ( ! array_key_exists( $user_slug, $this->data['users'] ) )
			return false;

		return $this->data['users'][ $user_slug ];
	}

	private function find_comment_id_by_spotim_id( $spotim_comment_id ) {
		$query = get_comments( array(
			'meta_key' 		=> 'spotim_comment_id',
			'meta_value' 	=> $spotim_comment_id,
			'fields' 		=> 'ids',
		) );

		return ( count( $query ) === 1 ) ? current( $query ) : false;
	}
}