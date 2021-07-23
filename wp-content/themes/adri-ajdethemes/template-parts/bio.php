<?php
/**
 * The template part for displaying an Author biography
 *
 * @package AdriAjdethemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


add_filter( 'get_the_author_description', 'do_shortcode');
?>

<div class="row">
	<div class="col-lg-12">

		<aside class="author-bio">		
			<?php printf( esc_attr( '%s' ), get_avatar( get_the_author_meta( 'ID' ) ) ); ?>
		
			<div class="author-content">
				<div class="bio-nickname">
					<?php esc_html_e( 'About the author', 'adri-ajdethemes' ); ?>
				</div>

				<div class="bio-name">
					<?php printf( esc_html__( '%s', 'adri-ajdethemes' ), get_the_author_meta( 'display_name' ) ); ?>
				</div>
		
				<div class="bio-description">
					<?php printf( wp_kses_post( __( '%s', 'adri-ajdethemes' ) ), get_the_author_meta( 'description' ) ); ?>
				</div>
			</div><!-- end of .author-content -->
		</aside><!-- end of .author-bio -->

	</div><!-- end of .col-lg-12 -->
</div><!-- end of .row -->

