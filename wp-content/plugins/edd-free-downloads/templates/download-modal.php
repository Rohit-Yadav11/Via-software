<?php

// Pull user data if available
if ( is_user_logged_in() ) {
	$user = new WP_User( get_current_user_id() );
}

$desktop_redirect     = edd_get_option( 'edd_free_downloads_redirect' );
$mobile_redirect      = edd_get_option( 'edd_free_downloads_mobile_redirect' );
$is_mobile            = $_GET['edd_is_mobile'] ? true : false;
$require_verification = edd_free_downloads_verify_email();
$require_login        = edd_no_guest_checkout();

$email = isset( $user ) ? $user->user_email : '';
$fname = isset( $user ) ? $user->user_firstname : '';
$lname = isset( $user ) ? $user->user_lastname : '';

$rname = edd_get_option( 'edd_free_downloads_require_name', false ) ? ' <span class="edd-free-downloads-required">*</span>' : '';

// Get EDD vars
$color = edd_get_option( 'checkout_color', 'blue' );
$color = ( $color == 'inherit' ) ? '' : $color;
$label = edd_get_option( 'edd_free_downloads_modal_button_label', __( 'Download Now', 'edd-free-downloads' ) );

/**
 * Getting our download_id from our ajax call
 */
if ( isset( $_GET['download_id'] ) && ! empty( $_GET['download_id'] ) ) {
	$post = get_post( $_GET['download_id'] );
} else {
	global $post; // Leaving here for backwards compat.
}

