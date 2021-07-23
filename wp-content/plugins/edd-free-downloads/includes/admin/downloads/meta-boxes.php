<?php
/**
 * Meta boxes
 *
 * @package     EDD\FreeDownloads\Admin\Downloads\MetaBoxes
 * @since       1.2.0
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Register meta boxes
 *
 * @since       2.0.0
 * @return      void
 */
function edd_free_downloads_add_download_meta_boxes() {
	// Download post type
	add_meta_box( 'free-downloads', __( 'Free Downloads Settings', 'edd-free-downloads' ), 'edd_free_downloads_render_download_meta_box', 'download', 'side', 'default' );
}
add_action( 'add_meta_boxes', 'edd_free_downloads_add_download_meta_boxes' );


/**
 * Render meta box
 *
 * @since       2.0.0
 * @global      object $post The post we are editing
 * @return      void
 */
function edd_free_downloads_render_download_meta_box() {
	global $post;

	$post_id         = $post->ID;
	$bypass_modal    = get_post_meta( $post_id, '_edd_free_downloads_bypass', true ) ? 1 : 0;
	$direct_download = edd_get_option( 'edd_free_downloads_direct_download', false ) ? 1 : 0;
	$on_complete     = edd_get_option( 'edd_free_downloads_on_complete', false );
	$on_complete     = ( $on_complete == 'auto-download' ) ? 1 : 0;
	$download_file   = get_post_meta( $post_id, '_edd_free_downloads_file', true );
	$bundle_enable   = get_post_meta( $post_id, '_edd_free_downloads_bundle', false ) ? 1 : 0;
	$notes_title     = get_post_meta( $post_id, '_edd_free_downloads_notes_title', true );
	$notes           = get_post_meta( $post_id, '_edd_free_downloads_notes', true );
	?>
	<p><strong><?php _e( 'Bypass Modal:', 'edd-free-downloads' ); ?></strong></p>
	<label for="_edd_free_downloads_bypass">
		<?php echo EDD()->html->checkbox( array(
			'name'    => '_edd_free_downloads_bypass',
			'current' => $bypass_modal,
		) ); ?>
		<?php _e( 'Disable Free Downloads modal', 'edd-free-downloads' ); ?>
		<span alt="f223" class="edd-help-tip dashicons dashicons-editor-help" title="<strong><?php _e( 'Bypass Modal', 'edd-free-downloads' ); ?></strong>: <?php printf( __( 'When checked, this will get treated as a normal %s and will require the user to go through the checkout process.', 'edd-free-downloads' ), edd_get_label_singular( true ) ); ?>"></span>
	</label>

	<div class="edd-free-downloads-bundle-wrap">
		<p><strong><?php _e( 'Use On Bundle', 'edd-free-downloads' ); ?></strong></p>
		<label for="_edd_free_downloads_bundle">
			<?php echo EDD()->html->checkbox( array(
				'name'    => '_edd_free_downloads_bundle',
				'current' => $bundle_enable,
			) ); ?>
			<?php _e( 'Enforce use on bundle', 'edd-free-downloads' ); ?>
			<span alt="f223" class="edd-help-tip dashicons dashicons-editor-help" title="<strong><?php _e( 'Use On Bundle', 'edd-free-downloads' ); ?></strong>: <?php printf( __( 'Free Downloads can\'t reliably determine the value of products in a bundle, so by default it ignores bundles. Check this to enforce use on this bundle and treat all bundled %s as free.', 'edd-free-downloads' ), edd_get_label_plural( true ) ); ?>"></span>
		</label>
	</div>
	<?php
	if ( $direct_download || $on_complete ) {
		?>
		<p><strong><?php _e( 'Download Archive:', 'edd-free-downloads' ); ?></strong></p>
		<label for="_edd_free_downloads_file">
			<?php echo EDD()->html->text( array(
				'name'        => '_edd_free_downloads_file',
				'class'       => 'widefat',
				'placeholder' => 'http://',
				'value'       => $download_file,
			) ); ?>
			<?php _e( 'Enter archive URL, or leave blank to attempt auto-detection', 'edd-free-downloads' ); ?>
			<span alt="f223" class="edd-help-tip dashicons dashicons-editor-help" title="<strong><?php _e( 'Download Archive', 'edd-free-downloads' ); ?></strong>: <?php printf( __( 'If this %s includes multiple files, specify an archive file here to use for the Direct Download and Auto Download options.', 'edd-free-downloads' ), edd_get_label_singular( true ) ); ?>"></span>
		</label>
		<?php
	}

	if ( edd_get_option( 'edd_free_downloads_show_notes', false ) ) {
		?>
		<p><strong><?php _e( 'Notes Title', 'edd-free-downloads' ); ?></strong></p>
		<label for="_edd_free_downloads_notes_title">
			<?php echo EDD()->html->text( array(
				'name'  => '_edd_free_downloads_notes_title',
				'class' => 'widefat',
				'value' => $notes_title,
			) ); ?>
			<?php _e( 'Enter the title for the notes field, or leave blank to use the global option', 'edd-free-downloads' ); ?>
		</label>

		<p><strong><?php _e( 'Notes', 'edd-free-downloads' ); ?></strong></p>
		<label for="_edd_free_downloads_notes">
			<?php echo EDD()->html->textarea( array(
				'name'  => '_edd_free_downloads_notes',
				'class' => 'widefat',
				'value' => $notes,
			) ); ?>
			<?php _e( 'Enter any notes, or leave blank to use the global option', 'edd-free-downloads' ); ?>
		</label>
		<?php
	}

	do_action( 'edd_free_downloads_meta_box_settings_fields', $post_id );

	if ( ! edd_get_option( 'edd_free_downloads_disable_cache', false ) ) {
		echo '<div class="edd-free-downloads-cache-actions">';
		echo '<a href="' . wp_nonce_url( add_query_arg( 'edd-action', 'free_downloads_delete_cached_files' ), 'edd_free_downloads_cache_nonce', '_wpnonce' ) . '" class="button">' . __( 'Clear Cached Files', 'edd-free-downloads' ) . '</a>';
		echo '</div>';
	}

	wp_nonce_field( basename( __FILE__ ), 'edd_free_downloads_meta_box_nonce' );
}


