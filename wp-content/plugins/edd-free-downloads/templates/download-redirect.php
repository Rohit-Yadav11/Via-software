<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<div id="edd-free-downloads-modal" class="edd-free-downloads-mobile">

<?php

global $wp_query;

// Pull user data if available
if ( is_user_logged_in() ) {
	$user = new WP_User( get_current_user_id() );
}

$require_verification = edd_free_downloads_verify_email();

$email = isset( $user ) ? $user->user_email : '';
$fname = isset( $user ) ? $user->user_firstname : '';
$lname = isset( $user ) ? $user->user_lastname : '';

$rname = edd_get_option( 'edd_free_downloads_require_name', false ) ? ' <span class="edd-free-downloads-required">*</span>' : '';

// Get EDD vars
$color = edd_get_option( 'checkout_color', 'blue' );
$color = ( $color == 'inherit' ) ? '' : $color;
$label = edd_get_option( 'edd_free_downloads_modal_button_label', __( 'Download Now', 'edd-free-downloads' ) );

$require_login = edd_no_guest_checkout();
?>
<form id="edd_free_download_form" method="post">
	<?php do_action( 'edd_free_downloads_before_redirect_form', $wp_query ); ?>

	<p>
		<label for="edd_free_download_email" class="edd-free-downloads-label"><?php _e( 'Email Address', 'edd-free-downloads' ); ?> <span class="edd-free-downloads-required">*</span></label>
		<input type="text" name="edd_free_download_email" id="edd_free_download_email" class="edd-free-download-field" placeholder="<?php _e( 'Email Address', 'edd-free-downloads' ); ?>" value="<?php echo $email; ?>" />
	</p>

	<?php if ( edd_get_option( 'edd_free_downloads_get_name', false ) ) : ?>
	<p>
		<label for="edd_free_download_fname" class="edd-free-downloads-label"><?php echo __( 'First Name', 'edd-free-downloads' ) . $rname; ?></label>
		<input type="text" name="edd_free_download_fname" id="edd_free_download_fname" class="edd-free-download-field" placeholder="<?php _e( 'First Name', 'edd-free-downloads' ); ?>" value="<?php echo $fname; ?>" />
	</p>

	<p>
		<label for="edd_free_download_lname" class="edd-free-downloads-label"><?php echo __( 'Last Name', 'edd-free-downloads' ) . $rname; ?></label>
		<input type="text" name="edd_free_download_lname" id="edd_free_download_lname" class="edd-free-download-field" placeholder="<?php _e( 'Last Name', 'edd-free-downloads' ); ?>" value="<?php echo $lname; ?>" />
	</p>
	<?php endif; ?>

	<?php if ( edd_get_option( 'edd_free_downloads_user_registration', false ) && ! is_user_logged_in() && ! class_exists( 'EDD_Auto_Register' ) ) : ?>
	<hr />

	<?php do_action( 'edd_free_downloads_before_redirect_form_registration', $wp_query ); ?>

	<p>
		<label for="edd_free_download_username" class="edd-free-downloads-label">
			<?php if ( $require_login ) : ?>
				<span class="edd-free-downloads-required">*</span>
			<?php endif; ?>
			<?php _e( 'Username', 'edd-free-downloads' ); ?> <span class="edd-free-downloads-required">*</span>
		</label>
		<input type="text" name="edd_free_download_username" id="edd_free_download_username" class="edd-free-download-field" placeholder="<?php _e( 'Username', 'edd-free-downloads' ); ?>" value="" />
	</p>

	<p>
		<label for="edd_free_download_pass" class="edd-free-downloads-label">
			<?php if ( $require_login ) : ?>
				<span class="edd-free-downloads-required">*</span>
			<?php endif; ?>
			<?php _e( 'Password', 'edd-free-downloads' ); ?> <span class="edd-free-downloads-required">*</span>
		</label>
		<input type="password" name="edd_free_download_pass" id="edd_free_download_pass" class="edd-free-download-field" />
	</p>

	<p>
		<label for="edd_free_download_pass2" class="edd-free-downloads-label">
			<?php if ( $require_login ) : ?>
				<span class="edd-free-downloads-required">*</span>
			<?php endif; ?>
			<?php _e( 'Confirm Password', 'edd-free-downloads' ); ?> <span class="edd-free-downloads-required">*</span>
		</label>
		<input type="password" name="edd_free_download_pass2" id="edd_free_download_pass2" class="edd-free-download-field" />
	</p>

	<?php do_action( 'edd_free_downloads_after_redirect_form_registration', $wp_query ); ?>

	<?php endif; ?>

	<?php if ( edd_get_option( 'edd_free_downloads_newsletter_optin', false ) && edd_free_downloads_has_newsletter_plugin() ) : ?>
	<?php $check_by_default = edd_get_option( 'edd_free_downloads_newsletter_auto_checked', false ); ?>
	<p>
		<input type="checkbox" name="edd_free_download_optin" id="edd_free_download_optin" <?php checked( true, $check_by_default, true ); ?> />
		<label for="edd_free_download_optin" class="edd-free-downloads-checkbox-label"><?php echo edd_get_option( 'edd_free_downloads_newsletter_optin_label', __( 'Subscribe to our newsletter', 'edd-free-downloads' ) ); ?></label>
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

	<?php if ( edd_get_option( 'edd_free_downloads_show_notes', false ) ) : ?>
		<?php
			$title = $content = '';

			if ( is_singular( 'download' ) ) {
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
			}
		?>
		<div class="edd-free-downloads-note-wrapper">
			<div class="edd-free-downloads-note-title"><strong><?php echo $title; ?></strong></div>
			<p class="edd-free-downloads-note-content"><?php echo wpautop( stripslashes( $content ) ); ?></p>
		</div>
	<?php endif; ?>

	<?php if ( $require_verification ) : ?>
		<div class="edd-free-downloads-verification-message-wrapper edd-alert edd-alert-info">
			<?php do_action( 'edd_free_downloads_before_verification_message', $download_id ); ?>
			<span class="edd-free-downloads-verification-message">
				<?php echo esc_html( edd_free_downloads_verify_message() ); ?>
			</span>
			<?php do_action( 'edd_free_downloads_after_verification_message', $download_id ); ?>
		</div>
	<?php endif; ?>

	<?php do_action( 'edd_free_downloads_after_redirect_form', $wp_query ); ?>

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
	<input type="hidden" name="edd_free_download_id" value="<?php echo $wp_query->query_vars['download_id']; ?>" />

	<?php
	// Detect if the price_ids are present.
	$price_ids = ! empty( $_GET['price_ids'] ) ? json_decode( $_GET['price_ids'] ) : false;
	?>

	<?php if ( ! empty( $price_ids )  ) : // If items are present, and json_decode is successful, output the price_ids. ?>
		<?php foreach ( $price_ids as $price_id ) : ?>
			<input type="hidden" name="edd_free_download_price_id[]" value="<?php echo absint( $price_id ); ?>" />
		<?php endforeach; ?>
	<?php endif; ?>

	<button name="edd_free_download_submit" class="edd-free-download-submit button <?php echo $color; ?>"><span><?php echo $label; ?></span></button>
	<button name="edd_free_download_cancel" class="edd-free-download-cancel button <?php echo $color; ?>"><span><?php _e( 'Cancel', 'edd-free-downloads' ); ?></span></button>

	<?php if ( edd_get_option( 'edd_free_downloads_direct_download' ) && ! $require_verification ) : ?>
		<?php
		$link_text = edd_get_option( 'edd_free_downloads_direct_download_label', __( 'No thanks, proceed to download', 'edd-free-downloads' ) );

		echo '<div class="edd-free-downloads-direct-download"><a href="#" class="edd-free-downloads-direct-download-link">' . $link_text . '</a></div>';
		?>
	<?php endif; ?>

	<?php do_action( 'edd_free_downloads_after_download_button', $post ); ?>
</form>

<script type="text/javascript">
	jQuery(document).ready(function ($) {
		$("#edd_free_download_email").focus();
		$("#edd_free_download_email").select();
	});
</script>
</div>
<?php wp_footer(); ?>

</body>
</html>
