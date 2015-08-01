<?php

class SpotIM_Frontend {

	public static function setup() {
		$_c = __CLASS__;

		add_filter( 'comments_template', 	array( $_c, 'filter_comments_template' ), 20 );
		add_action( 'wp_head', 				array( $_c, 'action_wp_head' ) );
		add_action( 'template_redirect', 	array( $_c, 'register_conversation_on_pageload' ) );
	}

	public static function filter_comments_template( $theme_template ) {
		$allow_comments = WP_SpotIM::instance()->admin->get_option( 'enable_comments_replacement' ) == '1';

		if ( $allow_comments && is_single() && comments_open() )
			$theme_template = plugin_dir_path( dirname( __FILE__ ) ) . 'templates/comments-template.php';

		return $theme_template;
	}

	public static function action_wp_head() {
		$spot_id = WP_SpotIM::instance()->admin->get_option( 'spot_id', 'sp_foo' );
		?>
		<!-- wp-spotim vars -->
		<script type="text/javascript">
			var WP_SpotIM = {
				spot_id: '<?php echo esc_js( $spot_id ); ?>'
			};

			// spot.im embed
			!function(t,e,n){function p(){var p=e.createElement("script");p.type="text/javascript",p.async=!0,p.src=("https:"===e.location.protocol?"https":"http")+":"+n,t.parentElement.appendChild(p)}function a(){var t=e.getElementsByTagName("script"),n=t[t.length-1];return n.parentNode}t.spotId=WP_SpotIM.spot_id,t.parentElement=a(),p()}(window.SPOTIM={},document,"//v2.spot.im/launcher/bundle.js");
		</script>
		<?php
	}

	public static function register_conversation_on_pageload() {
		// if we're not viewing a post, bail
		if ( ! is_single() )
			return;

		if ( comments_open( get_the_ID() ) && ! SpotIM_Util::is_conversation_processed( get_the_ID() ) ) {
			// by now this is a post that wasn't processed
			spotim_instance()->api->register_conversation( get_the_ID() );
		}
	}
}