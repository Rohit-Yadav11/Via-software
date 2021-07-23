<?php
/**
 * Scripts
 *
 * @package     EDD\FreeDownloads\Scripts
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load scripts
 *
 * @since       1.0.0
 * @return      void
 */
function edd_free_downloads_scripts() {
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	// Defining 'null' here for the 4th parameter for 'version' results in no version string attached, which is ideal here.
	wp_register_script( 'edd-free-downloads-mobile', EDD_FREE_DOWNLOADS_URL . 'assets/js/isMobile'. $suffix . '.js', array( 'jquery' ), null, true );
	wp_enqueue_script( 'edd-free-downloads-mobile' );

	wp_register_style( 'edd-free-downloads', EDD_FREE_DOWNLOADS_URL . 'assets/css/style' . $suffix . '.css', array(), EDD_FREE_DOWNLOADS_VER );
	wp_enqueue_style( 'edd-free-downloads' );

	wp_register_script( 'edd-free-downloads', EDD_FREE_DOWNLOADS_URL . 'assets/js/edd-free-downloads' . $suffix . '.js', array( 'jquery' ), EDD_FREE_DOWNLOADS_VER, true );
	wp_enqueue_script( 'edd-free-downloads' );

	// Localize some settings for use in Javascript.
	$close_button            = edd_get_option( 'edd_free_downloads_close_button', false );
	$close_button            = ( $close_button ? 'box' : 'overlay' );
	$download_label          = edd_get_option( 'edd_free_downloads_button_label', __( 'Download Now', 'edd-free-downloads' ) );
	$guest_checkout_disabled = edd_no_guest_checkout();
	$on_complete_handler     = edd_get_option( 'edd_free_downloads_on_complete', 'default' );

	wp_localize_script( 'edd-free-downloads', 'edd_free_downloads_vars', array(
		'close_button'            => $close_button,
		'user_registration'       => ( edd_get_option( 'edd_free_downloads_user_registration', false ) && ! class_exists( 'EDD_Auto_Register' ) ) ? 'true' : 'false',
		'require_name'            => edd_get_option( 'edd_free_downloads_require_name', false ) ? 'true' : 'false',
		'download_loading'        => __( 'Please Wait... ', 'edd-free-downloads' ),
		'download_label'          => $download_label,
		'modal_download_label'    => edd_get_option( 'edd_free_downloads_modal_button_label', __( 'Download Now', 'edd-free-downloads' ) ),
		'has_ajax'                => edd_is_ajax_enabled(),
		'ajaxurl'                 => edd_get_ajax_url(),
		'mobile_url'              => esc_url( add_query_arg( array( 'edd-free-download' => 'true' ) ) ),
		'form_class'              => apply_filters( 'edd_free_downloads_form_class', 'edd_purchase_submit_wrapper' ),
		'bypass_logged_in'        => edd_get_option( 'edd_free_downloads_bypass_logged_in', false ) && is_user_logged_in() ? 'true' : 'false',
		'is_download'             => ( is_singular( 'download' ) ? 'true' : 'false' ),
		'edd_is_mobile'           => wp_is_mobile(),
		'success_page'            => edd_get_success_page_uri(),
		'guest_checkout_disabled' => $guest_checkout_disabled,
		'email_verification'      => edd_free_downloads_verify_email(),
		'on_complete_handler'     => $on_complete_handler,
		'on_complete_delay'       => apply_filters( 'edd_free_downloads_on_complete_handler_delay', 2000 ),
	) );
}
add_action( 'wp_enqueue_scripts', 'edd_free_downloads_scripts' );


/**
 * Load admin scripts
 *
 * @since       1.3.0
 * @return      void
 */
function edd_free_downloads_admin_scripts() {
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	wp_register_style( 'edd-free-downloads', EDD_FREE_DOWNLOADS_URL . 'assets/css/admin'. $suffix . '.css', array(), EDD_FREE_DOWNLOADS_VER );
	wp_enqueue_style( 'edd-free-downloads' );

	wp_register_script( 'edd-free-downloads', EDD_FREE_DOWNLOADS_URL . 'assets/js/admin'. $suffix . '.js', array( 'jquery' ), EDD_FREE_DOWNLOADS_VER );
	wp_enqueue_script( 'edd-free-downloads' );
}
add_action( 'admin_enqueue_scripts', 'edd_free_downloads_admin_scripts' );
