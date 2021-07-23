<?php
/**
 * Template part for the Card Post layout.
 *
 * @package AdriAjdethemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


?>
<article 
	<?php
	// class="..."
	echo esc_attr( post_class( 'post-card' ) ); 
	// id="post-..." ?> 
	id="post-<?php esc_attr( the_ID() ); ?>" 
	<?php
	// Feature Image
	if ( has_post_thumbnail( $post->ID ) ) : ?>
	style="background-image: url(<?php echo esc_url( get_the_post_thumbnail_url( $post, 'full' ) ); ?>);" 
	<?php endif; ?>
>

	<a href="<?php echo esc_url( get_permalink() ); ?>">
		<?php
		// Date
		if ( get_theme_mod( 'blog_date' ) ) {
			printf( '<span class="post-date">' . esc_html__( '%s', 'adri-ajdethemes' ) . '</span>', get_the_date() );
		}
		// Title
		printf( '<h4 class="post-title">' . esc_html__( '%s', 'adri-ajdethemes' ) . '</h4>', get_the_title() );

		// Button "Read More"
		if ( get_theme_mod( 'blog_more_btn' ) ) {
			printf( '<span class="post-cta"><span>' . esc_html__( '%s', 'adri-ajdethemes' ) . '</span></span>', get_theme_mod( 'blog_more_btn_text', 'Read more' ) );
		}
		?>

	</a>
</article>