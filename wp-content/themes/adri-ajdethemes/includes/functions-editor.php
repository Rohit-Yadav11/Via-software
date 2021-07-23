<?php
/**
 * Theme defined WP block editor defaults.
 *
 * @package AdriAjdethemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Define theme - colors, font-sizes and 
 * custom units for the WP block editor
 *
 * @return void
 * @package AdriAjdethemes
 */
function adri_ajdethemes_block_editor_defaults() {

    // "Full" and "Wide" alignments
    add_theme_support( 'align-wide' );

    // Allow custom units
    add_theme_support( 'custom-units' );

    // Allow customizing the line height
    add_theme_support( 'custom-line-height' );

    // Colors
    add_theme_support( 'editor-color-palette', array(
        array(
            'name' => esc_html__( 'black', 'adri-ajdethemes' ),
            'slug' => 'black',
            'color' => '#262628',
        ),
        array(
            'name' => esc_html__( 'gray', 'adri-ajdethemes' ),
            'slug' => 'gray',
            'color' => '#66686F',
        ),
        array(
            'name' => esc_html__( 'gray light', 'adri-ajdethemes' ),
            'slug' => 'gray-light',
            'color' => '#AAABAF',
        ),
        array(
            'name' => esc_html__( 'light', 'adri-ajdethemes' ),
            'slug' => 'light',
            'color' => '#DDDDDD',
        ),
        array(
            'name' => esc_html__( 'lighter', 'adri-ajdethemes' ),
            'slug' => 'lighter',
            'color' => '#f4f4f4',
        ),
        array(
            'name' => esc_html__( 'impact black', 'adri-ajdethemes' ),
            'slug' => 'impact-black',
            'color' => '#131515',
        ),
        array(
            'name' => esc_html__( 'impact yellow', 'adri-ajdethemes' ),
            'slug' => 'impact-yellow',
            'color' => '#FFD400',
        ),
        array(
            'name' => esc_html__( 'energy blue', 'adri-ajdethemes' ),
            'slug' => 'energy-blue',
            'color' => '#152860',
        ),
        array(
            'name' => esc_html__( 'energy green', 'adri-ajdethemes' ),
            'slug' => 'energy-green',
            'color' => '#A1EF8B',
        ),
        array(
            'name' => esc_html__( 'royal green', 'adri-ajdethemes' ),
            'slug' => 'royal-green',
            'color' => '#104547',
        ),
        array(
            'name' => esc_html__( 'royal yellow', 'adri-ajdethemes' ),
            'slug' => 'royal-yellow',
            'color' => '#F2CD5D',
        ),
        array(
            'name' => esc_html__( 'sass blue', 'adri-ajdethemes' ),
            'slug' => 'sass-blue',
            'color' => '#3772FF',
        ),
        array(
            'name' => esc_html__( 'mint green', 'adri-ajdethemes' ),
            'slug' => 'mint-green',
            'color' => '#4DBC65',
        ),
        array(
            'name' => esc_html__( 'smooth blue', 'adri-ajdethemes' ),
            'slug' => 'smooth-blue',
            'color' => '#29335C',
        ),
        array(
            'name' => esc_html__( 'smooth red', 'adri-ajdethemes' ),
            'slug' => 'smooth-red',
            'color' => '#DB2B39',
        ),
    ) );

    // Font Sizes
    add_theme_support( 'editor-font-sizes', array(
        array(
            'name' => __( 'Small', 'adri-ajdethemes' ),
            'size' => 14,
            'slug' => 'small'
        ),
        array(
            'name' => __( 'H6', 'adri-ajdethemes' ),
            'size' => 16,
            'slug' => 'h6-size'
        ),
        array(
            'name' => __( 'H5', 'adri-ajdethemes' ),
            'size' => 18,
            'slug' => 'h5-size'
        ),
        array(
            'name' => __( 'H4', 'adri-ajdethemes' ),
            'size' => 22,
            'slug' => 'h4-size'
        ),
        array(
            'name' => __( 'H3', 'adri-ajdethemes' ),
            'size' => 26,
            'slug' => 'h3-size'
        ),
        array(
            'name' => __( 'H2', 'adri-ajdethemes' ),
            'size' => 30,
            'slug' => 'h2-size'
        ),
        array(
            'name' => __( 'H1', 'adri-ajdethemes' ),
            'size' => 34,
            'slug' => 'h1-size'
        )
    ) );
}
add_action( 'after_setup_theme', 'adri_ajdethemes_block_editor_defaults' );