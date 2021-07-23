<?php
/**
 * Register (sidebar) widget areas.
 *
 * @package AdriAjdethemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Register sidebar blog widgets
if ( ! function_exists( 'adri_ajdethemes_widgets_init' ) ) {
	function adri_ajdethemes_widgets_init() {

		register_sidebar( array(
			'name'          => esc_html__( 'Blog Sidebar', 'adri-ajdethemes' ),
			'id'            => 'sidebar-blog',
			'description'   => esc_html__( 'Add widgets here.', 'adri-ajdethemes' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<span class="widget-title">',
			'after_title'   => '</span>',
		) );
		
		register_sidebar( array(
			'name'          => esc_html__( 'Blog Sidebar Bottom', 'adri-ajdethemes' ),
			'id'            => 'sidebar-blog-x',
			'description'   => esc_html__( 'Add widgets here.', 'adri-ajdethemes' ),
			'before_widget' => '<div class="col-md-6 col-lg-4"><div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div></div>',
			'before_title'  => '<span class="widget-title">',
			'after_title'   => '</span>',
		) );
		
		register_sidebar( array(
			'name'          => esc_html__( 'Footer Widgets', 'adri-ajdethemes' ),
			'id'            => 'footer-widgets',
			'description'   => esc_html__( 'Add widgets here.', 'adri-ajdethemes' ),
			'before_widget' => '<div class="col-md-6 col-lg-3"><div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div></div>',
			'before_title'  => '<span class="widget-title">',
			'after_title'   => '</span>',
		) );

		register_sidebar( array(
			'name'          => esc_html__( 'Shop Sidebar Bottom', 'adri-ajdethemes' ),
			'id'            => 'sidebar-shop-x',
			'description'   => esc_html__( 'Add shop widgets here.', 'adri-ajdethemes' ),
			'before_widget' => '<div class="col-md-6 col-lg-3"><div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div></div>',
			'before_title'  => '<span class="widget-title">',
			'after_title'   => '</span>',
		) );

	}
}
add_action( 'widgets_init', 'adri_ajdethemes_widgets_init' );