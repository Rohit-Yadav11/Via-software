<?php
/**
 * Shortcodes
 *
 * @package     EDD\FreeDownloads\Shortcodes
 * @since       1.2.8
 */


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Free download shortcode
 *
 * @since       1.2.8
 * @param       array $atts Shortcode attributes
 * @param       string $content
 * @return      string Fully formatted purchase link
 */
function edd_free_downloads_shortcode( $atts, $content = null ) {
	$atts = shortcode_atts( array(
		'download_id' => isset( $atts['download_id'] ) ? $atts['download_id'] : false
	),
	$atts, 'edd_free_download' );

	$link = '<a href="#edd-free-download-modal" class="edd-free-download" data-download-id="' . $atts['download_id'] . '">' . $content . '</a>';

	return $link;
}
add_shortcode( 'edd_free_download', 'edd_free_downloads_shortcode' );
