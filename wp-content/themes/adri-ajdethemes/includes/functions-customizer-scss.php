<?php
/**
 * WP Customizer edit SCSS vars
 *
 * Requires the WP-SCSS plugin.
 *
 * @package AdriAjdethemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

// Check if WP-SCSS plugin is active.
if ( ! class_exists( 'Wp_Scss' ) ) {
    return;
}


// Always recompile in the customizer.
if ( is_customize_preview() && ! defined( 'WP_SCSS_ALWAYS_RECOMPILE' ) ) {
    define( 'WP_SCSS_ALWAYS_RECOMPILE', true );
}


// Update the default paths to match theme.
$wpscss_options = get_option( 'wpscss_options' );

if ( $wpscss_options['scss_dir'] !== '/sass/' || $wpscss_options['css_dir'] !== '/' ) {
    
    // Alter the options array appropriately.
    $wpscss_options['scss_dir'] = '/assets/scss/';
    $wpscss_options['css_dir']  = '/';
 
    // Update entire array
    update_option( 'wpscss_options', $wpscss_options );
}


// Default Color - Vars
$default_colors = array(
    'primary'    => '#FFD400',
    'secondary'  => '#131515',
    'gray'       => '#66686F',
    'gray-light' => '#AAABAF',
    'light'      => '#DDDDDD',
    'lighter'    => '#F4F4F4',
    'text-color' => '#66686F',
    'footer-bg'  => '#ffd400',
);

// Default Typography - Vars
$default_typography = array(
    'font-family'    => '"Poppins", "Helvetica Neue", "Helvetica", sans-serif;',
    'fs-base'        => '1rem',
    'fs-small'       => '.87rem',

    'fw-light'       => '300',
    'fw-reg'         => '400',
    'fw-sbold'       => '600',
    'fw-bold'        => '700',

    'font-weight'    => '300',
    'h-font-weight'  => '700',

    'h-font-family'    => '"Poppins", "Helvetica Neue", "Helvetica", sans-serif;',

    'h1-size'        => '2.125rem',
    'h2-size'        => '1.875rem',
    'h3-size'        => '1.625rem',
    'h4-size'        => '1.375rem',
    'h5-size'        => '1.125rem',
    'h6-size'        => '1rem',
);

/**
 * Update SCSS color variables
 *
 * @return array
 */
function adri_ajdethemes_set_color_variables() {
 
    // Get the default colors.
    global $default_colors;
 
    // Create an array of variables.
    $variables = array();
 
    // Loop through each variable and get theme_mod.
    foreach ( $default_colors as $key => $value ) {

        $variables[ $key ] = get_theme_mod( $key, $value );

    }
    
    return $variables;
    
}
add_filter( 'wp_scss_variables', 'adri_ajdethemes_set_color_variables' );


/**
 * Register settings and controls with the Customizer.
 *
 * @return void
 */
function adri_ajdethemes_customizer_register_colors() {
 
    global $wp_customize;
    global $default_colors;
 
    // Loop through each color variable in the array.
    foreach ( $default_colors as $key => $value ) {
 
        // Add setting for each variable.
        $wp_customize->add_setting( $key, array(
            'capability'        => 'edit_theme_options',
            'default'           => $value,
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'refresh'
        ) );
 
        // Add control for each variable.
        $wp_customize->add_control(
            new WP_Customize_Color_Control(
                $wp_customize,
                $key,
                array(
                    'label'    => esc_html( ucwords( str_replace( 'color-', '', $key ) ) . esc_html__( ' Color', 'adri-ajdethemes' ) ),
                    'description'   => esc_html__( 'Publish, close customizer and refresh the page to apply the change.', 'adri-ajdethemes' ),
                    'section'  => 'colors',
                    'settings' => $key,
                ) )
        );
    }

    // Footer Background Color
    $wp_customize->add_setting( 'footer-bg', array(
        'capability'        => 'edit_theme_options',
        'default'           => '',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'refresh'
    ) );

    $wp_customize->add_control( 
        new WP_Customize_Color_Control(
            $wp_customize, 'footer-bg',
            array(
                'label'    => esc_html__( 'Footer Background Color', 'adri-ajdethemes' ),
                'description'   => esc_html__( 'Publish, close customizer and refresh the page to apply the change.', 'adri-ajdethemes' ),
                'section'  => 'footer',
            ) )
    );
}
add_action( 'customize_register', 'adri_ajdethemes_customizer_register_colors' );