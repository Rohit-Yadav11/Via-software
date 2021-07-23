<?php
/**
 * Filters
 *
 * @package     EDD\FreeDownloads\Filters
 * @since       2.0.0
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Adds our templates dir to the EDD template stack
 *
 * @since       1.2.5
 * @param       array $paths The existing template stack
 * @return      array $paths The updated template stack
 */
function edd_free_downloads_add_template_stack( $paths ) {
	$paths[55] = EDD_FREE_DOWNLOADS_DIR . 'templates/';

	return $paths;
}
add_filter( 'edd_template_paths', 'edd_free_downloads_add_template_stack' );


/**
 * Add a new query var
 *
 * @since       1.1.0
 * @param       array $vars The current query vars
 * @return      array $vars The new query vars
 */
function edd_free_downloads_query_vars( $vars ) {
	$vars[] = 'download_id';

	return $vars;
}
add_filter( 'query_vars', 'edd_free_downloads_query_vars', -1 );


/**
 * Maybe override straight to checkout
 *
 * @since       1.1.0
 * @param       bool $ret Whether or not to go straight to checkout
 * @global      object $post The WordPress post object
 * @return      bool $ret Whether or not to go straight to checkout
 */
function edd_free_downloads_override_redirect( $ret ) {
	global $post;

	$id = get_the_ID();

	if ( is_single( $id ) && get_post_type( $id ) == 'download' && edd_is_free_download( $id ) ) {
		$ret = false;
	}

	return $ret;
}
add_filter( 'edd_straight_to_checkout', 'edd_free_downloads_override_redirect' );


/**
 * Override the EDD purchase form
 *
 * @since       2.0.0
 * @param       string $purchase_form The HTML for the existing purchase form
 * @param       array $args Arguments passed to the purchase form
 * @return      string $purchase_form Our updated purchase form
 */
function edd_free_downloads_purchase_download_form( $purchase_form, $args ) {

	$download_id   = absint( $args['download_id'] );
	$download_file = edd_get_download_files( $download_id );

	if ( edd_free_downloads_use_modal( $download_id ) && ! edd_has_variable_prices( $download_id ) ) {
		$purchase_form     = '';
		$form_id           = 'edd_purchase_' . $download_id;
		$download_label    = esc_attr( edd_get_option( 'edd_free_downloads_button_label', __( 'Download Now', 'edd-free-downloads' ) ) );
		$add_to_cart_label = edd_get_option( 'add_to_cart_text', __( 'Add to Cart', 'easy-digital-downloads' ) );
		$checkout_label    = edd_get_option( 'checkout_label', __( 'Purchase', 'easy-digital-downloads' ) );
		$buy_now_label     = edd_get_option( 'buy_now_text', __( 'Buy Now', 'easy-digital-downloads' ) );
		$button_style      = ( array_key_exists( 'style', $args ) && isset( $args['style'] ) ) ? $args['style'] : edd_get_option( 'button_style', 'button' );
		$button_color      = ( array_key_exists( 'color', $args ) && isset( $args['color'] ) ) ? $args['color'] : edd_get_option( 'checkout_color', 'blue' );
		$button_class      = ( array_key_exists( 'class', $args ) && isset( $args['class'] ) ) ? $args['class'] : '';

		// Maybe override text for shortcodes
		if( ! empty( $args['text'] ) ) {
			// Strip the price from the text argument
			$args['text'] = substr( $args['text'], strpos( $args['text'], '&nbsp;&ndash;&nbsp;' )+19, strlen( $args['text'] ) );

			if( ! empty( $args['text'] ) && ! in_array( $args['text'], array( $add_to_cart_label, $checkout_label, $buy_now_label ) ) ) {

				$download_label = $args['text'];

			}
		}


		if ( ! is_user_logged_in() || edd_get_option( 'edd_free_downloads_bypass_logged_in', false ) === false ) {

			$download_class = implode( ' ', array( $button_style, $button_color, $button_class, 'edd-submit edd-free-download edd-free-download-single' ) );

			if ( wp_is_mobile() ) {
				$href = esc_url( add_query_arg( array( 'edd-free-download' => 'true', 'download_id' => $args['download_id'] ) ) );
			} else {
				$href = '#edd-free-download-modal';
			}

			/**
			 * Output buffer is needed here to allow for do_action to work as expected
			 */
			ob_start(); ?>

				<form id="<?php echo esc_attr( $form_id ); ?>" class="edd_download_purchase_form">
					<?php do_action( 'edd_purchase_link_top', $download_id, $args );?>
					<div class="edd_free_downloads_form_class">
						<?php
						if ( edd_is_ajax_enabled() ) {
							echo apply_filters( 'edd_free_downloads_button_override', sprintf(
								'<a class="%1$s" href="' . $href . '" data-download-id="%3$s">%2$s</a>',
								$download_class,
								$download_label,
								$download_id
							), $download_id );
						} else {
							echo apply_filters( 'edd_free_downloads_button_override', sprintf(
								'<input type="submit" class="edd-no-js %1$s" name="edd_purchase_download" value="%2$s" href="' . $href . '" data-download-id="%3$s" />',
								$download_class,
								$download_label,
								$download_id
							), $download_id );
						}
						?>
					</div>
					<?php do_action( 'edd_purchase_link_end', $download_id, $args ); ?>
				</form>

			<?php

			$purchase_form = ob_get_clean();

		} else {

			$download_class = implode( ' ', array( edd_get_option( 'button_style', 'button' ), edd_get_option( 'checkout_color', 'blue' ), 'edd-submit' ) );

			do_action( 'edd_purchase_link_top', $download_id, $args );

			$purchase_form .= apply_filters( 'edd_free_downloads_button_override', sprintf(
				'<a href="#" class="edd-free-downloads-direct-download-link %1$s" data-download-id="%3$s">%2$s</a>',
				$download_class,
				$download_label,
				$download_id
			), $download_id );

			do_action( 'edd_purchase_link_end', $download_id, $args );

		}
	}

	return $purchase_form;

}
add_filter( 'edd_purchase_download_form', 'edd_free_downloads_purchase_download_form', 10, 2 );
