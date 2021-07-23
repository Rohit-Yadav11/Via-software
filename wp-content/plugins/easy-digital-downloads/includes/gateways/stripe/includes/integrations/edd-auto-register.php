<?php
/**
 * Integration: Auto Register
 *
 * @package EDD_Stripe
 * @since 2.8.0
 */

/**
 * Checks if the payment being created is by the Stripe gateway.
 * Ensures the registered user is logged in automatically if so.
 *
 * Added slightly early to not override anything more specific.
 *
 * @since 2.8.0
 *
 * @param bool $maybe_login Determines if the user should be logged in after registration.
 * @return bool
 */
function edds_auto_register_login_user( $maybe_login ) {

	if ( false === edd_is_gateway_active( 'stripe' ) ) {
		return $maybe_login;
	}
	// If the request originated from the Stripe gateway on the Checkout log inthe registered user.
	if ( isset( $_POST['action'] ) && 'edds_create_payment' === $_POST['action'] ) {
		return true;
	}

	return $maybe_login;
}
add_filter( 'edd_auto_register_login_user', 'edds_auto_register_login_user', 5 );