/**
 * Save post meta when the save_post action is called
 *
 * @since       2.0.0
 * @param       int $post_id The ID of the post we are saving
 * @global      object $post The post we are saving
 * @return      void
 */
function edd_free_downloads_meta_box_save( $post_id ) {
	global $post;

	// Don't process if nonce can't be validated
	if ( ! isset( $_POST['edd_free_downloads_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['edd_free_downloads_meta_box_nonce'], basename( __FILE__ ) ) ) {
		return $post_id;
	}

	// Don't process if this is an autosave
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || isset( $_REQUEST['bulk_edit'] ) ) {
		return $post_id;
	}

	// Don't process if this is a revision
	if ( isset( $post->post_type ) && $post->post_type == 'revision' ) {
		return $post_id;
	}

	// Don't process if the current user shouldn't be editing this product
	if ( ! current_user_can( 'edit_product', $post_id ) ) {
		return $post_id;
	}

	// The default fields that get saved
	$fields = apply_filters( 'edd_free_downloads_meta_box_fields_save', array(
		'_edd_free_downloads_bypass',
		'_edd_free_downloads_bundle',
		'_edd_free_downloads_file',
		'_edd_free_downloads_notes_title',
		'_edd_free_downloads_notes',
	) );

	foreach ( $fields as $field ) {
		if ( isset( $_POST[ $field ] ) ) {
			if ( is_string( $_POST[ $field ] ) ) {
				$new = esc_attr( $_POST[ $field ] );
			} else {
				$new = $_POST[ $field ];
			}

			$new = apply_filters( 'edd_free_downloads_meta_box_save_' . $field, $new );

			update_post_meta( $post_id, $field, $new );
		} else {
			delete_post_meta( $post_id, $field );
		}
	}
}
add_action( 'save_post', 'edd_free_downloads_meta_box_save' );
