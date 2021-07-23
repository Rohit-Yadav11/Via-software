<?php
/**
 * Admin Notices
 *
 * @package     EDD\FreeDownloads\Admin\Notices
 * @since       2.1.0
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Our admin notices class
 *
 * @since       2.1.0
 */
class EDD_Free_Downloads_Admin_Notices {


	/**
	 * Get things started
	 *
	 * @access      public
	 * @since       2.1.0
	 * @return      void
	 */
	public function __construct() {
		add_action( 'admin_notices', array( $this, 'notices' ) );
	}


	/**
	 * Display admin notices
	 *
	 * @access      public
	 * @since       2.1.0
	 * @return      void
	 */
	public function notices() {
		if ( empty( $_GET['edd-message'] ) ) {
			return;
		}

		$type    = 'updated';
		$message = '';

		switch ( strtolower( $_GET['edd-message'] ) ) {
			case 'fd-files-deleted' :
				$message = __( 'Cached files cleared successfully', 'edd-free-downloads' );
				break;
			case 'fd-cache-purged' :
				$message = __( 'Free Downloads cache purged successfully', 'edd-free-downloads' );
				break;
		}

		if ( ! empty( $message ) ) {
			echo '<div class="' . esc_attr( $type ) . '"><p>' . $message . '</p></div>';
		}
	}
}
new EDD_Free_Downloads_Admin_Notices;
