<?php
/**
 * This file represents an example of the code that themes would use to register
 * the required plugins.
 *
 * It is expected that theme authors would copy and paste this code into their
 * functions.php file, and amend to suit.
 *
 * @see http://tgmpluginactivation.com/configuration/ for detailed documentation.
 *
 * @package    TGM-Plugin-Activation
 * @subpackage Example
 * @version    2.6.1 for parent theme Adri Ajdethemes for publication on ThemeForest
 * @author     Thomas Griffin, Gary Jones, Juliette Reinders Folmer
 * @copyright  Copyright (c) 2011, Thomas Griffin
 * @license    http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link       https://github.com/TGMPA/TGM-Plugin-Activation
 */

/**
 * The TGM_Plugin_Activation class.
 *
 */
require_once get_template_directory() . '/includes/plugins/tgm/class-tgm-plugin-activation.php';

add_action( 'tgmpa_register', 'adri_ajdethemes_register_required_plugins' );

/**
 * Register the required plugins for this theme.
 *
 * This function is hooked into `tgmpa_register`, which is fired on the WP `init` action on priority 10.
 * 
 */
function adri_ajdethemes_register_required_plugins() {

	$plugins = array(

		array(
			'name'      => 'WP-SCSS',
			'slug'      => 'wp-scss',
			'required'  => true,
			'source'    => ADRI_AJDETHEMES_INC_DIR . '/plugins/wp-scss.zip',
		),

		array(
			'name'      => 'Adri Ajdethemes Elements',
			'slug'      => 'adri-ajdethemes-elements',
			'required'  => true,
			'source'    => ADRI_AJDETHEMES_INC_DIR . '/plugins/adri-ajdethemes-elements.zip',
			'version'   => '1.0.3',
		),
		
		array(
			'name'      => 'Elementor Page Builder',
			'slug'      => 'elementor',
			'required'  => true,
		),

		array(
			'name'      => 'Slider Revolution',
			'slug'      => 'revslider',
			'required'  => true,
			'source'    => ADRI_AJDETHEMES_INC_DIR . '/plugins/revslider.zip',
			'version'   => '6.3.5',
		),
		
		array(
			'name'      => 'Contact Form 7',
			'slug'      => 'contact-form-7',
			'required'  => false,
		),
		
		array(
			'name'      => 'WooCommerce',
			'slug'      => 'woocommerce',
			'required'  => false,
		),

		array(
			'name'      => 'Max Mega Menu',
			'slug'      => 'megamenu',
			'required'  => false,
		),
		
		array(
			'name'      => 'Custom Twitter Feeds',
			'slug'      => 'custom-twitter-feeds',
			'required'  => false,
		),
		
		array(
			'name'      => 'One Click Demo Import',
			'slug'      => 'one-click-demo-import',
			'required'  => false,
		),
		
		array(
			'name'      => 'Envato Market',
			'slug'      => 'envato-market',
			'required'  => false,
			'source'    => ADRI_AJDETHEMES_INC_DIR . '/plugins/envato-market.zip',
		)

	);

	/*
	 * Array of configuration settings. Amend each line as needed.
	 *
	 * TGMPA will start providing localized text strings soon. If you already have translations of our standard
	 * strings available, please help us make TGMPA even better by giving us access to these translations or by
	 * sending in a pull-request with .po file(s) with the translations.
	 *
	 * Only uncomment the strings in the config array if you want to customize the strings.
	 */
	$config = array(
		'id'           => 'adri-ajdethemes',                 // Unique ID for hashing notices for multiple instances of TGMPA.
		'default_path' => '',                      // Default absolute path to bundled plugins.
		'menu'         => 'tgmpa-install-plugins', // Menu slug.
		'has_notices'  => true,                    // Show admin notices or not.
		'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
		'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => false,                   // Automatically activate plugins after installation or not.
		'message'      => '',                      // Message to output right before the plugins table.

		/*
		'strings'      => array(
			'page_title'                      => __( 'Install Required Plugins', 'adri-ajdethemes' ),
			'menu_title'                      => __( 'Install Plugins', 'adri-ajdethemes' ),
			/* translators: %s: plugin name. * /
			'installing'                      => __( 'Installing Plugin: %s', 'adri-ajdethemes' ),
			/* translators: %s: plugin name. * /
			'updating'                        => __( 'Updating Plugin: %s', 'adri-ajdethemes' ),
			'oops'                            => __( 'Something went wrong with the plugin API.', 'adri-ajdethemes' ),
			'notice_can_install_required'     => _n_noop(
				/* translators: 1: plugin name(s). * /
				'This theme requires the following plugin: %1$s.',
				'This theme requires the following plugins: %1$s.',
				'adri-ajdethemes'
			),
			'notice_can_install_recommended'  => _n_noop(
				/* translators: 1: plugin name(s). * /
				'This theme recommends the following plugin: %1$s.',
				'This theme recommends the following plugins: %1$s.',
				'adri-ajdethemes'
			),
			'notice_ask_to_update'            => _n_noop(
				/* translators: 1: plugin name(s). * /
				'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.',
				'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.',
				'adri-ajdethemes'
			),
			'notice_ask_to_update_maybe'      => _n_noop(
				/* translators: 1: plugin name(s). * /
				'There is an update available for: %1$s.',
				'There are updates available for the following plugins: %1$s.',
				'adri-ajdethemes'
			),
			'notice_can_activate_required'    => _n_noop(
				/* translators: 1: plugin name(s). * /
				'The following required plugin is currently inactive: %1$s.',
				'The following required plugins are currently inactive: %1$s.',
				'adri-ajdethemes'
			),
			'notice_can_activate_recommended' => _n_noop(
				/* translators: 1: plugin name(s). * /
				'The following recommended plugin is currently inactive: %1$s.',
				'The following recommended plugins are currently inactive: %1$s.',
				'adri-ajdethemes'
			),
			'install_link'                    => _n_noop(
				'Begin installing plugin',
				'Begin installing plugins',
				'adri-ajdethemes'
			),
			'update_link' 					  => _n_noop(
				'Begin updating plugin',
				'Begin updating plugins',
				'adri-ajdethemes'
			),
			'activate_link'                   => _n_noop(
				'Begin activating plugin',
				'Begin activating plugins',
				'adri-ajdethemes'
			),
			'return'                          => __( 'Return to Required Plugins Installer', 'adri-ajdethemes' ),
			'plugin_activated'                => __( 'Plugin activated successfully.', 'adri-ajdethemes' ),
			'activated_successfully'          => __( 'The following plugin was activated successfully:', 'adri-ajdethemes' ),
			/* translators: 1: plugin name. * /
			'plugin_already_active'           => __( 'No action taken. Plugin %1$s was already active.', 'adri-ajdethemes' ),
			/* translators: 1: plugin name. * /
			'plugin_needs_higher_version'     => __( 'Plugin not activated. A higher version of %s is needed for this theme. Please update the plugin.', 'adri-ajdethemes' ),
			/* translators: 1: dashboard link. * /
			'complete'                        => __( 'All plugins installed and activated successfully. %1$s', 'adri-ajdethemes' ),
			'dismiss'                         => __( 'Dismiss this notice', 'adri-ajdethemes' ),
			'notice_cannot_install_activate'  => __( 'There are one or more required or recommended plugins to install, update or activate.', 'adri-ajdethemes' ),
			'contact_admin'                   => __( 'Please contact the administrator of this site for help.', 'adri-ajdethemes' ),

			'nag_type'                        => '', // Determines admin notice type - can only be one of the typical WP notice classes, such as 'updated', 'update-nag', 'notice-warning', 'notice-info' or 'error'. Some of which may not work as expected in older WP versions.
		),
		*/
	);

	tgmpa( $plugins, $config );
}
