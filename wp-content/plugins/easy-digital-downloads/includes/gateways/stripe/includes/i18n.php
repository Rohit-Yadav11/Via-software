<?php
/**
 * Internationalization
 *
 * @package EDD_Stripe
 * @copyright Copyright (c) 2020, Sandhills Development, LLC
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since 2.8.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns a list of error codes and corresponding localized error messages.
 *
 * @since 2.8.0
 *
 * @return array $error_list List of error codes and corresponding error messages.
 */
function edds_get_localized_error_messages() {
	$error_list = array(
		'invalid_number'           => __( 'The card number is not a valid credit card number.', 'easy-digital-downloads' ),
		'invalid_expiry_month'     => __( 'The card\'s expiration month is invalid.', 'easy-digital-downloads' ),
		'invalid_expiry_year'      => __( 'The card\'s expiration year is invalid.', 'easy-digital-downloads' ),
		'invalid_cvc'              => __( 'The card\'s security code is invalid.', 'easy-digital-downloads' ),
		'incorrect_number'         => __( 'The card number is incorrect.', 'easy-digital-downloads' ),
		'incomplete_number'        => __( 'The card number is incomplete.', 'easy-digital-downloads' ),
		'incomplete_cvc'           => __( 'The card\'s security code is incomplete.', 'easy-digital-downloads' ),
		'incomplete_expiry'        => __( 'The card\'s expiration date is incomplete.', 'easy-digital-downloads' ),
		'expired_card'             => __( 'The card has expired.', 'easy-digital-downloads' ),
		'incorrect_cvc'            => __( 'The card\'s security code is incorrect.', 'easy-digital-downloads' ),
		'incorrect_zip'            => __( 'The card\'s zip code failed validation.', 'easy-digital-downloads' ),
		'invalid_expiry_year_past' => __( 'The card\'s expiration year is in the past', 'easy-digital-downloads' ),
		'card_declined'            => __( 'The card was declined.', 'easy-digital-downloads' ),
		'processing_error'         => __( 'An error occurred while processing the card.', 'easy-digital-downloads' ),
		'invalid_request_error'    => __( 'Unable to process this payment, please try again or use alternative method.', 'easy-digital-downloads' ),
		'email_invalid'            => __( 'Invalid email address, please correct and try again.', 'easy-digital-downloads' ),
	);

	/**
	 * Filters the list of available error codes and corresponding error messages.
	 *
	 * @since 2.8.0
	 *
	 * @param array $error_list List of error codes and corresponding error messages.
	 */
	$error_list = apply_filters( 'edds_get_localized_error_list', $error_list );

	return $error_list;
}

/**
 * Returns a localized error message for a corresponding Stripe
 * error code.
 *
 * @link https://stripe.com/docs/error-codes
 *
 * @since 2.8.0
 *
 * @param string $error_code Error code.
 * @param string $error_message Original error message to return if a localized version does not exist.
 * @return string $error_message Potentially localized error message.
 */
function edds_get_localized_error_message( $error_code, $error_message ) {
	$error_list = edds_get_localized_error_messages();

	if ( ! empty( $error_list[ $error_code ] ) ) {
		return $error_list[ $error_code ];
	}

	return $error_message;
}
