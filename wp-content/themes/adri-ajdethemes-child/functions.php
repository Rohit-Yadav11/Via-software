<?php 
/**
 * Child theme functions and definitions
 *
 * @package AdriAjdethemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Enqueue styles
 *
 * @return void
 */
function adri_ajdethemes_child_styles() {
    wp_enqueue_style( 'adri_ajdethemes-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css');
}
add_action( 'wp_enqueue_scripts', 'adri_ajdethemes_child_styles', 1000 );