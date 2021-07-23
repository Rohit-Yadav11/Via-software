<?php
/**
 * Actions
 *
 * @package     EDD\FreeDownloads\Actions
 * @since       1.1.0
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Registers a new rewrite endpoint
 *
 * @since       1.1.0
 * @param       array $rewrite_rules The existing rewrite rules
 * @return      void
 */
function edd_free_downloads_add_endpoint( $rewrite_rules ) {
	add_rewrite_endpoint( 'edd-free-download', EP_ALL );
}
add_action( 'init', 'edd_free_downloads_add_endpoint' );


/**
 * Handle newsletter opt-in
 *
 * @since       1.1.0
 * @return      void
 */
function edd_free_downloads_remove_optin() {
	if ( ! isset( $_POST['edd_free_download_email'] ) ) {
		return;
	}

	if ( edd_get_option( 'edd_free_downloads_newsletter_optin', false ) ) {
		// Build user info array for global opt-in
		$user_info = array(
			'email'      => $_POST['edd_free_download_email'],
			'first_name' => ( isset( $_POST['edd_free_download_fname'] ) ? $_POST['edd_free_download_fname'] : '' ),
			'last_name'  => ( isset( $_POST['edd_free_download_lname'] ) ? $_POST['edd_free_download_lname'] : '' ),
		);

		// MailChimp, pre 3.0
		if ( class_exists( 'EDD_MailChimp' ) ) {

			global $edd_mc; // $edd_mc is pre 3.0, $eddmc is 3.0+

			if ( ! empty( $edd_mc ) ) {
				// Running pre MailChimp 3.0
				if ( isset( $_POST['edd_free_download_optin'] ) ) {
					$edd_mc->subscribe_email( $user_info );
				} else {
					remove_action( 'edd_complete_download_purchase', array( $edd_mc, 'completed_download_purchase_signup' ) );
				}

			}
		}

		// GetResponse
		if ( class_exists( 'EDD_GetResponse' ) ) {
			if ( isset( $_POST['edd_free_download_optin'] ) ) {
				edd_getresponse()->newsletter->subscribe_email( $user_info );
			} else {
				remove_action( 'edd_complete_download_purchase', array( edd_getresponse()->newsletter, 'completed_download_purchase_signup' ) );
			}
		}

		// Aweber
		if ( class_exists( 'EDD_Aweber' ) ) {
			global $edd_aweber;

			if ( isset( $_POST['edd_free_download_optin'] ) ) {
				$edd_aweber->subscribe_email( $user_info );
			} else {
				remove_action( 'edd_complete_download_purchase', array( $edd_aweber, 'completed_download_purchase_signup' ) );
			}
		}

		// MailPoet
		if ( class_exists( 'EDD_MailPoet' ) ) {
			global $edd_mp;

			if ( isset( $_POST['edd_free_download_optin'] ) ) {
				$edd_mp->subscribe_email( $user_info );
			} else {
				remove_action( 'edd_complete_download_purchase', array( $edd_mp, 'completed_download_purchase_signup' ) );
			}
		}

		// Sendy
		if ( class_exists( 'EDD_Sendy' ) ) {
			global $edd_sendy;

			if ( isset( $_POST['edd_free_download_optin'] ) ) {
				$edd_sendy->subscribe_email( $user_info );
			} else {
				remove_action( 'edd_complete_download_purchase', array( $edd_sendy, 'completed_download_purchase_signup' ) );
			}
		}

		// Convert Kit
		if ( class_exists( 'EDD_ConvertKit' ) ) {
			global $edd_convert_kit;

			if ( isset( $_POST['edd_free_download_optin'] ) ) {
				$edd_convert_kit->subscribe_email( $user_info );
			} else {
				remove_action( 'edd_complete_download_purchase', array( $edd_convert_kit, 'completed_download_purchase_signup' ) );
			}
		}

		// ActiveCampaign
		if ( class_exists( 'EDD_ActiveCampaign' ) ) {
			if ( isset( $_POST['edd_free_download_optin'] ) ) {
				edd_activecampaign()->subscribe_email( $user_info['email'], $user_info['first_name'], $user_info['last_name'] );
			} else {
				remove_action( 'edd_complete_download_purchase', array( edd_activecampaign(), 'completed_download_purchase_signup' ) );
			}
		}
	}
}
add_action( 'edd_update_payment_status', 'edd_free_downloads_remove_optin', -10 );

