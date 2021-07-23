<?php
/**
 * Template part for the Classic Post layout.
 *
 * @package AdriAjdethemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$post_link = get_permalink();
$post_class = get_theme_mod( 'blog_layout', 'post-classic' );
$excerpt_length = get_theme_mod( 'blog_excerpt_length', 25 );
?>

<article <?php esc_attr( post_class( $post_class ) ); ?> id="post-<?php esc_attr( the_ID() ); ?>">
                            
	<?php
	// Date (badge)
	if ( get_theme_mod( 'blog_date', true ) ) {
		printf( '<div class="post-date"><span>%s</span>%s</div>', get_the_time( 'd' ), get_the_time( 'M' ) );
	}

	// Feature Image
	printf( '<a href="%s" class="post-thumbnail-link">%s</a>', esc_url( $post_link ), get_the_post_thumbnail( $post, 'large' ) ); 
	?>

	<div class="post-meta">
		<?php
		// Categories
		if ( get_theme_mod( 'blog_cat', true ) ) {
			esc_attr( adri_ajdethemes_post_categories( 1, 'post-cat' ) );
		} ?>
	</div><!-- end of .post-meta -->

	<?php
	// Title & Excerpt
	printf( '<h2 class="%s"><a href="%s">%s</a></h2>', 'post-title', esc_url( $post_link ), get_the_title() );
	printf( '<p>%s</p>', esc_attr( adri_ajdethemes_excerpt( $excerpt_length ) ) );

	// "Read More" Button
	if ( get_theme_mod( 'blog_more_btn', true ) ) {
		printf(
			'<a href="%s" class="btn-txt-arr post-button">' . esc_html__( '%s', 'adri-ajdethemes' ) . ' <span class="arr-box"><i class="icon-Arrow-OutRight"></i></span></a>', 
			esc_url( $post_link ),
			get_theme_mod( 'blog_more_btn_text', 'Read more' )
		);
	}
	?>
	
</article>