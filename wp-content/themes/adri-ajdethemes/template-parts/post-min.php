<?php
/**
 * Template part for the Minimal Post layout.
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
	// Categories
	if ( get_theme_mod( 'blog_cat' ) ) {
		esc_attr( adri_ajdethemes_post_categories( 1, 'post-cat' ) );
	}
	// Title
	printf( '<h2 class="%s"><a href="%s">%s</a></h2>', 'post-title', esc_url( $post_link ), get_the_title() );
	?>

	<div class="post-meta">
		<?php
		// Author
		if ( get_theme_mod( 'blog_author' ) ) {
			printf( '<div class="post-author">%s</div>', get_the_author_link() );
		}
		// Date
		if ( get_theme_mod( 'blog_date' ) ) {
			printf( '<div class="post-date">%s</div>', get_the_date() );
		}
		?>
	</div><!-- end of .post-meta -->
	<?php
	// "Read More" Button
	if ( get_theme_mod( 'blog_more_btn' ) ) {
		printf(
			'<a href="%s" class="btn-txt-arr post-button">' . esc_html__( '%s', 'adri-ajdethemes' ) . ' <span class="arr-box"><i class="icon-Arrow-OutRight"></i></span></a>', 
			esc_url( $post_link ),
			get_theme_mod( 'blog_more_btn_text', 'Read more' )
		);
	}
	?>
	
</article>