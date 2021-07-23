<?php
/**
 * Manage deprecations.
 *
 * @package EDD_Stripe
 * @since   2.7.0
 */

/**
 * Process stripe checkout submission
 *
 * @access      public
 * @since       1.0
 * @return      void
 */
function edds_process_stripe_payment( $purchase_data ) {
	_edd_deprecated_function( 'edds_process_stripe_payment', '2.7.0', 'edds_process_purchase_form', debug_backtrace() );

	return edds_process_purchase_form( $purchase_data );
}

/**
 * Database Upgrade actions
 *
 * @access      public
 * @since       2.5.8
 * @return      void
 */
function edds_plugin_database_upgrades() {
	_edd_deprecated_function(
		__FUNCTION__,
		'2.8.1',
		null,
		debug_backtrace()
	);

	edd_stripe()->database_upgrades();
}

/**
 * Internationalization
 *
 * @since       1.6.6
 * @return      void
 */
function edds_textdomain() {
	_edd_deprecated_function(
		__FUNCTION__,
		'2.8.1',
		null,
		debug_backtrace()
	);

	edd_stripe()->load_textdomain();
}

/**
 * Register our payment gateway
 *
 * @since       1.0
 * @return      array
 */
function edds_register_gateway( $gateways ) {
	_edd_deprecated_function(
		__FUNCTION__,
		'2.8.1',
		null,
		debug_backtrace()
	);

	return edd_stripe()->register_gateway( $gateways );
}
