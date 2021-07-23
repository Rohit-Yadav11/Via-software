<?php
/**
 * Previous/Next post navigation (single post)
 *
 * @package AdriAjdethemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
} ?>


<div class="post-nav">
	<?php 
	if ( ! empty( get_previous_post() ) ) : ?>
	<div class="post-nav-prev">
		<span class="post-nav-control"><?php esc_html_e( 'Previous post', 'adri-ajdethemes' ); ?></span>

		<div class="post-nav-thumb prev">
		<?php 
			previous_post_link( 
				'%link', 
					'<span class="post-nav-label">' . esc_html__( 'Previous post', 'adri-ajdethemes' ) . '</span>' . 
					'<h6>' . esc_html__( '%title', 'adri-ajdethemes' ) . '</h6>
					<i class="icon-Arrow-OutLeft"></i>'
			); 
		?>
		</div>
	</div><!-- end of .post-nav-prev -->
	<?php 
	endif;
	
	if ( ! empty( get_next_post() ) ) : ?>
	<div class="post-nav-next">
		<span class="post-nav-control"><?php esc_html_e( 'Next post', 'adri-ajdethemes' ); ?></span>

		<div class="post-nav-thumb next">
		<?php 
			next_post_link( 
				'%link', 
					'<h6>' . esc_html__( '%title', 'adri-ajdethemes' ) . '</h6>
					<i class="icon-Arrow-OutRight"></i>
					<span class="post-nav-label">' . esc_html__( 'Next post', 'adri-ajdethemes' ) . '</span>'
			);
		?>
		</div>
	</div><!-- end of .post-nav-next -->
	<?php endif; ?>
</div><!-- end of .post-nav -->