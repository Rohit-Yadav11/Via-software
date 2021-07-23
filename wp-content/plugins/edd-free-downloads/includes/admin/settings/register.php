<?php
/**
 * Register settings
 *
 * @package     EDD\FreeDownloads\Admin\Settings\Register
 * @since       1.1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Add settings section
 *
 * @since       1.1.2
 * @param       array $sections The existing extensions sections.
 * @return      array The modified extensions settings
 */
function edd_free_downloads_add_settings_section( $sections ) {
	$sections['free_downloads'] = __( 'Free Downloads', 'edd-free-downloads' );

	return $sections;
}
add_filter( 'edd_settings_sections_extensions', 'edd_free_downloads_add_settings_section' );


/**
 * Add settings
 *
 * @since       1.1.2
 * @param       array $settings The existing plugin settings.
 * @return      array The modified plugin settings
 */
function edd_free_downloads_add_settings( $settings ) {
	$display_settings = array(
		array(
			'id'            => 'edd_free_downloads_general_settings',
			'name'          => '<h3>' . __( 'General Settings', 'edd-free-downloads' ) . '</h3>',
			'desc'          => '',
			'type'          => 'header',
			'tooltip_title' => __( 'General Settings', 'edd-free-downloads' ),
			'tooltip_desc'  => __( 'The settings below determine how the free download process works and how the relevant components appear on your site.', 'edd-free-downloads' )
		),
		array(
			'id'   => 'edd_free_downloads_button_label',
			'name' => __( 'Button Label', 'edd-free-downloads' ),
			'desc' => __( 'Specify the text for the button shown in download lists and product pages.', 'edd-free-downloads' ),
			'type' => 'text',
			'std'  => __( 'Download Now', 'edd-free-downloads' )
		),
		array(
			'id'   => 'edd_free_downloads_modal_button_label',
			'name' => __( 'Modal Button Label', 'edd-free-downloads' ),
			'desc' => __( 'Specify the text for the button shown in the Free Downloads modal.', 'edd-free-downloads' ),
			'type' => 'text',
			'std'  => __( 'Download Now', 'edd-free-downloads' )
		),
		array(
			'id'            => 'edd_free_downloads_close_button',
			'name'          => __( 'Display Close Button', 'edd-free-downloads' ),
			'desc'          => __( 'Check to display a close button on the modal.', 'edd-free-downloads' ),
			'type'          => 'checkbox',
			'tooltip_title' => __( 'Displaying A Close Button', 'edd-free-downloads' ),
			'tooltip_desc'  => __( 'By default, Free Downloads does not display a close button on the modal. In this state, users must click on the background to close the modal.', 'edd-free-downloads' )
		),
		array(
			'id'   => 'edd_free_downloads_bypass_logged_in',
			'name' => __( 'Bypass If Logged In', 'edd-free-downloads' ),
			'desc' => __( 'Check to bypass the modal if a user is logged in.', 'edd-free-downloads' ),
			'type' => 'checkbox'
		)
	);

	// Conditionally add the privacy policy option, since it is only supported by EDD 2.9.1
	if ( version_compare( EDD_VERSION, '2.9.1', '>=' ) ) {
		$privacy_policy_page = get_option( 'wp_page_for_privacy_policy', 0 );

		$privacy_policy_page_settings = admin_url( 'edit.php?post_type=download&page=edd-settings&tab=privacy' );
		$disabled = empty( $privacy_policy_page );
		$description = $disabled ?
			sprintf( __( 'To enable this option, you need to <a href="%s">select a Privacy Policy page</a>.', 'edd-free-downloads' ), $privacy_policy_page_settings ) :
			sprintf( __( 'When checked, the user will be provided with a checkbox to agree to the privacy policy and show a link to the privacy policy page <a href="%s">defined in the Easy Digital Downloads settings</a>.', 'edd-free-downloads' ), $privacy_policy_page_settings );

		$display_settings[] = array(
			'id'   => 'edd_free_downloads_display_privacy_policy_agreement',
			'name' => __( 'Agree to Privacy Policy', 'edd-free-downloads' ),
			'desc' => $description,
			'type' => 'checkbox',
			'options' => array(
				'disabled' => $disabled
			)
		);
	}

	$display_settings = apply_filters( 'edd_free_downloads_general_settings', $display_settings );

	$fields_settings = apply_filters( 'edd_free_downloads_fields_settings', array(
		array(
			'id'            => 'edd_free_downloads_fields_settings',
			'name'          => '<h3>' . __( 'Fields Settings', 'edd-free-downloads' ) . '</h3>',
			'desc'          => '',
			'type'          => 'header',
			'tooltip_title' => __( 'Fields Settings', 'edd-free-downloads' ),
			'tooltip_desc'  => __( 'The fields below define what fields are shown in the download modal and how the system interacts with them.', 'edd-free-downloads' )
		),
		array(
			'id'   => 'edd_free_downloads_get_name',
			'name' => __( 'Collect Name', 'edd-free-downloads' ),
			'desc' => __( 'Check to enable collection of the first and last name in the download modal.', 'edd-free-downloads' ),
			'type' => 'checkbox'
		),
		array(
			'id'   => 'edd_free_downloads_require_name',
			'name' => __( 'Require Name', 'edd-free-downloads' ),
			'desc' => __( 'Check to make the first and last name fields required.', 'edd-free-downloads' ),
			'type' => 'checkbox'
		),
		array(
			'id'            => 'edd_free_downloads_show_notes',
			'name'          => __( 'Show Notes Field', 'edd-free-downloads' ),
			'desc'          => __( 'Check to enable the notes field in the download modal.', 'edd-free-downloads' ),
			'type'          => 'checkbox',
			'tooltip_title' => __( 'The Notes Field', 'edd-free-downloads' ),
			'tooltip_desc'  => __( 'Enabling this option allows you to display notes in the modal.', 'edd-free-downloads' )
		),
		array(
			'id'   => 'edd_free_downloads_disable_global_notes',
			'name' => __( 'Disable Global Notes', 'edd-free-downloads' ),
			'desc' => __( 'Check to disable global notes and only use notes defined per-product.', 'edd-free-downloads' ),
			'type' => 'checkbox'
		),
		array(
			'id'   => 'edd_free_downloads_notes_title',
			'name' => __( 'Notes Field Title', 'edd-free-downloads' ),
			'desc' => __( 'Enter the title to display for the notes field, or leave blank for none.', 'edd-free-downloads' ),
			'type' => 'text',
			'std'  => __( 'Notes', 'edd-free-downloads' ),
		),
		array(
			'id'   => 'edd_free_downloads_notes',
			'name' => __( 'Notes', 'edd-free-downloads' ),
			'desc' => __( 'Enter any notes to display in the Free Downloads modal.', 'edd-free-downloads' ),
			'type' => 'rich_editor',
		),
	) );

	$processing_settings = apply_filters( 'edd_free_downloads_processing_settings', array(
		array(
			'id'            => 'edd_free_downloads_processing_settings',
			'name'          => '<h3>' . __( 'Processing Settings', 'edd-free-downloads' ) . '</h3>',
			'desc'          => '',
			'type'          => 'header',
			'tooltip_title' => __( 'Processing Settings', 'edd-free-downloads' ),
			'tooltip_desc'  => __( 'The fields below define how Free Downloads handles downloads after a user has filled out the modal fields.', 'edd-free-downloads' )
		),
		array(
			'id'   => 'edd_free_downloads_require_verification',
			'name' => __( 'Require Email Verification', 'edd-free-downloads' ),
			'desc' => __( 'Requires users to receive an email and follow a link in order to complete their download.', 'edd-free-downloads' ),
			'type' => 'checkbox',
		),
		array(
			'id'          => 'edd_free_downloads_require_verification_message',
			'name'        => __( 'Email Verification Message', 'edd-free-downloads' ),
			'desc'        => __( 'Define the message to show to visitors letting them know they will receive an email to verify their download.', 'edd-free-downloads' ),
			'type'        => 'text',
			'std'         => __( 'An email will be sent to the provided address to complete your download.', 'edd-free-downloads' ),
			'allow_blank' => false,
		),
		array(
			'id'          => 'edd_free_downloads_verification_email_subject',
			'name'        => __( 'Verification Email Subject', 'edd-free-downloads' ),
			'desc'        => __( 'The subject line for the email sent to the user.', 'edd-free-downloads' ),
			'type'        => 'text',
			'std'         => __( 'Confirm your free download.', 'edd-free-downloads' ),
			'allow_blank' => false,
		),
		array(
			'id'          => 'edd_free_downloads_verification_email',
			'name'        => __( 'Verification Email', 'edd-free-downloads' ),
			'desc'        => __( 'Enter in the content of the email you want to send to verify email addresses.', 'edd-free-downloads' ) . '<br/>' . edd_get_emails_tags_list(),
			'type'        => 'rich_editor',
			'std'         => __( "Please click the following link to complete your download.\n\n" . "{free_downloads_verification_link}", 'edd-free-downloads' ),
			'allow_blank' => false,
		),
		array(
			'id'            => 'edd_free_downloads_on_complete',
			'name'          => __( 'On-Complete Handler', 'edd-free-downloads' ),
			'desc'          => __( 'Specify what to do once the user has filled out the download form.', 'edd-free-downloads' ),
			'type'          => 'select',
			'tooltip_title' => __( 'On-Complete Handler', 'edd-free-downloads' ),
			'tooltip_desc'  => __( 'Free Downloads can do a number of things when a download is processed:', 'edd-free-downloads' ) .
								'<p><strong>' . __( 'Display Purchase Confirmation:', 'edd-free-downloads' ) . '</strong> ' . __( 'This is the default behavior of EDD. Users will be redirected to the same Purchase Confirmation page they would see when completing a purchase.', 'edd-free-downloads' ) . '</p>' .
								'<p><strong>' . __( 'Auto Download:', 'edd-free-downloads' ) . '</strong> ' . __( 'The system will close the download modal, and automatically download the relevant file(s).', 'edd-free-downloads' ) . '</p>' .
								'<p><strong>' . __( 'Custom Redirect:', 'edd-free-downloads' ) . '</strong> ' . __( 'Rather than redirecting to the Purchase Confirmation page, this allows you to define a custom redirection URL.', 'edd-free-downloads' ) . '</p>',
			'std'     => 'default',
			'options' => array(
				'default'       => __( 'Display Purchase Confirmation', 'edd-free-downloads' ),
				'auto-download' => __( 'Auto Download', 'edd-free-downloads' ),
				'redirect'      => __( 'Custom Redirect', 'edd-free-downloads' )
			)
		),
		array(
			'id'            => 'edd_free_downloads_redirect',
			'name'          => __( 'Redirect URL', 'edd-free-downloads' ),
			'desc'          => __( 'Enter a URL to redirect to on completion.', 'edd-free-downloads' ),
			'type'          => 'text',
			'tooltip_title' => __( 'Redirect URL', 'edd-free-downloads' ),
			'tooltip_desc'  => __( 'If no URL is set, users will be automatically redirected to the Purchase Confirmation page.', 'edd-free-downloads' )
		),
		array(
			'id'            => 'edd_free_downloads_mobile_on_complete',
			'name'          => __( 'Mobile On-Complete Handler', 'edd-free-downloads' ),
			'desc'          => __( 'Specify what to do once the user has filled out the download form.', 'edd-free-downloads' ),
			'type'          => 'select',
			'tooltip_title' => __( 'Mobile On-Complete Handler', 'edd-free-downloads' ),
			'tooltip_desc'  => __( 'Specify an alternate method of handling completion on mobile devices.', 'edd-free-downloads' ),
			'std'     => 'default',
			'options' => array(
				'default'       => __( 'Use Main On-Complete Handler', 'edd-free-downloads' ),
				'confirmation'  => __( 'Display Purchase Confirmation', 'edd-free-downloads' ),
				'auto-download' => __( 'Auto Download', 'edd-free-downloads' ),
				'redirect'      => __( 'Custom Redirect', 'edd-free-downloads' )
			)
		),
		array(
			'id'            => 'edd_free_downloads_mobile_redirect',
			'name'          => __( 'Mobile Redirect URL', 'edd-free-downloads' ),
			'desc'          => __( 'Enter a URL to redirect to on completion for mobile devices.', 'edd-free-downloads' ),
			'type'          => 'text',
			'tooltip_title' => __( 'Mobile Redirect URL', 'edd-free-downloads' ),
			'tooltip_desc'  => __( 'If no URL is set, users will be automatically redirected to the Purchase Confirmation page.', 'edd-free-downloads' )
		),
		array(
			'id'            => 'edd_free_downloads_apple_on_complete',
			'name'          => __( 'Apple On-Complete Handler', 'edd-free-downloads' ),
			'desc'          => __( 'Apple does not allow automatic downloads. Specify an alternative for Apple devices.', 'edd-free-downloads' ),
			'type'          => 'select',
			'tooltip_title' => __( 'Apple On-Complete Handler', 'edd-free-downloads' ),
			'tooltip_desc'  => __( 'Specify an alternate method of handling completion on Apple mobile devices.', 'edd-free-downloads' ),
			'std'     => 'default',
			'options' => array(
				'default'       => __( 'Use Main On-Complete Handler', 'edd-free-downloads' ),
				'confirmation'  => __( 'Display Purchase Confirmation', 'edd-free-downloads' ),
				'redirect'      => __( 'Custom Redirect', 'edd-free-downloads' )
			)
		),
		array(
			'id'            => 'edd_free_downloads_apple_redirect',
			'name'          => __( 'Apple Redirect URL', 'edd-free-downloads' ),
			'desc'          => __( 'Enter a URL to redirect to on completion for Apple devices.', 'edd-free-downloads' ),
			'type'          => 'text',
			'tooltip_title' => __( 'Apple Redirect URL', 'edd-free-downloads' ),
			'tooltip_desc'  => __( 'If no URL is set, users will be automatically redirected to the Purchase Confirmation page.', 'edd-free-downloads' )
		),
		array(
			'id'   => 'edd_free_downloads_disable_emails',
			'name' => __( 'Disable purchase confirmation emails', 'edd-free-downloads' ),
			'desc' => __( 'Checking this will disable the purchase confirmation emails which are sent to customers after they purchase a free product.', 'edd-free-downloads' ),
			'type' => 'checkbox',
		),
		array(
			'id'   => 'edd_free_downloads_disable_admin_emails',
			'name' => __( 'Disable admin sales notifications', 'edd-free-downloads' ),
			'desc' => sprintf( __( 'Checking this will disable the default sales notifications which get sent to the email addresses in the <a href="%s">Sale Notification Emails</a> setting.', 'edd-free-downloads' ), admin_url( 'edit.php?post_type=download&page=edd-settings&tab=emails&section=sale_notifications' ) ),
			'type' => 'checkbox',
		),
		array(
			'id'   => 'edd_free_downloads_direct_download',
			'name' => __( 'Direct Download', 'edd-free-downloads' ),
			'desc' => __( 'Check to allow users to download files without entering their info.', 'edd-free-downloads' ),
			'type' => 'checkbox'
		),
		array(
			'id'   => 'edd_free_downloads_direct_download_label',
			'name' => __( 'Direct Download Label', 'edd-free-downloads' ),
			'desc' => __( 'Enter the text to display for the direct download link', 'edd-free-downloads' ),
			'type' => 'text',
			'std'  => __( 'No thanks, proceed to download', 'edd-free-downloads' )
		),
		array(
			'id'   => 'free_downloads_zip_status',
			'name' => __( 'Compression Status', 'edd-free-downloads' ),
			'desc' => '',
			'type' => 'hook'
		)
	) );

	$cache_settings = apply_filters( 'edd_free_downloads_cache_settings', array(
		array(
			'id'            => 'edd_free_downloads_cache_settings',
			'name'          => '<h3>' . __( 'Cache Settings', 'edd-free-downloads' ) . '</h3>',
			'desc'          => '',
			'type'          => 'header',
			'tooltip_title' => __( 'Cache Settings', 'edd-free-downloads' ),
			'tooltip_desc'  => __( 'The fields below define how Free Downloads handles file caching. Free Downloads caches files which are stored remotely while compressing them if direct download or auto download are enabled.', 'edd-free-downloads' )
		),
		array(
			'id'            => 'edd_free_downloads_disable_cache',
			'name'          => __( 'Disable Cache', 'edd-free-downloads' ),
			'desc'          => __( 'Check to disable caching remote files entirely.', 'edd-free-downloads'),
			'type'          => 'checkbox',
			'tooltip_title' => __( 'Disable Cache', 'edd-free-downloads' ),
			'tooltip_desc'  => __( 'Free Downloads caches remote files to prevent long download times while compressing multi-file downloads. If you prefer not to use this feature, check this option.', 'edd-free-downloads' )
		),
		array(
			'id'            => 'edd_free_downloads_purge_cache_timeout',
			'name'          => __( 'Cache Timeout', 'edd-free-downloads' ),
			'desc'          => __( 'Enter the length of the cache timeout, in hours.', 'edd-free-downloads'),
			'type'          => 'text',
			'size'          => 'small',
			'std'           => '1',
			'allow_blank'   => false,
			'tooltip_title' => __( 'Cache Timeout', 'edd-free-downloads' ),
			'tooltip_desc'  => __( 'If a cached file is requested, Free Downloads will see when it was created. If that time is greater than this cache timeout, the file will be recreated.', 'edd-free-downloads' )
		),
		array(
			'id'   => 'free_downloads_display_purge_cache',
			'name' => __( 'Purge Cache', 'edd-free-downloads' ),
			'desc' => '',
			'type' => 'hook'
		)
	) );

	// Allow extension of the settings.
	$integration_settings = apply_filters( 'edd_free_downloads_integration_settings', array() );

	if ( count( $integration_settings ) > 0 ) {
		$integration_header = array(
			array(
				'id'            => 'edd_free_downloads_integrations_settings',
				'name'          => '<h3>' . __( 'Integration Settings', 'edd-free-downloads' ) . '</h3>',
				'desc'          => '',
				'type'          => 'header',
				'tooltip_title' => __( 'Integration Settings', 'edd-free-downloads' ),
				'tooltip_desc'  => __( 'The fields below define how Free Downloads integrates with other plugins you have installed.', 'edd-free-downloads' )
			)
		);

		$integration_settings = array_merge( $integration_header, $integration_settings );
	}

	$plugin_settings = array_merge( $display_settings, $fields_settings, $processing_settings, $cache_settings, $integration_settings );
	$plugin_settings = array( 'free_downloads' => $plugin_settings );

	return array_merge( $settings, $plugin_settings );
}
add_filter( 'edd_settings_extensions', 'edd_free_downloads_add_settings' );


