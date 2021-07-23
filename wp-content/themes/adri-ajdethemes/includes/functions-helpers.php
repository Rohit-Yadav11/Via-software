<?php
/**
 * Theme specific functions
 *
 * @package AdriAjdethemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Prints HTML with meta information for the post categories (max: 2).
 * 
 * @package AdriAjdethemes
 */
if ( ! function_exists( 'adri_ajdethemes_post_categories' ) ) {
	function adri_ajdethemes_post_categories( $max = 2, $class ) {

		$i = 0;
		foreach( ( get_the_category() ) as $cat ) {
            printf( '<a href="%s" class="%s">%s</a>', esc_url( get_category_link( $cat->cat_ID ) ), $class, $cat->cat_name );
            if ( ++$i == $max ) break;
		}
	}
}


/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 * 
 */
if ( ! function_exists( 'adri_ajdethemes_pingback_header' ) ) {
	function adri_ajdethemes_pingback_header() {
		if ( is_singular() && pings_open() ) {
			echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
		}
	}
	add_action( 'wp_head', 'adri_ajdethemes_pingback_header' );
}


/**
 * Filter the excerpt length to 100 words.
 *
 * @param int $length Excerpt length.
 */
if ( ! function_exists( 'adri_ajdethemes_custom_excerpt_length' ) ) {
	function adri_ajdethemes_custom_excerpt_length( $length ) {
		return 100;
	}
	add_filter( 'excerpt_length', 'adri_ajdethemes_custom_excerpt_length', 999 );
}


/**
 * Customizable excerpt length.
 *
 * @package AdriAjdethemes
 */
if ( ! function_exists( 'adri_ajdethemes_excerpt' ) ) {
	function adri_ajdethemes_excerpt( $length = 25 ) {
		return esc_attr( wp_trim_words( get_the_excerpt(), $length ) );
	}
}


/**
 * Filter the excerpt "read more" string.
 *
 * @param string $more "Read more" excerpt string.
 */
if ( ! function_exists( 'adri_ajdethemes_excerpt_more' ) ) {
	function adri_ajdethemes_excerpt_more( $more ) {
		return '...';
	}
	add_filter( 'excerpt_more', 'adri_ajdethemes_excerpt_more' );
}


/**
 * Check if is a blog related page
 *
 * @package AdriAjdethemes
 */
if ( ! function_exists( 'adri_ajdethemes_is_blog' ) ) {
	function adri_ajdethemes_is_blog() {
		return ( is_archive() || is_author() || is_category() || is_home() || is_single() || is_tag() ) && 'post' == get_post_type();
	}
}


/**
 * Check if is a WooCommerce related page
 *
 * @package AdriAjdethemes
 */
if ( ! function_exists( 'adri_ajdethemes_is_woocommerce' ) ) {
	function adri_ajdethemes_is_woocommerce() {
		if ( class_exists( 'WooCommerce' ) ) {
			return ( is_shop() || is_cart() || is_checkout() || is_account_page() );
		}
	}
}


/**
 * Stop empty search query
 *
 * @package AdriAjdethemes
 */
if ( ! function_exists( 'adri_ajdethemes_stop_empty_search' ) ) {
	function adri_ajdethemes_stop_empty_search( $search, \WP_Query $q ) {
		
		if( ! is_admin() && empty( $search ) && $q->is_search() && $q->is_main_query() )
        $search .=" AND 0=1 ";

    	return $search;

	}
	add_filter( 'posts_search', 'adri_ajdethemes_stop_empty_search', 10, 2 );
}


/**
 * Add body class if the footer has "reveal" effect
 *
 * @package AdriAjdethemes
 */
if ( get_theme_mod( 'footer_has_reveal' ) ) {

	add_filter( 'body_class', function( $classes ) {
		return array_merge( $classes, array( 'footer-has-reveal' ) );
	} );

}


/**
 * Stops CF7 to go loco with the P tags
 *
 * @package AdriAjdethemes
 */
add_filter( 'wpcf7_autop_or_not', '__return_false' );



/**
 * Stops the widgets from auto adding p tags
 *
 * @package AdriAjdethemes
 */
if ( ! function_exists( 'adri_ajdethemes_remove_wpautop_from_widgets' ) ) {
	function adri_ajdethemes_remove_wpautop_from_widgets( $instance ) {
		$instance['filter'] = false;
		return $instance;
	}
	add_filter('widget_display_callback', 'adri_ajdethemes_remove_wpautop_from_widgets');
}