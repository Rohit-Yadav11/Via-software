<?php
/**
 * Email Functions
 *
 * @package     EDD\FreeDownloads\Emails
 * @since       2.2.0
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add the {free_downloads_verification_link} tag to the EDD Email tags
 *
 * @since 2.2.0
 * @return void
 */
function edd_free_downloads_register_email_tags() {
	edd_add_email_tag( 'free_downloads_verification_link', __( 'Insert the free downloads confirmation link', 'edd-free-downloads' ), 'edd_free_downloads_get_confirmation_link' );
	edd_add_email_tag( 'free_download_name', __( 'Insert the name of the item delivered via Free Downloads', 'edd-free-downloads' ), 'edd_free_downloads_get_download_name' );
}
add_action( 'edd_add_email_tags', 'edd_free_downloads_register_email_tags', 100 );

/**
 * Retrieve the download confirmation link for a payment ID
 *
 * @since 2.2.0
 * @param int $payment_id
 *
 * @return string
 */
function edd_free_downloads_get_confirmation_link( $payment_id = 0 ) {
	if ( ! is_numeric( $payment_id ) || empty( $payment_id ) ) {
		return '';
	}

	$payment = edd_get_payment( $payment_id );
	if ( ! is_a( $payment, 'EDD_Payment' ) ) {
		return '';
	}

	$token      = wp_hash( $payment->key, 'nonce' );
	$base_url   = home_url( 'index.php' );
	$expiration = current_time( 'timestamp' ) + ( edd_get_option( 'download_link_expiration', 24 ) * HOUR_IN_SECONDS );
	$args       = array(
		'edd_action'      => 'free_download_verify',
		'token'           => $token,
		'key'             => $payment->key,
		'ttl'             => $expiration,
		'verify_download' => wp_hash( $payment->key . $token . $expiration, 'nonce' ),
	);

	$url = add_query_arg( $args, $base_url );

	return $url;
}

/**
 * Run the verification process for when a user clicks the link in the verification email.
 *
 * @since 2.2.0
 * @uses edd_free_downloads_complete_download()
 */
function edd_free_download_verify() {
	$payment_key = ! empty( $_GET['key'] )   ? sanitize_text_field( $_GET['key'] )       : false;
	$token       = ! empty( $_GET['token'] ) ? sanitize_text_field( $_GET['token'] )     : false;
	$payment     = ! empty( $payment_key )   ? edd_get_payment_by( 'key', $payment_key ) : false;
	$expiration  = ! empty( $_GET['ttl'] )   ? absint( $_GET['ttl'] )                    : false;
	$expired     = current_time( 'timestamp' ) > $expiration;

	$checksum    = ! empty( $_GET['verify_download'] ) ? sanitize_text_field( $_GET['verify_download'] ) : '';
	$verified    = false;
	$challenge   = wp_hash( $payment_key . $token . $expiration, 'nonce' );
	if ( hash_equals( $checksum, $challenge ) ) {
		$verified = true;
	}

	$error_message = false;

	if ( ! $verified || $expired ) {
		if ( ! empty( $payment ) ) {
			$download_id   = $payment->downloads[0]['id'];
			$download      = new EDD_Download( $download_id );

			if ( $expired ) {
				$error_message = sprintf(
					__( 'Your verification link for <a href="%s">%s</a> has expired', 'edd-free-downloads' ),
					get_permalink( $download_id ),
					$download->get_name()
				);
			} else {
				$error_message = sprintf(
					__( 'Error processing download of <a href="%s">%s</a>', 'edd-free-downloads' ),
					get_permalink( $download_id ),
					$download->get_name()
				);
			}

		} else {
			$error_message = __( 'Error processing download. Please contact support.', 'edd-free-downloads' );
		}
	}

	if ( ! empty( $error_message ) ) {
		wp_die( $error_message, __( 'Error', 'edd-free-downloads' ), 403 );
	}

	// Verify that the token provided matches that of the payment that exists.
	if ( ! hash_equals( wp_hash( $payment->key, 'nonce' ), $token ) ) {
		wp_die( __( 'Validation failed.', 'edd-free-downloads' ), __( 'Validation error', 'edd-free-downloads' ) );
	}

	if ( 'publish' !== $payment->status ) {

		do_action( 'edd_free_downloads_pre_complete_payment', $payment->ID );

		$payment->status = 'publish';
		$payment->save();

		do_action( 'edd_free_downloads_post_complete_payment', $payment->ID );

		$payment->add_note( __( 'Free Downloads email verification complete.', 'edd-free-downloads' ) );
	}

	edd_free_downloads_complete_download( $payment );

}
add_action( 'edd_free_download_verify', 'edd_free_download_verify' );

/**
 * Retrieve the download name for a Free Download 'purchase'.
 *
 * @since 2.2.0
 * @param int $payment_id
 *
 * @return string
 */
function edd_free_downloads_get_download_name( $payment_id = 0 ) {
	if ( ! is_numeric( $payment_id ) || empty( $payment_id ) ) {
		return '';
	}

	$payment = edd_get_payment( $payment_id );
	if ( ! is_a( $payment, 'EDD_Payment' ) ) {
		return '';
	}

	$download_id = $payment->downloads[0]['id'];
	$download    = new EDD_Download( $download_id );

	return $download->get_name();
}