/**
 * MailChimp 3.0 Integration
 *
 * When the box is checked, set the proper session value so that MailChimp knows if the person should be subscribed
 * or transactional.
 *
 * @since 2.3.4
 * 
 * @param int $payment_id The Payment ID that is being processed with Free Downloads
 */
function edd_free_downloads_mail_chimp_payent_completed( $payment_id ) {

	if ( class_exists( 'EDD_MailChimp' ) ) {

		global $eddmc; // $edd_mc is pre 3.0, $eddmc is 3.0+

		if ( ! empty( $eddmc ) ) {

			$opt_in_meta = false;
			$payment     = edd_get_payment( $payment_id );

			if( $payment ) {
				$opt_in_meta = $payment->get_meta( 'edd_free_download_optin', true );
			}

			$opted_in = isset( $_POST['edd_free_download_optin'] ) || $opt_in_meta;

			if ( ! $opted_in ) {
				edd_debug_log( 'Free Downloads - MailChimp value not added to payment meta because edd_free_download_optin was not present.' );
				return;
			}

			edd_debug_log( 'Free Downloads - MailChimp value added to payment because edd_free_download_optin was present.' );
			if ( ! empty( $payment ) ) {
				$payment->update_meta( '_mc_subscribed', 1 );
			}
		}
	}

}
add_action( 'edd_free_downloads_pre_complete_payment', 'edd_free_downloads_mail_chimp_payent_completed', 10, 1 );


/**
 * Callback function for the Compression Status setting
 *
 * @since       2.0.0
 * @return      void
 */
function edd_free_downloads_zip_status() {
	if ( class_exists( 'ZipArchive' ) ) {
		$html  = '<span class="edd-free-downloads-zip-status-available">' . __( 'Available', 'edd-free-downloads' ) . '</span>';
		$html .= '<span alt="f223" class="edd-help-tip dashicons dashicons-editor-help" title="<strong>' . __( 'Compression Status', 'edd-free-downloads' ) . '</strong>: ' . sprintf( __( 'Great! It looks like you have the ZipArchive class available! That means that we can auto-compress the files for multi-file %s.', 'edd-free-downloads' ), edd_get_label_plural( true ) ) . '"></span>';
	} else {
		$html  = '<span class="edd-free-downloads-zip-status-unavailable">' . __( 'Unavailable', 'edd-free-downloads' ) . '</span>';
		$html .= '<span alt="f223" class="edd-help-tip dashicons dashicons-editor-help" title="<strong>' . __( 'Compression Status', 'edd-free-downloads' ) . '</strong>: ' . sprintf( __( 'Oops! It looks like you don\'t have the ZipArchive class available! If you want us to auto-compress the files for multi-file %s, please make sure that your PHP instance is compiled with ZipArchive support.', 'edd-free-downloads' ), edd_get_label_plural( true ) ) . '"></span>';
	}

	echo $html;
}
add_action( 'edd_free_downloads_zip_status', 'edd_free_downloads_zip_status' );


/**
 * Ensure the cache directory exists
 *
 * @since       2.0.0
 * @return      void
 */
function edd_free_downloads_directory_exists() {
	$upload_dir = wp_upload_dir();
	$upload_dir = $upload_dir['basedir'] . '/edd-free-downloads-cache/';

	// Ensure that the cache directory exists
	if ( ! is_dir( $upload_dir ) ) {
		wp_mkdir_p( $upload_dir );
	}

	// Top level blank index.php
	if ( ! file_exists( $upload_dir . 'index.php' ) ) {
		@file_put_contents( $upload_dir . 'index.php', '<?php' . PHP_EOL . '// Silence is golden.' );
	}

	// Top level .htaccess
	$rules = "Options -Indexes";
	if ( file_exists( $upload_dir . '.htaccess' ) ) {
		$contents = @file_get_contents( $upload_dir . '.htaccess' );

		if ( $contents !== $rules || ! $contents ) {
			@file_put_contents( $upload_dir . '.htaccess', $rules );
		}
	} else {
		@file_put_contents( $upload_dir . '.htaccess', $rules );
	}
}
add_action( 'admin_init', 'edd_free_downloads_directory_exists' );


