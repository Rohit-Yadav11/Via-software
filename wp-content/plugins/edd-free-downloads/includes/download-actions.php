<?php
/**
 * Download Actions
 *
 * @package     EDD\FreeDownloads\Download\Actions
 * @since       2.0.0
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Process downloads
 *
 * @since       1.0.0
 * @return      void
 */
function edd_free_download_process() {

	// No spammers please!
	if ( ! empty( $_POST['edd_free_download_check'] ) ) {
		wp_die( __( 'Bad spammer, no download!', 'edd-free-downloads' ), __( 'Oops!', 'edd-free-downloads' ) );
	}

	if ( ! isset( $_POST['edd_free_download_nonce'] ) || ! wp_verify_nonce( $_POST['edd_free_download_nonce'], 'edd_free_download_nonce' ) ) {
		wp_die( __( 'Cheatin&#8217; huh?', 'edd-free-downloads' ), __( 'Oops!', 'edd-free-downloads' ) );
	}

	if ( ! isset( $_POST['edd_free_download_email'] ) ) {
		wp_die( __( 'An internal error has occurred, please try again or contact support. Email address not provided.', 'edd-free-downloads' ), __( 'Oops!', 'edd-free-downloads' ), array( 'back_link' => true ) );
	}

	if ( ! is_user_logged_in() ) {
		// Bypass auto-registration
		if ( edd_get_option( 'edd_free_downloads_bypass_auto_register', false ) && class_exists( 'EDD_Auto_Register' ) ) {
			remove_action( 'edd_auto_register_insert_user', array( EDD_Auto_Register::get_instance(), 'email_notifications' ), 10, 3 );
			remove_action( 'edd_insert_payment', array( EDD_Auto_Register::get_instance(), 'maybe_insert_user' ), 10, 2 );
		}
	}

	if ( edd_get_option( 'edd_free_downloads_user_registration', false ) && ! is_user_logged_in() && ! class_exists( 'EDD_Auto_Register' ) ) {
		// If we are registering a user, make sure the required fields are filled out
		if( edd_get_option( 'edd_free_downloads_user_registration', false ) && ! class_exists( 'EDD_Auto_Register' ) ) {
			if ( ! isset( $_POST['edd_free_download_username'] ) || ! isset( $_POST['edd_free_download_pass'] ) || ! isset( $_POST['edd_free_download_pass2'] ) ) {
				wp_die( __( 'The username and password fields are required, please try again.', 'edd-free-downloads' ), __( 'Oops!', 'edd-free-downloads' ), array( 'back_link' => true ) );
			}
		}

		if ( $_POST['edd_free_download_pass'] != $_POST['edd_free_download_pass2'] ) {
			wp_die( __( 'Password and password confirmation fields don\'t match, please try again,', 'edd-free-downloads' ), __( 'Oops!', 'edd-free-downloads' ), array( 'back_link' => true ) );
		}

		// Make sure the username doesn't already exist
		$username = trim( $_POST['edd_free_download_username'] );

		if ( username_exists( $username ) ) {
			wp_die( __( 'The specified username already exists, please log in or try again.', 'edd-free-downloads' ), __( 'Oops!', 'edd-free-downloads' ), array( 'back_link' => true ) );
		} elseif ( ! edd_validate_username( $username ) ) {
			// Invalid username
			if ( is_multisite() ) {
				wp_die( __( 'Invalid username. Only lowercase letters (a-z) and numbers are allowed.', 'edd-free-downloads' ), __( 'Oops!', 'edd-free-downloads' ), array( 'back_link' => true ) );
			} else {
				wp_die( __( 'Invalid username.', 'edd-free-downloads' ), __( 'Oops!', 'edd-free-downloads' ), array( 'back_link' => true ) );
			}
		}

		// Make sure the email doesn't already exist
		if ( email_exists( $_POST['edd_free_download_email'] ) ) {
			wp_die( __( 'The specified email has already been used, please log in or try again.', 'edd-free-downloads' ), __( 'Oops!', 'edd-free-downloads' ), array( 'back_link' => true ) );
		}
	}

	$email = sanitize_email( trim( $_POST['edd_free_download_email'] ) );
	$user  = get_user_by( 'email', $email );

	if ( ! is_email( $_POST['edd_free_download_email'] ) || ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
		/**
		 * @todo  Update translation files
		 */
		wp_die( esc_html__( 'Please enter a valid email address.', 'edd-free-downloads' ), esc_html__( 'Oops!', 'edd-free-downloads' ) );
	}

	// No banned emails please!
	if ( edd_is_email_banned( $email ) ) {
		/**
		 * @todo  Update translation files
		 */
		wp_die( esc_html__( 'Your email address or domain is not allowed to download content. Please contact support if you feel this is an error.', 'edd-free-downloads' ), esc_html__( 'Oops!', 'edd-free-downloads' ) );
	}

	// If privacy policy is required, verify it's checked.
	if ( edd_get_option( 'edd_free_downloads_display_privacy_policy_agreement', false ) && empty( $_POST['edd_free_download_privacy_agreement'] ) ) {
		wp_die( esc_html__( 'You must agree to the privacy policy to download this content.', 'edd-free-downloads' ), esc_html__( 'Oops!', 'edd-free-downloads' ) );
	}

	$download_id = isset( $_POST['edd_free_download_id'] ) ? intval( $_POST['edd_free_download_id'] ) : false;
	/**
	 * @todo  Update translation files
	 */
	if ( empty( $download_id ) ) {
		wp_die( esc_html__( 'An internal error has occurred, please try again or contact support. $download_id is empty', 'edd-free-downloads' ), esc_html__( 'Oops!', 'edd-free-downloads' ) );
	}

	$download = get_post( $download_id );
	// Bail if this isn't a valid download
	/**
	 * @todo  update translation files
	 */
	if ( ! $download instanceof WP_Post ) {
		wp_die( esc_html__( 'An internal error has occurred, please try again or contact support. Download is not a valid object', 'edd-free-downloads' ), esc_html__( 'Oops!', 'edd-free-downloads' ) );
	}

	/**
	 * @todo  update translation files
	 */
	if ( 'download' != $download->post_type ) {
		wp_die( esc_html__( 'An internal error has occurred, please try again or contact support. Invalid object type', 'edd-free-downloads' ), esc_html__( 'Oops!', 'edd-free-downloads' ) );
	}

	// Bail if this isn't a published download (or the current user can't edit it)
	/**
	 * @todo  update translation files
	 */
	if ( ! current_user_can( 'edit_post', $download->ID ) && 'publish' != $download->post_status ) {
		wp_die( esc_html__( 'An internal error has occurred, please try again or contact support. User does not have access to this download or the download is not available', 'edd-free-downloads' ), esc_html__( 'Oops!', 'edd-free-downloads' ) );
	}

	if ( isset( $_POST['edd_free_download_fname'] ) ) {
		$user_first = sanitize_text_field( $_POST['edd_free_download_fname'] );
	} else {
		$user_first = $user ? $user->first_name : '';
	}

	if ( isset( $_POST['edd_free_download_lname'] ) ) {
		$user_last = sanitize_text_field( $_POST['edd_free_download_lname'] );
	} else {
		$user_last = $user ? $user->last_name : '';
	}

	// Ensure there is not any session data that might cause bad data. See https://github.com/easydigitaldownloads/edd-free-downloads/issues/260
	add_filter( 'edd_fees_get_fees', '__return_empty_array' );

	$price_ids    = isset( $_POST['edd_free_download_price_id'] ) ? $_POST['edd_free_download_price_id'] : false;

	$payment = new EDD_Payment;
	$payment->first_name = $user_first;
	$payment->last_name  = $user_last;
	$payment->user_id    = $user ? $user->ID : 0;
	$payment->email      = $email;
	$payment->total      = 0;
	$payment->gateway    = 'manual';
	$payment->status     = 'pending';

	if ( ! $price_ids && isset( $_GET['price_ids'] ) ) {
		$price_ids = sanitize_text_field( $_GET['price_ids'] );
	}

	if ( isset( $price_ids ) && is_array( $price_ids ) ) {
		foreach ( $price_ids as $cart_id => $price_id ) {
			/**
			 * This logic handles multi-purchase mode with multiple $price_ids selected.
			 *
			 * $price_ids should be an array here.
			 */
			if ( ! edd_is_free_download( $download_id, $price_id ) ) {
				wp_die( __( 'The requested product is not a free product! Please try again or contact support.', 'edd-free-downloads' ), __( 'Oops!', 'edd-free-downloads' ) );
			}

			$payment->add_download( $download_id, array(
				'price_id'   => $price_id,
				'item_price' => 0
			) );
		}
	} elseif ( isset( $price_ids ) && ! is_array( $price_ids ) && ! empty( $price_ids ) ) {
		/**
		 * This logic deals with variable pricing and accounts for if there is a
		 * free product as well.
		 *
		 * In this situation there is only a single price id though the
		 * variable is still $price_ids.
		 *
		 * $price_ids should be a single integer here.
		 */
		if ( ! edd_is_free_download( $download_id, $price_ids ) ) {
			wp_die( __( 'The requested product is not a free product! Please try again or contact support.', 'edd-free-downloads' ), __( 'Oops!', 'edd-free-downloads' ) );
		}

		$payment->add_download( $download_id, array(
			'price_id'   => $price_ids,
			'item_price' => 0
		) );
	} else {
		/**
		 * This logic deal with a free download.
		 *
		 * In this situation there is no price id set thus we
		 * will set it to `false` below.
		 */
		if ( ! edd_is_free_download( $download_id ) ) {
			wp_die( __( 'An internal error has occurred, please try again or contact support. Invalid item.', 'edd-free-downloads' ), __( 'Oops!', 'edd-free-downloads' ) );
		}

		$payment->add_download( $download_id, array(
			'price_id'   => false, // We have a free download
		) );
	}

	$payment->save();

	if ( isset( $_POST['edd_free_download_optin'] ) ) {
		$payment->add_meta( 'edd_free_download_optin', true );
	}


	// If verification is not required, go ahead and mark the payment as 'completed'.
	$require_verification = edd_free_downloads_verify_email();
	if ( ! $require_verification ) {

		do_action( 'edd_free_downloads_pre_complete_payment', $payment->ID );

		$payment->status = 'publish';
		$payment->save();

		do_action( 'edd_free_downloads_post_complete_payment', $payment->ID );

	}
	$payment->add_note( __( 'Purchased through EDD Free Downloads', 'edd-free-downloads' ) );

	edd_empty_cart();

	$purchase_data['purchase_key'] = $payment->key;
	edd_set_purchase_session( $purchase_data );

	if ( edd_get_option( 'edd_free_downloads_user_registration', false ) && ! is_user_logged_in() && ! class_exists( 'EDD_Auto_Register' ) ) {
		$account = array(
			'user_login' => trim( sanitize_text_field( $_POST['edd_free_download_username'] ) ),
			'user_pass'  => trim( sanitize_text_field( $_POST['edd_free_download_pass'] ) ),
			'user_email' => $email,
			'user_first' => $user_first,
			'user_last'  => $user_last
		);

		edd_register_and_login_new_user( $account );
	}

	if ( $require_verification ) {
		$email_sent = edd_free_downloads_send_verification( $payment, $email );
		$results    = array();
		if ( $email_sent ) {
			$results['success'] = true;
			$results['message'] = __( 'Your email has been successfully sent.', 'edd-free-downloads' );

			$payment->add_note( sprintf( __( 'Free Downloads verification sent to %s.', 'edd-free-downloads' ), $email ) );
		} else {
			$results['success'] = false;
			$results['message'] = __( 'Your email failed to send, please try again.', 'edd-free-downloads' );

			$payment->add_note( sprintf( __( 'Free Downloads verification failed to send to %s.', 'edd-free-downloads' ), $email ) );
		}

		header('Content-Type: application/json');
		echo json_encode( $results );
		edd_die();
	}

	edd_free_downloads_complete_download( $payment );
}
add_action( 'edd_free_download_process', 'edd_free_download_process' );

