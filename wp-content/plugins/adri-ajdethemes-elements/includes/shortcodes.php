<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Social Icons List - Wrapper
 *
 * @since 1.0.0
 */

function adri_ajdethemes_elements_social_links_list_wrapper_shortcode( $atts, $content = null ) {

    extract(
        shortcode_atts(
            array(
                'css_class' => ''
            ), 
            $atts
        )
    );

    if ( strlen( $css_class ) > 0 ) {
        $css_class = 'class="'. $css_class .'"';
    }

    $output = '
        <ul class="social-icons ' . $css_class . '">
            ' . do_shortcode( $content ) . '
        </ul>';
         
    return $output;
}
add_shortcode( 'slinks', 'adri_ajdethemes_elements_social_links_list_wrapper_shortcode' );


/**
 * Social Icons List - Item
 *
 * @since 1.0.0
 */

function adri_ajdethemes_elements_social_icon_link_shortcode( $atts, $content = null ) {

    extract(
        shortcode_atts(
            array(
                'css_class'     => '',
                'sc_link'       => '#',
                'sc_icon'       => 'fab fa-twitter',
            ), 
            $atts
        )
    );

    $output =  "<li>
                    <a href='{$sc_link}'>
                        <i class='{$sc_icon}'></i>
                    </a>
                </li>";
         
    return $output;
}
add_shortcode( 'sicon', 'adri_ajdethemes_elements_social_icon_link_shortcode' );


/**
 * Widget Accordion - Wrapper
 *
 * @since 1.0.0
 */

function adri_ajdethemes_elements_accordion_wrapper_shortcode( $atts, $content = null ) {

    extract(
        shortcode_atts(
            array(
                'css_class' => ''
            ), 
            $atts
        )
    );

    if ( strlen( $css_class ) > 0 ) {
        $css_class = esc_attr( $css_class );
    }

    $output = '
        <div class="widget-accordion ' . esc_attr( $css_class ) . '">
            ' . do_shortcode( $content ) . '
        </div>';
         
    return $output;
}
add_shortcode( 'adri-accordion', 'adri_ajdethemes_elements_accordion_wrapper_shortcode' );


/**
 * Widget Accordion - Item
 *
 * @since 1.0.0
 */

function adri_ajdethemes_elements_accordion_item_shortcode( $atts, $content = null ) {

    extract(
        shortcode_atts(
            array(
                'css_class' => '',
                'title'     => 'New York, US',
                'active'    => '',
            ), 
            $atts
        )
    );

    if ( strlen( $css_class ) > 0 ) {
        $css_class = esc_attr( $css_class );
    }

    $id_nbr = rand(0,1000) + rand(0, 10);

    if ( strlen( $active ) > 0 ) {
        $active = 'checked';
    }

    $output = '
        <div class="w-acc-item ' . $css_class . '">
            <input type="radio" name="accordion-item" id="wacc_' . $id_nbr . '" ' . $active . '>
            <label for="wacc_' . $id_nbr . '"><span>' . $title . '</span></label>

            <div class="w-acc-content">' . do_shortcode( $content ) . '</div>
        </div>';
         
    return $output;
}
add_shortcode( 'adri-accordion-item', 'adri_ajdethemes_elements_accordion_item_shortcode' );