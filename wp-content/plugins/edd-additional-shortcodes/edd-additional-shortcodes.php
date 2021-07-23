<?php
/*
Plugin Name: Easy Digital Downloads - Additional Shortcodes
Plugin URI: https://easydigitaldownloads.com/downloads/edd-additional-shortcodes/
Description: Adds additional shortcodes to EDD
Version: 1.4
Author: Easy Digital Downloads
Author URI: https://easydigitaldownloads.com
Text Domain: edd-asc-txt
*/

if ( ! defined ( 'ABSPATH' ) ) {
	exit;
}

class EDD_Additional_Shortcodes {
		/**
		 * @var         EDD_Additional_Shortcodes $instance The one true EDD_Additional_Shortcodes
		 * @since       1.0
		 */
		private static $instance;
		public $plugin_dir;
		public $plugin_url;
		public $version;

		public $shortcodes;
		public $integrations = array();

		private function __construct() {
			if ( !defined( 'EDD_VERSION' ) ) {
				return;
			}
		}

		/**
		 * Get active instance
		 *
		 * @access      public
		 * @since       1.0
		 * @return      object self::$instance The one true EDD_Additional_Shortcodes
		 */
		public static function instance() {
			if( !self::$instance ) {
				self::$instance = new EDD_Additional_Shortcodes();

				self::$instance->properties();
				self::$instance->includes();
				self::$instance->shortcodes = new EDD_Additional_Shortcodes_Core();

				if ( class_exists( 'EDD_Software_Licensing' ) ) {
					self::$instance->integrations['software_licensing'] = new EDD_Additional_Shortcodes_SL();
				}
			}

			return self::$instance;
		}

		/**
		 * Setup properties for the class
		 *
		 * @since 1.4
		 */
		private function properties() {
			self::$instance->version = '1.4';
			self::$instance->plugin_dir = trailingslashit( plugin_dir_path( __FILE__ ) );
			self::$instance->plugin_url = trailingslashit( plugin_dir_url( __FILE__ ) );
		}

		/**
		 * Include the necessary files
		 *
		 * @since 1.4
		 */
		private function includes() {
			include_once self::$instance->plugin_dir . 'includes/shortcodes.php';

			include_once self::$instance->plugin_dir . 'includes/backwards-compatibility.php';

			if ( class_exists( 'EDD_Software_Licensing' ) ) {
				include_once self::$instance->plugin_dir . 'includes/integrations/software-licensing.php';
			}
		}

		/**
		 * Maybe execute the shortcode as a shortcode, or simply return the content.
		 *
		 * @since 1.4
		 * @return string
		 */
		public function maybe_do_shortcode( $content ) {
			$do_shortcode = apply_filters( 'edd_asc_do_shortcode', true );

			if ( $do_shortcode ) {
				return do_shortcode( $content );
			}

			return $content;
		}
}

/**
 * Load the class, and allow it to be access later
 *
 * @since 1.4
 * @return object EDD_Additional_Shortcodes
 */
function edd_additional_shortcodes() {
	return EDD_Additional_Shortcodes::instance();
}
add_action( 'plugins_loaded', 'edd_additional_shortcodes' );