/**
 * Do the final processing that delivers the files for a free downloads purchase.
 *
 * @since 2.2.0
 * @param EDD_Payment $payment The payment object
 */
function edd_free_downloads_complete_download( $payment = null ) {

	if ( empty( $payment ) || ! is_a( $payment, 'EDD_Payment' ) ) {
		wp_die( __( 'Error completing download request', 'edd-free-downloads' ), 403 );
	}

	// Determine if we have files to deliver
	$all_files = array();
	foreach ( $payment->downloads as $download ) {
		$price_id       = isset( $download['options']['price_id'] ) ? $download['options']['price_id'] : null;
		$download_files = edd_get_download_files( $download['id'], $price_id );

		if ( ! empty( $download_files ) ) {
			$all_files[] = $download_files;
		}
	}

	$on_complete        = edd_get_option( 'edd_free_downloads_on_complete', 'default' );
	$success_page       = edd_get_success_page_uri();
	$custom_url         = edd_get_option( 'edd_free_downloads_redirect', false );
	$custom_url         = $custom_url ? esc_url( $custom_url ) : $success_page;
	$mobile_custom_url  = edd_get_option( 'edd_free_downloads_mobile_redirect', false );
	$mobile_custom_url  = $mobile_custom_url ? esc_url( $mobile_custom_url ) : $success_page;
	$apple_custom_url   = edd_get_option( 'edd_free_downloads_apple_redirect', false );
	$apple_custom_url   = $apple_custom_url ? esc_url( $apple_custom_url ) : $success_page;
	$mobile_on_complete = edd_get_option( 'edd_free_downloads_mobile_on_complete', 'default' );
	$apple_on_complete  = edd_get_option( 'edd_free_downloads_apple_on_complete', 'default' );

	/**
	 * This accounts for logged in users when no download file is attached to the purchase.
	 *
	 * Also logic to make sure if the custom redirect option is set
	 */
	edd_debug_log( 'Free Downloads: Processing download, has downloads - ' . var_export( $all_files, true ) );
	if ( empty( $all_files ) && ! in_array( $on_complete, array( 'default', 'redirect' ) ) ) {
		$on_complete = 'default';
	}

	edd_debug_log( 'Free Downloads: Processing download with complete handler - ' . $on_complete );

	switch ( $on_complete ) {
		case 'default' :
			$redirect_url = add_query_arg( 'payment_key', $payment->key, $success_page );
			break;
		case 'redirect' :
			$redirect_url = add_query_arg( 'payment_key', $payment->key, $custom_url );
			break;
		case 'auto-download' :
			$redirect_url = add_query_arg( array(
				'edd_action' => 'free_downloads_process_download',
				'payment-id' => $payment->ID
			) );
			break;
	}

	if ( wp_is_mobile() ) {
		$mobile = new Mobile_Detect;
		$is_ios = $mobile->isiOS();

		if ( ( $is_ios && $apple_on_complete == 'default' ) || ( ! $is_ios && $mobile_on_complete == 'default' ) ) {
			$redirect_url = $redirect_url;
		} elseif( ( $is_ios && $apple_on_complete == 'confirmation' ) || ( ! $is_ios && $mobile_on_complete == 'confirmation' ) ) {
			$redirect_url = $success_page;
		} elseif( ( ! $is_ios && $mobile_on_complete == 'auto-download' ) ) {
			$redirect_url = add_query_arg( array(
				'edd_action' => 'free_downloads_process_download',
				'payment-id' => $payment->ID
			) );
		} elseif( ( $is_ios && $apple_on_complete == 'redirect' ) || ( ! $is_ios && $mobile_on_complete == 'redirect' ) ) {
			$redirect_url = $is_ios ? $apple_custom_url : $mobile_custom_url;
		}
	}

	$redirect_url = $redirect_url ? $redirect_url : $success_page;

	// Support Conditional Success Redirects
	if ( function_exists( 'edd_csr_is_redirect_active' ) && $redirect_url == $success_page ) {
		if ( edd_csr_is_redirect_active( edd_csr_get_redirect_id( $payment->cart_details[0]['id'] ) ) ) {
			$redirect_id = edd_csr_get_redirect_id( $payment->cart_details[0]['id'] );

			$redirect_url = edd_csr_get_redirect_page_id( $redirect_id );
			$redirect_url = get_permalink( $redirect_url );
		}
	}

	edd_debug_log( 'Free Downloads: Processing download with redirect URL of - ' . $redirect_url );
	wp_redirect( apply_filters( 'edd_free_downloads_redirect', $redirect_url, $payment->ID ) );
	edd_die();

}

