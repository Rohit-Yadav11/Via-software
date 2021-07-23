<?php
/**
 * Theme functions and definitions
 *
 * @package AdriAjdethemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/*
 * Constants & Globals
 */

define( 'ADRI_AJDETHEMES_VERSION', '1.0.0' );
define( 'ADRI_AJDETHEMES_DIR', get_template_directory() );
define( 'ADRI_AJDETHEMES_INC_DIR', get_template_directory() . '/includes' );
define( 'ADRI_AJDETHEMES_URI', get_template_directory_uri() );


if ( ! isset( $content_width ) ) {
	$content_width = 1140; // Pixels.
}


/**
 * Theme Setup
 *
 */
if ( ! function_exists( 'adri_ajdethemes_setup' ) ) { 
    function adri_ajdethemes_setup() {
        
        load_theme_textdomain( 'adri-ajdethemes', ADRI_AJDETHEMES_DIR . '/languages' );

        register_nav_menus( array( 
            'primary-menu'          => esc_html__( 'Primary', 'adri-ajdethemes' ),
            'secondary-menu-left'   => esc_html__( 'Secondary Left', 'adri-ajdethemes' ),
            'secondary-menu-right'  => esc_html__( 'Secondary Right', 'adri-ajdethemes' ),
            'footer-menu'           => esc_html__( 'Footer Menu', 'adri-ajdethemes' ),
        ) );
        
        add_theme_support( 'post-thumbnails' );
        add_theme_support( 'automatic-feed-links' );
        add_theme_support( 'title-tag' );
        add_theme_support(
            'html5',
            array(
                'search-form',
                'comment-form',
                'comment-list',
                'gallery',
                'caption',
            )
        );

        add_editor_style( 'editor-style.css' );
    }
}
add_action( 'after_setup_theme', 'adri_ajdethemes_setup' );


/**
 * Imports
 * 
 */
require_once ADRI_AJDETHEMES_INC_DIR . '/functions-enqueued.php';
require_once ADRI_AJDETHEMES_INC_DIR . '/functions-helpers.php';
require_once ADRI_AJDETHEMES_INC_DIR . '/functions-widgets.php';
require_once ADRI_AJDETHEMES_INC_DIR . '/functions-customizer.php';
require_once ADRI_AJDETHEMES_INC_DIR . '/functions-customizer-css.php';
require_once ADRI_AJDETHEMES_INC_DIR . '/functions-customizer-scss.php';
require_once ADRI_AJDETHEMES_INC_DIR . '/functions-editor.php';
if ( is_admin() ) { require_once ADRI_AJDETHEMES_INC_DIR . '/plugins/tgm/init.php'; }
require_once ADRI_AJDETHEMES_INC_DIR . '/functions-woocommerce.php';
require_once ADRI_AJDETHEMES_INC_DIR . '/functions-import-demo-data.php';