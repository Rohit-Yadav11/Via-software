<?php
/**
 * Inline CSS outputted by the Customizer
 *
 * @package AdriAjdethemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'adri_ajdethemes_customizer_css' ) ) {

    function adri_ajdethemes_customizer_css() {
        ob_start();

        // Page Title - Customizer CSS
        $pt_custom_css = null;
        if ( get_theme_mod( 'page_title_layout' ) === 'pt-lg' || get_theme_mod( 'page_title_layout' ) === 'pt-md' || get_theme_mod( 'page_title_layout' ) === 'pt-sm' ) {
            
            $pt_txt_color = 'color:' . get_theme_mod( 'page_title_txt_color', '#222222' ) . ';';
            $pt_subtitle_txt_color = get_theme_mod( 'page_subtitle_txt_color', '#ffd400' );
            $pt_bg_overlay = null;
            $pt_bg = null;

            if ( get_theme_mod( 'page_title_bg_img' ) ) {

                $pt_bg = 'background: url(' . get_theme_mod( 'page_title_bg_img' ) . ');';

                $pt_bg_overlay = '.page-title.pt-has-bg-img:before {
                    background: ' . get_theme_mod( 'page_title_bg_overlay', 'rgba(244,244,244, 0.9)' ) . ';
                }';

            } elseif ( get_theme_mod( 'page_title_bg' ) ) {

                $pt_bg = 'background: ' . get_theme_mod( 'page_title_bg', '#f4f4f4' ) . ';';

            }

            $pt_subtitle_style = '';
            if ( strlen( get_theme_mod( 'page_subtitle_txt_color' ) ) > 0 ) {
                $pt_subtitle_style = "
                .page-title .st-subtitle {
                    color: $pt_subtitle_txt_color;
                }
                ";
            }

            $pt_custom_css = "
                .page-title {
                    $pt_bg
                    $pt_txt_color
                }
                $pt_subtitle_style
                $pt_bg_overlay
            ";
        }
        
        echo esc_attr( $pt_custom_css );


        // Footer Custom Height (reveal effect)
        if ( get_theme_mod( 'footer_has_reveal' ) && ( strlen( get_theme_mod( 'footer_height' ) ) > 0 ) ) {

            $footer_height_custom_css = "
                @media (min-width: 1200px) {
                    .footer-has-reveal footer.site-footer.footer-reveal {
                        min-height: " . get_theme_mod( 'footer_height' ) . "px;
                    }
                }
            ";

            echo esc_attr( $footer_height_custom_css );
        }
    
        return ob_get_clean();
    }
}