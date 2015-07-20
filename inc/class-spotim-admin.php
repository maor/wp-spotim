<?php

class SpotIM_Admin {
	protected $_screens = array();

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'create_admin_menu' ), 20 );
	}

	public function create_admin_menu() {
		$this->_screens['main'] = add_options_page(
			__( 'Spot.IM Settings', 'wp-spotim' ), 
			__( 'Spot.IM Settings', 'wp-spotim' ), 
			'manage_options', 
			'spotim_settings', 
			array( $this, 'admin_page_callback' )
		);

		// Just make sure we are create instance.
		add_action( 'load-' . $this->_screens['main'], array( &$this, 'load_cb' ) );
	}

	public function admin_page_callback() {
		?>
		<div class="wrap">
			<h2 class="aal-page-title"><?php _e( 'Spot.IM Settings', 'wp-spotim' ); ?></h2>

			<form id="activity-filter" method="get" action="<?php echo admin_url( 'admin-ajax.php' ); ?>">
				<p><?php esc_html_e( 'Welcome to Spot.IM Comments JSON exporter.', 'wp-spotim' ); ?></p>
				<input type="hidden" name="action" value="spot-generate-json" />
				<button type="submit" class="button button-primary"><?php _e( 'Export JSON', 'wp-spotim' ); ?></button>
			</form>
		</div>
		<?php
	}

	public function load_cb() {

	}
}