/**
 * Maybe add Auto Register settings
 *
 * If EDD Auto Register is installed, it conflicts with
 * our auto registration functions. Thus, only allow our
 * auto registration option if it isn't installed.
 *
 * @since       1.1.0
 * @param       array $settings The existing settings.
 * @return      array $settings The updated settings
 */
function edd_free_downloads_auto_register_settings( $settings ) {
	if ( ! class_exists( 'EDD_Auto_Register' ) ) {
		$auto_register_settings = array(
			array(
				'id'   => 'edd_free_downloads_user_registration',
				'name' => __( 'User Registration', 'edd-free-downloads' ),
				'desc' => __( 'Check to display a registration form in the download modal for logged-out users.', 'edd-free-downloads' ),
				'type' => 'checkbox',
			)
		);

		$settings = array_merge( $settings, $auto_register_settings );
	} else {
		$auto_register_settings = array(
			array(
				'id'            => 'edd_free_downloads_bypass_auto_register',
				'name'          => __( 'Bypass Auto Registration', 'edd-free-downloads' ),
				'desc'          => __( 'Check to bypass auto-registration for free downloads.', 'edd-free-downloads' ),
				'type'          => 'checkbox',
				'tooltip_title' => __( 'Bypass Auto Registration', 'edd-free-downloads' ),
				'tooltip_desc'  => __( 'By default, the Auto Registration plugin registers a new user account when a free download is processed. Check this to prevent user accounts from being automatically created.', 'edd-free-downloads' )
			)
		);

		$settings = array_merge( $settings, $auto_register_settings );
	}

	return $settings;
}
add_filter( 'edd_free_downloads_fields_settings', 'edd_free_downloads_auto_register_settings' );


