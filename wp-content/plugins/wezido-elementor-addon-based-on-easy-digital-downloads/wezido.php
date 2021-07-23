<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           wezido
 *
 * @wordpress-plugin
 * Plugin Name:       Wezido - Elementor Addon Based on Easy Digital Downloads
 * Plugin URI:        https://teconce.com
 * Description:       An Elementor Extension Works with the Easy Digital Download Products
 * Version:           1.0.0
 * Author:            Teconce
 * Author URI:        http://teconce.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wezido
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WEZIDO_VERSION', '1.0.0' );

/**
 * Missing Elementor notice
 */
function wezido_admin_notice_missing_main_plugin()	{
	$message = sprintf(
		esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'wezido' ),
		'<strong>' . esc_html__( 'Wezido - Elementor Addon', 'wezido' ) . '</strong>',
		'<strong>' . esc_html__( 'Elementor', 'wezido' ) . '</strong>'
	);

	printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wezido-activator.php
 */
function activate_wezido() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wezido-activator.php';
	wezido_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wezido-deactivator.php
 */
function deactivate_wezido() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wezido-deactivator.php';
	wezido_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wezido' );
register_deactivation_hook( __FILE__, 'deactivate_wezido' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wezido.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */

$plugin = new wezido();
$plugin->run();

/**
 * Get the list of active plugin...
 * ...then check if Elementor is active, and register my widgets
 */
$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins'));

if (in_array( 'elementor/elementor.php', $active_plugins ) ) {
	
	add_action( 'init', function() use ($plugin) {
			$plugin->wezido_register_elementor_widgets();
	});

} else {
	add_action( 'admin_notices', 'wezido_admin_notice_missing_main_plugin');								
}