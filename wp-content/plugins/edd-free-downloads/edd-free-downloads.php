<?php
/**
 * Plugin Name:     Easy Digital Downloads - Free Downloads
 * Plugin URI:      https://easydigitaldownloads.com/downloads/free-downloads/
 * Description:     Adds better handling for directly downloading free products to EDD
 * Version:         2.3.5
 * Author:          Easy Digital Downloads
 * Author URI:      https://easydigitaldownloads.com
 * Text Domain:     edd-free-downloads
 *
 * @package         EDD\FreeDownloads
 * @author          Easy Digital Downloads, LLC
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! class_exists( 'EDD_Free_Downloads' ) ) {


	/**
	 * Main EDD_Free_Downloads class
	 *
	 * @since       1.0.0
	 */
	class EDD_Free_Downloads {


		/**
		 * @var         EDD_Free_Downloads $instance The one true EDD_Free_Downloads
		 * @since       1.0.0
		 */
		private static $instance;


		/**
		 * Get active instance
		 *
		 * @access      public
		 * @since       1.0.0
		 * @return      self::$instance The one true EDD_Free_Downloads
		 */
		public static function instance() {
			if ( ! self::$instance ) {
				self::$instance = new EDD_Free_Downloads();
				self::$instance->setup_constants();
				self::$instance->includes();
				self::$instance->load_textdomain();
				self::$instance->hooks();
			}

			return self::$instance;
		}


		/**
		 * Setup plugin constants
		 *
		 * @access      private
		 * @since       1.0.0
		 * @return      void
		 */
		private function setup_constants() {
			// Plugin version
			define( 'EDD_FREE_DOWNLOADS_VER', '2.3.5' );

			// Plugin path
			define( 'EDD_FREE_DOWNLOADS_DIR', plugin_dir_path( __FILE__ ) );

			// Plugin URL
			define( 'EDD_FREE_DOWNLOADS_URL', plugin_dir_url( __FILE__ ) );
		}


		/**
		 * Include necessary files
		 *
		 * @access      private
		 * @since       1.0.0
		 * @return      void
		 */
		private function includes() {
			require_once EDD_FREE_DOWNLOADS_DIR . 'includes/scripts.php';
			require_once EDD_FREE_DOWNLOADS_DIR . 'includes/functions.php';
			require_once EDD_FREE_DOWNLOADS_DIR . 'includes/actions.php';
			require_once EDD_FREE_DOWNLOADS_DIR . 'includes/filters.php';
			require_once EDD_FREE_DOWNLOADS_DIR . 'includes/download-actions.php';
			require_once EDD_FREE_DOWNLOADS_DIR . 'includes/template-actions.php';
			require_once EDD_FREE_DOWNLOADS_DIR . 'includes/shortcodes.php';
			require_once EDD_FREE_DOWNLOADS_DIR . 'includes/emails.php';

			if( ! class_exists( 'Mobile_Detect' ) ) {
				require_once EDD_FREE_DOWNLOADS_DIR . 'includes/libraries/mobile-detect/Mobile_Detect.php';
			}

			if ( is_admin() ) {
				require_once EDD_FREE_DOWNLOADS_DIR . 'includes/admin/class.admin-notices.php';
				require_once EDD_FREE_DOWNLOADS_DIR . 'includes/admin/settings/register.php';
				require_once EDD_FREE_DOWNLOADS_DIR . 'includes/admin/downloads/meta-boxes.php';
			}
		}


		/**
		 * Run action and filter hooks
		 *
		 * @access      private
		 * @since       1.0.0
		 * @return      void
		 */
		private function hooks() {
			// Handle licensing
			if ( class_exists( 'EDD_License' ) ) {
				$license = new EDD_License( __FILE__, 'Free Downloads', EDD_FREE_DOWNLOADS_VER, 'Easy Digital Downloads' );
			}
		}


		/**
		 * Internationalization
		 *
		 * @access      public
		 * @since       1.0.0
		 * @return      void
		 */
		public function load_textdomain() {
			// Set filter for language directory
			$lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
			$lang_dir = apply_filters( 'edd_free_downloads_language_directory', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale = apply_filters( 'plugin_locale', get_locale(), '' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'edd-free-downloads', $locale );

			// Setup paths to current locale file
			$mofile_local   = $lang_dir . $mofile;
			$mofile_global  = WP_LANG_DIR . '/edd-free-downloads/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/edd-free-downloads/ folder
				load_textdomain( 'edd-free-downloads', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/edd-free-downloads/ folder
				load_textdomain( 'edd-free-downloads', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'edd-free-downloads', false, $lang_dir );
			}
		}
	}
}


/**
 * The main function responsible for returning the one true EDD_Free_Downloads
 * instance to functions everywhere
 *
 * @since       1.0.0
 * @return      EDD_Free_Downloads The one true EDD_Free_Downloads
 */
function edd_free_downloads() {
	if ( ! class_exists( 'Easy_Digital_Downloads' ) ) {
		add_action( 'admin_notices', 'edd_free_downloads_edd_not_active' );
		return;
	} else {
		return EDD_Free_Downloads::instance();
	}
}
add_action( 'plugins_loaded', 'edd_free_downloads' );


/**
 * Display an error if EDD isn't active
 *
 * @since       2.1.8
 * @return      void
 */
function edd_free_downloads_edd_not_active() {
	echo '<div class="error"><p>' . __( 'Free Downloads requires Easy Digital Downloads! Please install or activate it to continue!', 'edd-free-downloads' ) . '</p></div>';
}


/**
 * Process upgrades
 *
 * @since       1.3.0
 * @global      array $edd_options The EDD options array
 * @return      void
 */
function edd_free_downloads_upgrade() {
	global $edd_options;

	if ( ! get_option( 'edd_free_downloads_upgrade_130' ) ) {
		// Upgrade notes field settings
		if ( isset( $edd_options['edd_free_downloads_notes'] ) && ( ! empty( $edd_options['edd_free_downloads_notes'] ) || $edd_options['edd_free_downloads_notes'] != '' ) ) {
			$edd_options['edd_free_downloads_show_notes'] = '1';
		}

		// Upgrade on-complete settings
		if ( ! isset( $edd_options['edd_free_downloads_auto_download'] ) && ! isset( $edd_options['edd_free_downloads_auto_download_redirect'] ) && ( ! isset( $edd_options['edd_free_downloads_redirect'] ) || $edd_options['edd_free_downloads_redirect'] == '' ) ) {
			$edd_options['edd_free_downloads_on_complete'] = 'default';
		} elseif ( isset( $edd_options['edd_free_downloads_auto_download'] ) && ! isset( $edd_options['edd_free_downloads_auto_download_redirect'] ) ) {
			$edd_options['edd_free_downloads_on_complete'] = 'auto-download';
		} elseif ( ! isset( $edd_options['edd_free_downloads_auto_download'] ) && ! isset( $edd_options['edd_free_downloads_auto_download_redirect'] ) && ( isset( $edd_options['edd_free_downloads_redirect'] ) && $edd_options['edd_free_downloads_redirect'] != '' ) ) {
			$edd_options['edd_free_downloads_on_complete'] = 'redirect';
		}
		unset( $edd_options['edd_free_downloads_auto_download'] );
		unset( $edd_options['edd_free_downloads_auto_download_redirect'] );

		update_option( 'edd_settings', $edd_options );
		update_option( 'edd_free_downloads_upgrade_130', '1' );
	}
}
register_activation_hook( __FILE__, 'edd_free_downloads_upgrade' );