/**
 * Display a purge cache button in the settings panel
 *
 * @since       2.1.0
 * @return      void
 */
function edd_free_downloads_display_purge_cache() {
	$html  = '<a href="' . wp_nonce_url( add_query_arg( 'edd_action', 'free_downloads_purge_cache' ), 'edd_free_downloads_cache_nonce', '_wpnonce' ) . '" class="button edd-free-downloads-purge-cache"><span class="dashicons dashicons-trash"></span> ' . __( 'Purge Cache', 'edd-free-downloads' ) . '</a>';
	$html .= '<span alt="f223" class="edd-help-tip dashicons dashicons-editor-help" title="<strong>' . __( 'Purge Cache', 'edd-free-downloads' ) . '</strong>: ' . __( 'This will purge all files currently cached by Free Downloads. This does not affect any files hosted locally, it only clears the local copies of remotely hosted files and cached compressed files generated by direct download or auto download.', 'edd-free-downloads' ) . '"></span>';

	echo $html;
}
add_action( 'edd_free_downloads_display_purge_cache', 'edd_free_downloads_display_purge_cache' );


/**
 * Purge all cached files
 *
 * @since       2.1.0
 * @return      void
 */
function edd_free_downloads_purge_cache() {
	if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'edd_free_downloads_cache_nonce' ) ) {
		wp_die( __( 'Nonce verification failed!', 'edd-free-downloads' ), __( 'Oops!', 'edd-free-downloads' ) );
	}

	$upload_dir  = wp_upload_dir();
	$upload_dir  = trailingslashit( $upload_dir['basedir'] . '/edd-free-downloads-cache' );

	$files = glob( $upload_dir . '/*' );
	foreach ( $files as $file ) {
		if ( is_file( $file ) && ! strstr( $file, 'index.php' ) ) {
			unlink( $file );
		}
	}

	$redirect_url = add_query_arg( array( 'edd_action' => null, '_wpnonce' => null, 'edd-message' => 'fd-cache-purged' ) );
	wp_safe_redirect( $redirect_url );
	die();
}
add_action( 'edd_free_downloads_purge_cache', 'edd_free_downloads_purge_cache' );


/**
 * Clear cached files for a given download
 *
 * @since       2.1.0
 * @return      void
 */
function edd_free_downloads_delete_cached_files() {
	if ( ! isset( $_GET['post'] ) ) {
		wp_die( __( 'No download specified!', 'edd-free-downloads' ), __( 'Oops!', 'edd-free-downloads' ) );
	}

	if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'edd_free_downloads_cache_nonce' ) ) {
		wp_die( __( 'Nonce verification failed!', 'edd-free-downloads' ), __( 'Oops!', 'edd-free-downloads' ) );
	}

	$upload_dir  = wp_upload_dir();
	$upload_dir  = trailingslashit( $upload_dir['basedir'] . '/edd-free-downloads-cache' );
	$download_id = absint( $_GET['post'] );

	// Delete cached remote files
	$files = edd_free_downloads_get_files( $download_id );

	foreach ( $files as $file_name => $file_path ) {
		if( file_exists( $upload_dir . $file_name ) ) {
			unlink( $upload_dir . $file_name );
		}
	}

	// Delete cached zip file
	$zip_name = apply_filters( 'edd_free_downloads_zip_name', strtolower( str_replace( ' ', '-', get_bloginfo( 'name' ) ) ) . '-bundle-' . $download_id . '.zip' );
	$zip_file = $upload_dir . '/' . $zip_name;

	if( file_exists( $zip_file ) ) {
		unlink( $zip_file );
	}

	$redirect_url = add_query_arg( array( 'edd-action' => null, '_wpnonce' => null, 'edd-message' => 'fd-files-deleted' ) );
	wp_safe_redirect( $redirect_url );
	die();
}
add_action( 'edd_free_downloads_delete_cached_files', 'edd_free_downloads_delete_cached_files' );

