<?php
/**
 * Theme functions and definitions
 *
 * @package AdriAjdethemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'adri_ajdethemes_scripts_styles' ) ) {
    /**
	 * Theme Scripts & Styles.
	 *
	 * @return void
	 */
    function adri_ajdethemes_scripts_styles() {
        // Bootstrap 4 - Reboot
        wp_enqueue_style( 'bootstrap-reboot', ADRI_AJDETHEMES_URI . '/assets/css/vendors/bootstrap-reboot.min.css', [], '4.4.1');
        // Bootstrap 4 - Grid
        wp_enqueue_style( 'bootstrap-grid', ADRI_AJDETHEMES_URI . '/assets/css/vendors/bootstrap-grid.min.css', [], '4.4.1');
        // Google Fonts
        wp_enqueue_style( 'adri_ajdethemes-google-fonts', 'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap' );
        // IconsMind Font
        wp_enqueue_style( 'iconsmind', ADRI_AJDETHEMES_URI . '/assets/fonts/im/im-style.css', [], '1.3' );
        // FontAwesome
        wp_enqueue_style( 'fontawesome', ADRI_AJDETHEMES_URI . '/assets/fonts/fa-brands/fontawesome.css', [], '5.13.0' );
        // FontAwesome (Solid) Font
        wp_enqueue_style( 'fontawesome-solid', ADRI_AJDETHEMES_URI . '/assets/fonts/fa-brands/solid.css', [], '5.13.0' );
        // FontAwesome (Brands) Font
        wp_enqueue_style( 'fontawesome-brands', ADRI_AJDETHEMES_URI . '/assets/fonts/fa-brands/brands.css', [], '5.13.0' );
        // Rellax JS (parallax)
        wp_enqueue_script( 'rellax', ADRI_AJDETHEMES_URI . '/assets/js/vendors/rellax.min.js', array( 'jquery' ), ADRI_AJDETHEMES_VERSION, true );
        // Swiper Slider
        wp_enqueue_style( 'swiper', ADRI_AJDETHEMES_URI . '/assets/js/vendors/swiperjs/swiper-bundle.min.css', ADRI_AJDETHEMES_VERSION );
        wp_enqueue_script( 'swiper', ADRI_AJDETHEMES_URI . '/assets/js/vendors/swiperjs/swiper-bundle.min.js', array( 'jquery' ), ADRI_AJDETHEMES_VERSION, true );
        // Theme CSS
        wp_enqueue_style( 'adri_ajdethemes-style', ADRI_AJDETHEMES_URI . '/style.css' );
        // Customizer Inline CSS
        wp_add_inline_style( 'adri_ajdethemes-style', adri_ajdethemes_customizer_css() );

        // Theme JS
        wp_enqueue_script( 'adri_ajdethemes-js', ADRI_AJDETHEMES_URI . '/assets/js/main.js', array( 'jquery' ), ADRI_AJDETHEMES_VERSION, true );
    }
}
add_action( 'wp_enqueue_scripts', 'adri_ajdethemes_scripts_styles' );



if ( ! function_exists( 'adri_ajdethemes_admin_scripts_styles' ) ) {
    /**
	 * Admin Scripts & Styles.
	 *
	 * @return void
	 */
    function adri_ajdethemes_admin_scripts_styles() {
        // Google Fonts
        wp_enqueue_style( 'adri_ajdethemes-google-fonts', 'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap' );
        // FontAwesome
        wp_enqueue_style( 'fontawesome', ADRI_AJDETHEMES_URI . '/assets/fonts/fa-brands/fontawesome.css', [], '5.13.0' );
        // FontAwesome (Solid) Font
        wp_enqueue_style( 'fontawesome-solid', ADRI_AJDETHEMES_URI . '/assets/fonts/fa-brands/solid.css', [], '5.13.0' );
	    // Admin Styles
	    wp_enqueue_style( 'adri_ajdethemes-admin-style', ADRI_AJDETHEMES_URI . '/editor-style.css' );
    }
    add_action( 'admin_enqueue_scripts', 'adri_ajdethemes_admin_scripts_styles' );
}