$download_id = $post->ID;
$price_ids   = isset( $_GET['price_ids'] ) ? array_map( 'absint', $_GET['price_ids'] ) : array();
?>
<?php echo do_shortcode('[edd_login]'); ?>
<p class="redlogin">Create a new account<a href="/register/">Register</a></p>
<!-- <form id="edd_free_download_form" method="post">
	<?php do_action( 'edd_free_downloads_before_modal_form', $post ); ?>
	<p class="redlogin">Already have an account? <a href="#">Sign in</a></p>
	<p>
		<label for="edd_free_download_email" class="edd-free-downloads-label"><?php esc_html_e( 'Email Address', 'edd-free-downloads' ); ?> <span class="edd-free-downloads-required">*</span></label>
		<input type="text" name="edd_free_download_email" id="edd_free_download_email" class="edd-free-download-field" placeholder="<?php _e( 'Email Address', 'edd-free-downloads' ); ?>" value="<?php echo $email; ?>" tabindex="-1" />
	</p>

	<?php if ( edd_get_option( 'edd_free_downloads_get_name', false ) ) : ?>
		<p>
			<label for="edd_free_download_fname" class="edd-free-downloads-label"><?php esc_html_e( 'First Name', 'edd-free-downloads' ) . $rname; ?>
				<?php if ( isset( $_GET['require_name'] ) && 'true' === $_GET['require_name'] ) : ?>
					<span class="edd-free-downloads-required">*</span>
				<?php endif ?>
			</label>
			<input type="text" name="edd_free_download_fname" id="edd_free_download_fname" class="edd-free-download-field" placeholder="<?php _e( 'First Name', 'edd-free-downloads' ); ?>" value="<?php echo $fname; ?>" tabindex="0" />
			<?php do_action( 'edd_free_downloads_after_modal_fname', $post ); ?>
		</p>

		<p>
			<label for="edd_free_download_lname" class="edd-free-downloads-label"><?php esc_html_e( 'Last Name', 'edd-free-downloads' ) . $rname; ?>
				<?php if ( isset( $_GET['require_name'] ) && 'true' === $_GET['require_name'] ) : ?>
					<span class="edd-free-downloads-required">*</span>
				<?php endif; ?>
			</label>
			<input type="text" name="edd_free_download_lname" id="edd_free_download_lname" class="edd-free-download-field" placeholder="<?php _e( 'Last Name', 'edd-free-downloads' ); ?>" value="<?php echo $lname; ?>" tabindex="0" />
			<?php do_action( 'edd_free_downloads_after_modal_lname', $post ); ?>
		</p>
	<?php endif; ?>

	<?php if ( edd_get_option( 'edd_free_downloads_user_registration', false ) && ! is_user_logged_in() && ! class_exists( 'EDD_Auto_Register' ) ) : ?>

		<hr />

		<?php do_action( 'edd_free_downloads_before_modal_form_registration', $post ); ?>

		<p>
			<label for="edd_free_download_username" class="edd-free-downloads-label"><?php esc_html_e( 'Username', 'edd-free-downloads' ); ?>
				<?php if ( $require_login ) : ?>
					<span class="edd-free-downloads-required">*</span>
				<?php endif; ?>
			</label>
			<input type="text" name="edd_free_download_username" id="edd_free_download_username" class="edd-free-download-field" placeholder="<?php _e( 'Username', 'edd-free-downloads' ); ?>" value="" tabindex="0" />
			<?php do_action( 'edd_free_downloads_after_modal_username', $post ); ?>
		</p>

		<p>
			<label for="edd_free_download_pass" class="edd-free-downloads-label"><?php esc_html_e( 'Password', 'edd-free-downloads' ); ?>
				<?php if ( $require_login ) : ?>
					<span class="edd-free-downloads-required">*</span>
				<?php endif; ?>
			</label>
			<input type="password" name="edd_free_download_pass" id="edd_free_download_pass" class="edd-free-download-field" tabindex="0" />
			<?php do_action( 'edd_free_downloads_after_modal_pass', $post ); ?>
		</p>

		<p>
			<label for="edd_free_download_pass2" class="edd-free-downloads-label"><?php esc_html_e( 'Confirm Password', 'edd-free-downloads' ); ?>
				<?php if ( $require_login ) : ?>
					<span class="edd-free-downloads-required">*</span>
				<?php endif; ?>
			</label>
			<input type="password" name="edd_free_download_pass2" id="edd_free_download_pass2" class="edd-free-download-field" tabindex="0" />
			<?php do_action( 'edd_free_downloads_after_modal_pass_2', $post ); ?>
		</p>

		<?php do_action( 'edd_free_downloads_after_modal_form_registration', $post ); ?>

	<?php endif; ?>

	<?php if ( edd_get_option( 'edd_free_downloads_newsletter_optin', false ) && edd_free_downloads_has_newsletter_plugin() ) : ?>
		<?php $check_by_default = edd_get_option( 'edd_free_downloads_newsletter_auto_checked', false ); ?>
		<p>
			<input type="checkbox" name="edd_free_download_optin" id="edd_free_download_optin" <?php checked( true, $check_by_default, true ); ?> />
			<label for="edd_free_download_optin" class="edd-free-downloads-checkbox-label"><?php echo edd_get_option( 'edd_free_downloads_newsletter_optin_label', __( 'Subscribe to our newsletter', 'edd-free-downloads' ) ); ?></label>
			<?php do_action( 'edd_free_downloads_after_modal_optin', $post ); ?>
		</p>
	<?php endif; ?>

	<?php $show_privacy_policy = edd_get_option( 'edd_free_downloads_display_privacy_policy_agreement', false ); ?>
	<?php $privacy_policy_page = get_option( 'wp_page_for_privacy_policy' ); ?>
	<?php if ( ! empty( $show_privacy_policy ) && ! empty( $privacy_policy_page ) ) : ?>
		<?php $privacy_policy_permalink = get_permalink( get_option( 'wp_page_for_privacy_policy' ) ); ?>
		<?php if ( ! empty( $privacy_policy_permalink ) ) : ?>
		<p>
			<input type="checkbox" name="edd_free_download_privacy_agreement" id="edd-free-download-privacy-agreement" value="1" />
			<label for="edd-free-download-privacy-agreement" class="edd-free-downloads-checkbox-label">
				<?php printf( __( 'Agree to the <a href="%s" target="_blank" rel="_noopener">Privacy Policy</a>', 'edd-free-downloads' ), $privacy_policy_permalink ); ?>
			</label>
		</p>
		<?php endif; ?>
	<?php endif; ?>

	<?php if ( edd_get_option( 'edd_free_downloads_show_notes' ) ) : ?>
		<?php
			$title = $content = '';

			if ( ! edd_get_option( 'edd_free_downloads_disable_global_notes', false ) ) {
				$title   = edd_get_option( 'edd_free_downloads_notes_title', '' );
				$content = edd_get_option( 'edd_free_downloads_notes', '' );
			}

			if ( $download_title = get_post_meta( $post->ID, '_edd_free_downloads_notes_title', true ) ) {
				$title = $download_title;
			}

			if ( $download_note = get_post_meta( $post->ID, '_edd_free_downloads_notes', true ) ) {
				$content = $download_note;
			}
		?>
		<div class="edd-free-downloads-note-wrapper">
			<?php do_action( 'edd_free_downloads_modal_before_notes', $post ); ?>
			<div class="edd-free-downloads-note-title"><strong><?php echo esc_html( $title ); ?></strong></div>
			<p class="edd-free-downloads-note-content"><?php echo wpautop( stripslashes( $content ) ); ?></p>
			<?php do_action( 'edd_free_downloads_modal_after_notes', $post ); ?>
		</div>
	<?php endif; ?>

	<?php do_action( 'edd_free_downloads_after_modal_form', $post ); ?>

	<input type="hidden" name="edd_free_download_check" value="" />

	<?php echo wp_nonce_field( 'edd_free_download_nonce', 'edd_free_download_nonce', true, false ); ?>

	<div class="edd-free-download-errors">
		<?php
		foreach ( edd_free_downloads_form_errors() as $error => $message ) {
			echo '<p id="edd-free-download-error-' . $error . '">';
			echo '<strong>' . __( 'Error:', 'edd-free-downloads' ) . '</strong> ' . $message;
			echo '</p>';
		}
		?>
	</div>

	<input type="hidden" name="edd_action" value="free_download_process" />
	<input type="hidden" name="edd_free_download_id" value="<?php echo $download_id; ?>" />

	<?php foreach ( $price_ids as $price_id ) : ?>
	<input type="hidden" name="edd_free_download_price_id[]" value="<?php echo absint( $price_id ); ?>" />
	<?php endforeach; ?>

	<?php if ( $require_verification ) : ?>
		<div class="edd-free-downloads-verification-message-wrapper edd-alert edd-alert-info">
			<?php do_action( 'edd_free_downloads_before_verification_message', $download_id ); ?>
			<span class="edd-free-downloads-verification-message">
				<?php echo esc_html( edd_free_downloads_verify_message() ); ?>
			</span>
			<?php do_action( 'edd_free_downloads_after_verification_message', $download_id ); ?>
		</div>
	<?php endif; ?>

	<?php
	if ( false === $is_mobile && ! empty( $desktop_redirect ) ) {
		/**
		 * We are not on a mobile device ( i.e. we on a desktop view )
		 * and we have a url redirect set.
		 *
		 * @todo  This is the exact same html as the final `else` statement
		 * logic should be consolidated.
		 */
		?>
		<button name="edd_free_download_submit" class="edd-free-download-submit edd-submit button <?php echo esc_attr( $color ); ?>"><span><?php echo esc_html( $label); ?></span></button>
		<?php do_action( 'edd_free_downloads_after_desktop_redirect', $post );

	} elseif ( true === $is_mobile && ! empty( $mobile_redirect ) ) {
		/**
		 * We ARE on a mobile device and we have a redirect url redirect set.
		 */
		$url = $mobile_redirect;
		?>
		<a href="<?php echo esc_url( $url ); ?>" class="edd-free-download-submit edd-submit edd-add-to-cart <?php echo esc_attr( $color ); ?> button edd-free-download edd-free-download-single edd-has-js"><span><?php echo esc_html( $label ); ?></span></a>
		<?php do_action( 'edd_free_downloads_after_mobile_redirect', $post );

	} else {
		/**
		 * This is the standard submit button when no
		 * desktop or mobile redirect urls are set.
		 */
		?>
		<button name="edd_free_download_submit" class="edd-free-download-submit edd-submit button <?php echo esc_attr( $color ); ?>"><span><?php echo esc_html( $label ); ?></span></button>
		<?php do_action( 'edd_free_downloads_after_default_redirect', $post );
	}
	?>

	<?php if ( edd_get_option( 'edd_free_downloads_direct_download' ) && ! $require_verification ) : ?>
		<?php
		$link_text = edd_get_option( 'edd_free_downloads_direct_download_label', __( 'No thanks, proceed to download', 'edd-free-downloads' ) );

		echo '<div class="edd-free-downloads-direct-download"><a href="#" class="edd-free-downloads-direct-download-link">' . $link_text . '</a></div>';

		do_action( 'edd_free_downloads_after_direct_download_link', $post );

		?>
	<?php endif; ?>

	<?php do_action( 'edd_free_downloads_after_download_button', $post ); ?>
</form> -->
<?php if ( edd_get_option( 'edd_free_downloads_close_button', false ) ) : ?>
	<span class="edd-free-downloads-modal-close" aria-label="<?php _ex( 'close', 'close button for download modal','edd-free-downloads' ) ?>" role="button" tabindex="0">&#10005;</span>
<?php endif; ?>
<?php
/**
 * This template is called via AJAX and we switch the $post
 * object, thus we will reset it here
 */
wp_reset_postdata();
