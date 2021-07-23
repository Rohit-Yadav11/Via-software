<?php
/**
 * Template Actions
 *
 * @package     EDD\FreeDownloads\Template\Actions
 * @since       2.0.0
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Listen for edd-free-download queries and handle accordingly
 *
 * @since       1.1.0
 * @return      void
 */
function edd_free_downloads_display_redirect( $template ) {

	global $wp_query;

	// Check for edd-free-download variable
	if ( ! isset( $wp_query->query_vars['edd-free-download'] ) ) {
		return $template;
	}

	// Make sure we have a download InvalidArgumentException
	if ( ! isset( $wp_query->query_vars['download_id'] ) ) {
		return $template;
	}

	$template = edd_locate_template( 'download-redirect.php' );

	return $template;

}
add_filter( 'template_include', 'edd_free_downloads_display_redirect' );
