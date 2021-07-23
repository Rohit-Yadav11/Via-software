<?php
/**
 * Plugin Name: Element Pack Lite - Addon for Elementor
 * Plugin URI: http://elementpack.pro/
 * Description: Element Pack is a packed of elementor widget. This plugin gives you extra widgets features for elementor page builder plugin.
 * Version: 2.8.0
 * Author: BdThemes
 * Author URI: https://bdthemes.com/
 * Text Domain: bdthemes-element-pack-lite
 * Domain Path: /languages
 * License: GPL3
 * Elementor requires at least: 3.0.0
 * Elementor tested up to: 3.1.1
 */


if ( ! function_exists( 'element_pack_pro_installed' ) ) {

	function element_pack_pro_installed() {

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

	    $file_path = 'bdthemes-element-pack/bdthemes-element-pack.php';
	    $installed_plugins = get_plugins();

	    return isset( $installed_plugins[ $file_path ] );
	}
}


if ( ! element_pack_pro_installed() ) {
	// Some pre define value for easy use
	define( 'BDTEP_VER', '2.8.0' );
	define( 'BDTEP__FILE__', __FILE__ );
	define( 'BDTEP_PNAME', basename( dirname(BDTEP__FILE__) ) );
	define( 'BDTEP_PBNAME', plugin_basename(BDTEP__FILE__) );
	define( 'BDTEP_PATH', plugin_dir_path( BDTEP__FILE__ ) );
	define( 'BDTEP_MODULES_PATH', BDTEP_PATH . 'modules/' );
	define( 'BDTEP_INC_PATH', BDTEP_PATH . 'includes/' );
	define( 'BDTEP_URL', plugins_url( '/', BDTEP__FILE__ ) );
	define( 'BDTEP_ASSETS_URL', BDTEP_URL . 'assets/' );
	define( 'BDTEP_ASSETS_PATH', BDTEP_PATH . 'assets/' );
	define( 'BDTEP_MODULES_URL', BDTEP_URL . 'modules/' );
	


	// Helper function here
	require(dirname(__FILE__).'/includes/helper.php');
	require(dirname(__FILE__).'/includes/utils.php');

	/**
	 * Plugin load here correctly
	 * Also loaded the language file from here
	 */
	function bdthemes_element_pack_load_plugin() {
	    load_plugin_textdomain( 'bdthemes-element-pack-lite', false, basename( dirname( __FILE__ ) ) . '/languages' );

		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', 'element_pack_lite_need_elementor' );
			return;
		}

		// Admin settings controller
	    require( BDTEP_PATH . 'includes/class-settings-api.php' );
	    // element pack admin settings here
	    require( BDTEP_PATH . 'includes/admin-settings.php' );
		// Element pack widget and assets loader
	    require( BDTEP_PATH . 'loader.php' );
	    // Notice class
	    require( BDTEP_PATH . '/includes/admin-notice.php' );
	}
	add_action( 'plugins_loaded', 'bdthemes_element_pack_load_plugin', 9 );
	
}

/**
 * Check Elementor installed and activated correctly
 */
function element_pack_lite_need_elementor() {
	$screen = get_current_screen();
	if ( isset( $screen->parent_file ) && 'plugins.php' === $screen->parent_file && 'update' === $screen->id ) {
		return;
	}

	$plugin = 'elementor/elementor.php';

	if ( _is_elementor_installed() ) {
		if ( ! current_user_can( 'activate_plugins' ) ) { return; }
		$activation_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin );
		$admin_message = '<p>' . esc_html__( 'Ops! Element Pack not working because you need to activate the Elementor plugin first.', 'bdthemes-element-pack-lite' ) . '</p>';
		$admin_message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $activation_url, esc_html__( 'Activate Elementor Now', 'bdthemes-element-pack-lite' ) ) . '</p>';
	} else {
		if ( ! current_user_can( 'install_plugins' ) ) { return; }
		$install_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ), 'install-plugin_elementor' );
		$admin_message = '<p>' . esc_html__( 'Ops! Element Pack not working because you need to install the Elementor plugin', 'bdthemes-element-pack-lite' ) . '</p>';
		$admin_message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $install_url, esc_html__( 'Install Elementor Now', 'bdthemes-element-pack-lite' ) ) . '</p>';
	}

	echo '<div class="error">' . $admin_message . '</div>';
}

/**
 * Check the elementor installed or not
 */
if ( ! function_exists( '_is_elementor_installed' ) ) {

    function _is_elementor_installed() {
        $file_path = 'elementor/elementor.php';
        $installed_plugins = get_plugins();

        return isset( $installed_plugins[ $file_path ] );
    }
}