/**
 * Return the setting of the email verification setting.
 *
 * @since 2.2.0
 * @return bool
 */
function edd_free_downloads_verify_email() {
	$verify_email = edd_get_option( 'edd_free_downloads_require_verification', false );
	$verify_email = is_user_logged_in() ? false : $verify_email;

	return (bool) apply_filters( 'edd_free_downloads_require_verification', $verify_email );
}

/**
 * Return the verification message setting.
 *
 * @since 2.2.0
 * @return string
 */
function edd_free_downloads_verify_message() {
	$default_message      = __( 'An email will be sent to the provided address to complete your download.', 'edd-free-downloads' );
	$verification_message = edd_get_option( 'edd_free_downloads_require_verification_message', $default_message );
	return apply_filters( 'edd_free_downloads_verification_message', $verification_message );
}

/**
 * Return the verification email subject setting.
 *
 * @since 2.2.0
 * @return string
 */
function edd_free_downloads_verification_subject() {
	$default_subject      = __( 'Confirm your free download.', 'edd-free-downloads' );
	$verification_subject = edd_get_option( 'edd_free_downloads_verification_email_subject', $default_subject );
	return apply_filters( 'edd_free_downloads_verification_subject', $verification_subject );
}

/**
 * Return the verification email contents.
 *
 * @since 2.2.0
 * @return string
 */
function edd_free_downloads_verification_email() {
	$default_message    = __( "Please click the following link to complete your download.\n\n" . "{free_downloads_verification_link}", 'edd-free-downloads' );
	$verification_email = edd_get_option( 'edd_free_downloads_verification_email', $default_message );
	return apply_filters( 'edd_free_downloads_verification_email', $verification_email );
}

/**
 * Sends the Free Downloads verification email.
 *
 * @since 2.2.0
 *
 * @param object  $payment  EDD_Payment The payment generated for this email.
 * @param string  $to_email The email address to send the payment to.
 *
 * @return bool
 */
function edd_free_downloads_send_verification( $payment = null, $to_email = '' ) {
	if ( empty( $payment ) || empty( $to_email ) ) {
		return false;
	}

	if ( ! is_a( $payment, 'EDD_Payment' ) ) {
		return false;
	}

	if ( ! is_email( $to_email ) ) {
		return false;
	}

	$from_name   = edd_get_option( 'from_name', wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) );
	$from_name   = apply_filters( 'edd_free_downloads_verification_from_name', $from_name, $payment->ID );

	$from_email  = edd_get_option( 'from_email', get_bloginfo( 'admin_email' ) );
	$from_email  = apply_filters( 'edd_free_downloads_verification_from_address', $from_email, $payment->ID );

	$subject     = edd_free_downloads_verification_subject();
	$subject     = apply_filters( 'edd_free_downloads_verification_subject', wp_strip_all_tags( $subject ), $payment->ID );
	$subject     = edd_do_email_tags( $subject, $payment->ID );

	$headers     = "From: " . stripslashes_deep( html_entity_decode( $from_name, ENT_COMPAT, 'UTF-8' ) ) . " <$from_email>\r\n";
	$headers    .= "Reply-To: ". $from_email . "\r\n";
	$headers    .= "Content-Type: text/html; charset=utf-8\r\n";
	$headers     = apply_filters( 'edd_free_downloads_verification_email_headers', $headers, $payment->ID );

	$message     = edd_free_downloads_verification_email();
	$message     = make_clickable( edd_do_email_tags( $message, $payment->ID ) );

	$emails = EDD()->emails;
	$emails->__set( 'from_name', $from_name );
	$emails->__set( 'from_email', $from_email );
	$emails->__set( 'headers', $headers );

	$sent = $emails->send( $to_email, $subject, $message );

	return $sent;
}

/**
 * When Free Downloads is about to mark a payment is complete, determine what email settings should be disabled.
 *
 * @since 2.2.0
 *
 * @param  $payment
 * @return void
 */
function edd_free_downloads_maybe_disable_emails( $payment ) {
	$disable_purchase_receipts   = (bool) edd_get_option( 'edd_free_downloads_disable_emails', false );
	$disable_admin_notifications = (bool) edd_get_option( 'edd_free_downloads_disable_admin_emails', false );

	// Disable purchase receipts
	if ( $disable_purchase_receipts ) {
		remove_action( 'edd_complete_purchase', 'edd_trigger_purchase_receipt', 999 );

		if ( function_exists( 'Receiptful' ) ) {
			remove_action( 'edd_complete_purchase', array( Receiptful()->email, 'send_transactional_email' ) );
		}

		// Disabling purchase receipt inherently disables admin notices as well.
		if ( ! $disable_admin_notifications ) {
			add_action( 'edd_free_downloads_post_complete_payment',  'edd_admin_email_notice', 10, 1 );
		}

	}

	// Check if admin notices should be sent, in case we are sending purchase confirmations.
	if ( $disable_admin_notifications ) {
		// If not, remove the action.
		remove_action( 'edd_admin_sale_notice', 'edd_admin_email_notice', 10, 2 );
	}
}
add_action( 'edd_free_downloads_pre_complete_payment', 'edd_free_downloads_maybe_disable_emails', 10, 1 );