/**
 * Get the notes for a given download
 *
 * @since       2.1.0
 * @return      void
 */
function edd_free_downloads_get_notes() {
	if ( ! $_POST['download_id'] ) {
		die( '-1' );
	}

	$download_id = intval( $_POST['download_id'] );
	$download    = get_post( $download_id );

	if ( 'download' != $download->post_type ) {
		die( '-2' );
	}

	$note    = '';
	$title   = '';
	$content = '';

	if ( ! edd_get_option( 'edd_free_downloads_disable_global_notes', false ) ) {
		$title   = edd_get_option( 'edd_free_downloads_notes_title', '' );
		$content = edd_get_option( 'edd_free_downloads_notes', '' );
	}

	if ( $download_title = get_post_meta( $download_id, '_edd_free_downloads_notes_title', true ) ) {
		$title = $download_title;
	}

	if ( $download_note = get_post_meta( $download_id, '_edd_free_downloads_notes', true ) ) {
		$content = $download_note;
	}

	if ( $title !== '' || $content !== '' ) {
		$note = array(
			'title'   => $title,
			'content' => wpautop( stripslashes( $content ) )
		);

		$note = json_encode( $note );
	}

	echo $note;
	edd_die();
}
add_action( 'wp_ajax_edd_free_downloads_get_notes', 'edd_free_downloads_get_notes' );
add_action( 'wp_ajax_nopriv_edd_free_downloads_get_notes', 'edd_free_downloads_get_notes' );

/**
 * This function and related actions are called via an AJAX
 * request to trigger the modal for Free Downloads.
 *
 * @return string HTML output for the Free Downloads modal
 */
function edd_free_downloads_get_modal() {

	edd_get_template_part( 'download', 'modal' );

	edd_die();
}
add_action( 'wp_ajax_edd_free_downloads_get_modal', 'edd_free_downloads_get_modal' );
add_action( 'wp_ajax_nopriv_edd_free_downloads_get_modal', 'edd_free_downloads_get_modal' );








function edd_free_downloads_get_file_path() {

	$post_id = ! empty( $_GET['download_id'] ) ? intval( $_GET['download_id'] ) : false;

	if ( false !== $post_id ) {

		$download_file = get_post_meta( $post_id, 'edd_download_files', true );
		$download_file = wp_list_pluck( $download_file, 'file' );

		/**
		 * If we have more than one file we will zip them up here
		 * and update the URL appropriately.
		 */
		if ( count( $download_file ) > 1 ) {
			$download_file = edd_free_downloads_compress_files( $download_file );
			$download_file = str_replace( WP_CONTENT_DIR, WP_CONTENT_URL, $download_file );
		} else {
			/**
			 * We are dealing with a single file download
			 * @var [type]
			 */
			if ( isset( $download_file[1] ) ) {
				$download_file = $download_file[1];
			} else {
				/**
				 * Accounting for purchases with no download file
				 */
				$download_file = '';
			}
		}

		/**
		 * Note that wp_send_json also does die()
		 */
		wp_send_json( $download_file );
	}

	edd_die();
}
add_action( 'wp_ajax_edd_free_downloads_get_file_path', 'edd_free_downloads_get_file_path' );
add_action( 'wp_ajax_nopriv_edd_free_downloads_get_file_path', 'edd_free_downloads_get_file_path' );












/**
 * This function creates a wrapper div needed to allow for
 * javascript targeting of the inner modal elements
 */
function edd_free_downloads_add_modal_wrapper() {
	echo '<div class="edd-free-downloads-modal-wrapper edd-free-downloads"><span class="edd-loading"></span><div id="edd-free-downloads-modal" style="display:none"></div></div>';
}
add_action( 'wp_footer', 'edd_free_downloads_add_modal_wrapper' );
