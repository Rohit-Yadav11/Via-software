<?php
/**
 * Predefined demo imports for the 
 * plugin "One Click Demo Imports"
 *
 * @package AdriAjdethemes
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


if ( ! function_exists( 'adri_ajdethemes_ocdi_import_files' ) ) {
    function adri_ajdethemes_ocdi_import_files() {

        $notice = "
          <h4>" . esc_html__( 'After the import is complete:', 'adri-ajdethemes' ) . "</h4>
          <p>" . esc_html__( '1. Don\'t import more than one demo in one WP installation.', 'adri-ajdethemes' ) . "</p>
          <p>" . esc_html__( '2. Import the slider manually via Slider Revolution from the exports folder, if is not pulled via the import.', 'adri-ajdethemes' ) . "</p>
          <p>" . esc_html__( '3. Setup the correct Menu Locations after the import (Dashboard > Appearance > Menus > Menu Location).', 'adri-ajdethemes' ) . "</p>
          <p>" . esc_html__( '4. Disable Elementor default colors and fonts (Dashboard > Elementor > Settings) after the import.', 'adri-ajdethemes' ) . "</p>
          <p>" . esc_html__( '5. Check the documentation.', 'adri-ajdethemes' ) . "</p>
        ";

        return array(
          array(
            'import_file_name'           => 'Demo - Impact',
            'categories'                 => array( 'Multi Page' ),
            'import_file_url'            => 'https://cloud.ajdethemes.com/adri-demo-import/demo-impact/adri-demo-impact.xml',
            'import_widget_file_url'     => 'https://cloud.ajdethemes.com/adri-demo-import/demo-impact/adri-demo-impact-widgets.wie',
            'import_customizer_file_url' => 'https://cloud.ajdethemes.com/adri-demo-import/demo-impact/adri-demo-impact-customizer.dat',
            'import_preview_image_url'   => 'https://cloud.ajdethemes.com/adri-demo-import/demo-impact/01_impact.jpg',
            'import_notice'              => $notice,
            'preview_url'                => 'https://ajdethemes.com/adri/impact/',
          ),
          array(
            'import_file_name'           => 'Demo - SASS',
            'categories'                 => array( 'Multi Page' ),
            'import_file_url'            => 'https://cloud.ajdethemes.com/adri-demo-import/demo-sass/adri-demo-sass.xml',
            'import_widget_file_url'     => 'https://cloud.ajdethemes.com/adri-demo-import/demo-sass/adri-demo-sass-widgets.wie',
            'import_customizer_file_url' => 'https://cloud.ajdethemes.com/adri-demo-import/demo-sass/adri-demo-sass-customizer.dat',
            'import_preview_image_url'   => 'https://cloud.ajdethemes.com/adri-demo-import/demo-sass/02_sass.jpg',
            'import_notice'              => $notice,
            'preview_url'                => 'https://ajdethemes.com/adri/sass/',
          ),
          array(
            'import_file_name'           => 'Demo - Royal',
            'categories'                 => array( 'Multi Page' ),
            'import_file_url'            => 'https://cloud.ajdethemes.com/adri-demo-import/demo-royal/adri-demo-royal.xml',
            'import_widget_file_url'     => 'https://cloud.ajdethemes.com/adri-demo-import/demo-royal/adri-demo-royal-widgets.wie',
            'import_customizer_file_url' => 'https://cloud.ajdethemes.com/adri-demo-import/demo-royal/adri-demo-royal-customizer.dat',
            'import_preview_image_url'   => 'https://cloud.ajdethemes.com/adri-demo-import/demo-royal/03_royal.jpg',
            'import_notice'              => $notice,
            'preview_url'                => 'https://ajdethemes.com/adri/royal/',
          ),
          array(
            'import_file_name'           => 'Demo - Mint',
            'categories'                 => array( 'Multi Page' ),
            'import_file_url'            => 'https://cloud.ajdethemes.com/adri-demo-import/demo-mint/adri-demo-mint.xml',
            'import_widget_file_url'     => 'https://cloud.ajdethemes.com/adri-demo-import/demo-mint/adri-demo-mint-widgets.wie',
            'import_customizer_file_url' => 'https://cloud.ajdethemes.com/adri-demo-import/demo-mint/adri-demo-mint-customizer.dat',
            'import_preview_image_url'   => 'https://cloud.ajdethemes.com/adri-demo-import/demo-mint/04_mint.jpg',
            'import_notice'              => $notice,
            'preview_url'                => 'https://ajdethemes.com/adri/mint/',
          ),
          array(
            'import_file_name'           => 'Demo - Energy',
            'categories'                 => array( 'Multi Page' ),
            'import_file_url'            => 'https://cloud.ajdethemes.com/adri-demo-import/demo-energy/adri-demo-energy.xml',
            'import_widget_file_url'     => 'https://cloud.ajdethemes.com/adri-demo-import/demo-energy/adri-demo-energy-widgets.wie',
            'import_customizer_file_url' => 'https://cloud.ajdethemes.com/adri-demo-import/demo-energy/adri-demo-energy-customizer.dat',
            'import_preview_image_url'   => 'https://cloud.ajdethemes.com/adri-demo-import/demo-energy/05_energy.jpg',
            'import_notice'              => $notice,
            'preview_url'                => 'https://ajdethemes.com/adri/energy/',
          ),
          array(
            'import_file_name'           => 'Demo - Mono',
            'categories'                 => array( 'Multi Page' ),
            'import_file_url'            => 'https://cloud.ajdethemes.com/adri-demo-import/demo-mono/adri-demo-mono.xml',
            'import_widget_file_url'     => 'https://cloud.ajdethemes.com/adri-demo-import/demo-mono/adri-demo-mono-widgets.wie',
            'import_customizer_file_url' => 'https://cloud.ajdethemes.com/adri-demo-import/demo-mono/adri-demo-mono-customizer.dat',
            'import_preview_image_url'   => 'https://cloud.ajdethemes.com/adri-demo-import/demo-mono/06_mono.jpg',
            'import_notice'              => $notice,
            'preview_url'                => 'https://ajdethemes.com/adri/mono/',
          ),
          array(
            'import_file_name'           => 'Demo - DarkMode',
            'categories'                 => array( 'One Page' ),
            'import_file_url'            => 'https://cloud.ajdethemes.com/adri-demo-import/demo-darkmode/adri-demo-darkmode.xml',
            'import_widget_file_url'     => 'https://cloud.ajdethemes.com/adri-demo-import/demo-darkmode/adri-demo-darkmode-widgets.wie',
            'import_customizer_file_url' => 'https://cloud.ajdethemes.com/adri-demo-import/demo-darkmode/adri-demo-darkmode-customizer.dat',
            'import_preview_image_url'   => 'https://cloud.ajdethemes.com/adri-demo-import/demo-darkmode/07_darkmode.jpg',
            'import_notice'              => $notice,
            'preview_url'                => 'https://ajdethemes.com/adri/dark/',
          ),
          array(
            'import_file_name'           => 'Demo - Smooth',
            'categories'                 => array( 'One Page' ),
            'import_file_url'            => 'https://cloud.ajdethemes.com/adri-demo-import/demo-smooth/adri-demo-smooth.xml',
            'import_widget_file_url'     => 'https://cloud.ajdethemes.com/adri-demo-import/demo-smooth/adri-demo-smooth-widgets.wie',
            'import_customizer_file_url' => 'https://cloud.ajdethemes.com/adri-demo-import/demo-smooth/adri-demo-smooth-customizer.dat',
            'import_preview_image_url'   => 'https://cloud.ajdethemes.com/adri-demo-import/demo-smooth/08_smooth.jpg',
            'import_notice'              => $notice,
            'preview_url'                => 'https://ajdethemes.com/adri/smooth/',
          )
        );
    }
}
add_filter( 'pt-ocdi/import_files', 'adri_ajdethemes_ocdi_import_files' );



if ( ! function_exists( 'adri_ajdethemes_ocdi_after_import_setup' ) ) {
  function adri_ajdethemes_ocdi_after_import_setup( $selected_import ) {

    // Set the menu locations after import  
    $secondary_left   = get_term_by( 'name', 'Secondary Left', 'nav_menu' );
    $secondary_right  = get_term_by( 'name', 'Secondary Right', 'nav_menu' );
    $footer_menu      = get_term_by( 'name', 'Footer Menu', 'nav_menu' );
      
    // Set posts page
    $blog_page_id  = get_page_by_title( 'Blog' );  
    update_option( 'show_on_front', 'page' );
    update_option( 'page_for_posts', $blog_page_id->ID );

    // Set demo homepage & primary menu
    switch ( $selected_import['import_file_name'] ) {
      case 'Demo - SASS':
        $front_page_id = get_page_by_title( 'Home Page - SASS' );        
        $primary_menu = get_term_by( 'name', 'Adri - Primary Menu', 'nav_menu' );
      break;
      
      case 'Demo - Royal':
        $front_page_id = get_page_by_title( 'Home Page - Royal' );
        $primary_menu = get_term_by( 'name', 'Adri - Primary Menu', 'nav_menu' );
      break;
      
      case 'Demo - Mint':
        $front_page_id = get_page_by_title( 'Home Page - Mint' );
        $primary_menu = get_term_by( 'name', 'Adri - Primary Menu', 'nav_menu' );
      break;

      case 'Demo - Energy':
        $front_page_id = get_page_by_title( 'Home Page - Energy' );
        $primary_menu = get_term_by( 'name', 'Adri - Primary Menu', 'nav_menu' );
      break;

      case 'Demo - Mono':
        $front_page_id = get_page_by_title( 'Home Page - Mono' );
        $primary_menu = get_term_by( 'name', 'Adri - Primary Menu', 'nav_menu' );
      break;

      case 'Demo - DarkMode':
        $front_page_id = get_page_by_title( 'Home Page - Dark Mode (One Page)' );
        $primary_menu = get_term_by( 'name', 'One Page - Primary Menu', 'nav_menu' );
      break;

      case 'Demo - Smooth':
        $front_page_id = get_page_by_title( 'Home Page - Smooth (One Page)' );
        $primary_menu = get_term_by( 'name', 'One Page - Primary Menu', 'nav_menu' );
      break;
      
      default:
        $front_page_id = get_page_by_title( 'Home page - Impact' );
        $primary_menu = get_term_by( 'name', 'Adri - Primary Menu', 'nav_menu' );
      break;
    }
    update_option( 'page_on_front', $front_page_id->ID );
    
    set_theme_mod( 'nav_menu_locations', array(
      'primary-menu' => $primary_menu->term_id,
      'secondary-menu-left' => $secondary_left->term_id,
      'secondary-menu-right' => $secondary_right->term_id,
      'footer-menu' => $footer_menu->term_id,
      )
    );

    // Import all the sliders
    if ( class_exists( 'RevSlider') ) {
      $slider_array = array(
        get_template_directory() . "/includes/demo/sliders/cover-dark.zip",
        get_template_directory() . "/includes/demo/sliders/cover-energy.zip",
        get_template_directory() . "/includes/demo/sliders/cover-mint.zip",
        get_template_directory() . "/includes/demo/sliders/cover-video-smooth.zip",
        get_template_directory() . "/includes/demo/sliders/slider-impact.zip",
        get_template_directory() . "/includes/demo/sliders/slider-royal.zip",
        get_template_directory() . "/includes/demo/sliders/slider-sass.zip",
      );

      $slider = new RevSlider();

      foreach( $slider_array as $filepath ) {
        $slider->importSliderFromPost( true, true, $filepath );  
      }

      echo ' Sliders imported.';
    }

    // Disables Elementor global defaults for Fonts, Colors and Lightbox
    if ( defined( 'ELEMENTOR_VERSION' ) ) {
      update_option( 'elementor_disable_color_schemes', 'yes' );
      update_option( 'elementor_disable_typography_schemes', 'yes' );
      update_option( 'elementor_global_image_lightbox', '' );

      $kit = \Elementor\Plugin::$instance->kits_manager->get_active_kit();
      $kit->update_settings( [
        'global_image_lightbox' => ''
      ] );
    }

    // Disables the plugin branding notice
    add_filter( 'pt-ocdi/disable_pt_branding', '__return_true' );
  }
}
add_action( 'pt-ocdi/after_import', 'adri_ajdethemes_ocdi_after_import_setup' );