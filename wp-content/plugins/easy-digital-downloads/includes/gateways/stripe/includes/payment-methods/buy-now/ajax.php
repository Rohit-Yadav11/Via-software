<?php
/**
 * Buy Now: AJAX
 *
 * @package EDD_Stripe
 * @since   2.8.0
 */

/**
 * Adds a Download to the Cart on the `edds_add_to_cart` AJAX action.
 *
 * @since 2.8.0
 */
function edds_buy_now_ajax_add_to_cart() {
	$data = $_POST;

	if ( ! isset( $data['download_id'] ) || ! isset( $data['nonce'] ) ) {
		return wp_send_json_error( array(
			'message' => __( 'Unable to add item to cart.', 'easy-digital-downloads' ),
		) );
	}
		
	$download_id = absint( $data['download_id'] );
	$price_id    = absint( $data['price_id'] );
	$quantity    = absint( $data['quantity'] );

	$nonce       = sanitize_text_field( $data['nonce'] );
	$valid_nonce = wp_verify_nonce( $nonce, 'edd-add-to-cart-' . $download_id );

	if ( false === $valid_nonce ) {
		return wp_send_json_error( array(
			'message' => __( 'Unable to add item to cart.', 'easy-digital-downloads' ),
		) );
	}

	// Empty cart.
	edd_empty_cart();

	// Add individual item.
	edd_add_to_cart( $download_id, array(
		'quantity' => $quantity,
		'price_id' => $price_id,
	) );

	return wp_send_json_success( array(
		'checkout' => edds_buy_now_checkout(),
	) );
}
add_action( 'wp_ajax_edds_add_to_cart', 'edds_buy_now_ajax_add_to_cart' );
add_action( 'wp_ajax_nopriv_edds_add_to_cart', 'edds_buy_now_ajax_add_to_cart' );

/**
 * Empties the cart on the `edds_buy_now_empty_cart` AJAX action.
 *
 * @since 2.8.0
 */
function edds_buy_now_ajax_empty_cart() {
	edd_empty_cart();

	return wp_send_json_success();
}
add_action( 'wp_ajax_edds_empty_cart', 'edds_buy_now_ajax_empty_cart' );
add_action( 'wp_ajax_nopriv_edds_empty_cart', 'edds_buy_now_ajax_empty_cart' );