/**
 * Add newsletter opt-out checkbox if relevant
 *
 * @since       1.1.0
 * @param       array $settings The existing settings.
 * @return      array $settings The updated settings
 */
function edd_free_downloads_newsletter_settings( $settings ) {
	if ( edd_free_downloads_has_newsletter_plugin() ) {
		$newsletter_settings = array(
			array(
				'id'   => 'edd_free_downloads_newsletter_optin',
				'name' => __( 'Display Opt-In', 'edd-free-downloads' ),
				'desc' => __( 'Check to display a newsletter opt-in checkbox in the download modal.', 'edd-free-downloads' ),
				'type' => 'checkbox',
			),
			array(
				'id'   => 'edd_free_downloads_newsletter_auto_checked',
				'name' => __( 'Signup Checked by Default', 'edd-free-downloads' ),
				'desc' => __( 'Should the newsletter signup checkbox be checked by default for Free Downloads?', 'edd-free-downloads' ),
				'type' => 'checkbox',
			),
			array(
				'id'   => 'edd_free_downloads_newsletter_optin_label',
				'name' => __( 'Opt-In Field Label', 'edd-free-downloads' ),
				'desc' => __( 'Specify the text to display for the opt-in field label.', 'edd-free-downloads' ),
				'type' => 'text',
				'std'  => __( 'Subscribe to our newsletter', 'edd-free-downloads' ),
			),
		);

		$settings = array_merge( $settings, $newsletter_settings );
	}

	return $settings;
}
add_filter( 'edd_free_downloads_integration_settings', 'edd_free_downloads_newsletter_settings' );