/**
 * Process auto download
 *
 * @since       1.0.8
 * @return      void
 */
function edd_free_downloads_process_auto_download() {
	if ( ! isset( $_GET['payment-id'] ) && ! $_GET['download_id'] ) {
		return;
	}

	if ( ! function_exists( 'edd_get_file_ctype' ) ) {
		require_once EDD_PLUGIN_DIR . 'includes/process-download.php';
	}

	$download_files = array();

	if ( isset( $_GET['payment-id'] ) ) {
		$payment = edd_get_payment( intval( $_GET['payment-id'] ) );

		if ( ! empty( $payment->cart_details ) ) {
			foreach ( $payment->cart_details as $key => $item ) {
				$download_id = $item['id'];
				$archive_url = get_post_meta( $download_id, '_edd_free_downloads_file', true );

				if ( $archive_url && $archive_url != '' ) {
					$download_files = array_merge( $download_files, array( basename( $archive_url ) => $archive_url ) );
				} else {
					if ( array_key_exists( 'item_number', $item ) && isset( $item['item_number']['options']['price_id'] ) ) {
						$download_files = array_merge( $download_files, edd_free_downloads_get_files( $download_id, $item['item_number']['options']['price_id'] ) );
					} else {
						$download_files = array_merge( $download_files, edd_free_downloads_get_files( $download_id ) );
					}
				}
			}
		}
	} else {
		$download_id = absint( $_GET['download_id'] );
		$price_ids   = '';

		if ( isset( $_GET['price_ids'] ) && $_GET['price_ids'] != '' ) {
			$price_ids = explode( ',', trim( sanitize_text_field( $_GET['price_ids'] ) ) );
		} else {
			if ( edd_has_variable_prices( $download_id ) ) {
				$price_ids = edd_get_default_variable_price( $download_id );
			}
		}

		// If the user is logged in, it might be necessary to create a payment for this purchase.
		if ( is_user_logged_in() ) {

			/**
			 * Getting our logged in user info
			 *
			 * This do_action is needed to correctly get currently logged in
			 * user
			 */
			do_action( 'edd_pre_process_purchase' );

			$user_id  = get_current_user_id();
			$user     = get_userdata( $user_id );
			$customer = new EDD_Customer( $user->data->user_email );

			// Allow extensions to require a new payment record be made every time.
			$require_new_payment = apply_filters( 'edd_free_downloads_require_new_payment', false, $download_id, $user_id, $customer );

			// If multi-purchase mode is enabled and used, require a new payment.
			if ( ! empty( $price_ids ) && count( $price_ids ) > 1 ) {
				$require_new_payment = true;
			}

			$has_purchased = false;
			if ( false === $require_new_payment ) {
				$has_purchased = edd_has_user_purchased( $user_id, $download_id, $price_ids );
			}

			// If the user is already logged in, has purchased this item before and a new record isn't required, look it up.
			if ( false === $require_new_payment && true === $has_purchased ) {

				/**
				 * The logged in user has already "purchased" this item
				 */

				global $edd_logs;

				/**
				 * The function get_connected_logs() takes an array using the same
				 * parameters as get_posts
				 */
				$logs = $edd_logs->get_connected_logs( array(
					'suppress_filters' => false,
					'posts_per_page' => 1,
					'fields' => 'ids',
					'post_parent' => $download_id,
					'meta_query' => array( array(
						'key' => '_edd_log_payment_id',
						'value' => explode( ',', $customer->payment_ids ),
						'compare' => 'IN',
					) ),
				) );

				$payment_id = get_post_meta( $logs[0], '_edd_log_payment_id', true );
				$payment    = edd_get_payment( $payment_id );

			} else {
				/**
				 * actually creating a payment record
				 */
				$payment              = new EDD_Payment();
				$payment->user_id     = $customer->user_id;
				$payment->user_email  = $user->data->user_email;
				$payment->customer_id = $customer->id;

				// Triggers EDD_Payment to create a customer
				if ( empty( $customer->id ) ) {
					$payment->email = $payment->user_email;
					$payment->user_id = get_current_user_id();
					$payment->first_name = $user->first_name;
					$payment->last_name = $user->last_name;
				}

				if ( is_array( $price_ids ) ) {
					foreach ( $price_ids as $price_id ) {
						$payment->add_download( $download_id, array(
							'price_id'   => $price_id,
							'item_price' => 0
						) );
					}
				} else {
					$payment->add_download( $download_id, array( 'item_price' => 0 ) );
				}

				do_action( 'edd_free_downloads_pre_complete_payment', $payment->ID );

				$payment->gateway = 'manual';
				$payment->status  = 'publish';
				$payment->save();

				do_action( 'edd_free_downloads_post_complete_payment', $payment->ID );


				$payment->add_note( __( 'Purchased through EDD Free Downloads', 'edd-free-downloads' ) );
			}

		}

		$archive_url = get_post_meta( $download_id, '_edd_free_downloads_file', true );

		if ( $archive_url && $archive_url != '' ) {
			$download_files = array_merge( $download_files, array( basename( $archive_url ) => $archive_url ) );
		} elseif ( ! edd_is_bundled_product( $download_id ) ) {
			if ( ! empty( $price_ids ) ) {
				foreach ( $price_ids as $price_id ) {
					$download_files = array_merge( $download_files, edd_free_downloads_get_files( $download_id, $price_id ) );
				}
			} else {
				$download_files = array_merge( $download_files, edd_free_downloads_get_files( $download_id ) );
			}
		} else {
			$download_files = array_merge( $download_files, edd_free_downloads_get_files( $download_id ) );
		}
	}

	$on_complete        = edd_get_option( 'edd_free_downloads_on_complete', 'default' );
	$success_page       = edd_get_success_page_uri();
	$custom_url         = edd_get_option( 'edd_free_downloads_redirect', false );
	$custom_url         = $custom_url ? esc_url( $custom_url ) : $success_page;

	if( is_array( $download_files ) && count( $download_files ) > 0 && $on_complete === 'auto-download' ) {
		if ( count( $download_files ) > 1 ) {
			$download_url = edd_free_downloads_compress_files( $download_files, $download_id );
			$download_url = str_replace( WP_CONTENT_DIR, WP_CONTENT_URL, $download_url );

			// Prevent errors with edd_free_downloads_download_file()
			$hosted = 'multi';
		} else {

			$download_url = array_values( $download_files );
			$download_url = $download_url[0]['file'];

			$hosted = edd_free_downloads_get_host( $download_url );
			if ( 'local' !== $hosted ) {
				$download_url = edd_free_downloads_fetch_remote_file( $download_url, $hosted );
				$download_url = str_replace( WP_CONTENT_DIR, WP_CONTENT_URL, $download_url );
			}
		}

		/**
		 * If the user submitted information and a payment was created, log the file download.
		 *
		 * If the Direct Download option is enabled, and the user clicks the direct download link, no payment is created
		 * and we should bypass this attempt to log the file download as the necessary information is not present.
		 */
		if ( ! empty( $payment ) ) {
			/**
			 * Looping through files to create report logs
			 */
			foreach ( $download_files as $download_file ) {
				edd_record_download_in_log(
					$download_file['download_id'],
					$download_file['file_id'],
					array(
						'email' => $payment->email,
						'id'    => get_current_user_id(),
						'name'  => $payment->first_name . ' ' . $payment->last_name,
					),
					edd_get_ip(),
					$payment->ID
				);
			}
		}

		edd_free_downloads_download_file( $download_url, $hosted );
	} else {

		/**
		 * Our $download_files array is empty because there are no files and the user is logged in
		 */

		if ( ! is_user_logged_in() ) {
			return;
		}

		switch ( $on_complete ) {
			case 'redirect' :
				$redirect_url = add_query_arg( 'payment_key', $payment->key, $custom_url );
				break;
			case 'default' :
			default:
				$redirect_url = add_query_arg( 'payment_key', $payment->key, $success_page );
				break;
		}

		wp_safe_redirect( $redirect_url ); exit;

	} // End if logged in and "purchasing" a free product with no download file.
}

add_action( 'edd_free_downloads_process_download', 'edd_free_downloads_process_auto_download' );
