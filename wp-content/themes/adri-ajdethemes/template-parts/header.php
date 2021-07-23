<?php
/**
 * The template for displaying header.
 *
 * @package AdriAjdethemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


get_theme_mod( 'nav_grid' ) === 'fw' ? $nav_is_fw = 'nav-fw' : $nav_is_fw = '';
get_theme_mod( 'nav_is_sticky' ) ? $nav_is_sticky = 'nav-sticky' : $nav_is_sticky = '';
get_theme_mod( 'nav_site_trans_logo' ) ? $nav_trans_logo = 'nav-has-trans-logo' : $nav_trans_logo = '';

$nav_is_trans = '';
if ( get_theme_mod( 'nav_is_trans_only_home' ) ) {
    if ( is_front_page() || ( is_single() && has_post_thumbnail() && ( class_exists( 'WooCommerce' ) && ! is_product() ) ) ) {
        get_theme_mod( 'nav_is_trans' ) ? $nav_is_trans = 'nav-trans' : $nav_is_trans = '';
    }
} elseif ( get_theme_mod( 'nav_is_trans' ) ) {
    $nav_is_trans = 'nav-trans';
}


/**
 * Navbar Grid Wrapper
 *
 * @return void
 */
if ( ! function_exists( 'adri_ajdethemes_has_nav_grid' ) ) {
	function adri_ajdethemes_has_nav_grid() {

        if ( get_theme_mod( 'nav_grid', 'grid' ) === 'grid' ) : ?>

            <div class="container">
                <div class="row">
                    <div class="col-lg-12">

        <?php 
        endif;

	}
}


/**
 * Closing Navbar Grid Wrapper
 *
 * @return void
 */
if ( ! function_exists( 'adri_ajdethemes_has_nav_grid_end' ) ) {
	function adri_ajdethemes_has_nav_grid_end() {

        if ( get_theme_mod( 'nav_grid', 'grid' ) === 'grid' ) : ?>

                    </div><!-- end of .col-lg-12 -->
                </div><!-- end of .row -->
            </div><!-- end of .container -->

        <?php 
        endif;

	}
}


/**
 * Logo for Transparent Bg
 *
 * @return void
 */
if ( ! function_exists( 'adri_ajdethemes_nav_brand_trans_logo' ) ) {
    function adri_ajdethemes_nav_brand_trans_logo() {

        $logo_trans = '';

        if ( get_theme_mod( 'nav_site_trans_logo' ) ) {
    
            $logo_trans = sprintf( '<img src="%1$s" alt="%2$s" class="logo-trans">', 
                esc_url( get_theme_mod( 'nav_site_trans_logo' ) ), 
                esc_attr( get_bloginfo( 'name' ) ) 
            );
    
        }

        return $logo_trans;
    }
}


/**
 * Navbar Brand (Logo & Mobile Toggle)
 *
 * @return void
 */
if ( ! function_exists( 'adri_ajdethemes_nav_brand' ) ) {
	function adri_ajdethemes_nav_brand() { ?>
        
        <div class="brand">
            <?php 
            if ( get_theme_mod( 'nav_site_logo' ) ) {

                printf( 
                    '<a href="%1$s" title="%3$s" rel="home">
                        <img src="%2$s" alt="%3$s" class="logo">
                        %4$s
                    </a>', 

                    // 1.
                    esc_url( home_url( '/' ) ),
                    // 2.
                    esc_url( get_theme_mod( 'nav_site_logo' ) ), 
                    // 3.
                    esc_attr( get_bloginfo( 'name' ) ),
                    // 4.
                    adri_ajdethemes_nav_brand_trans_logo()
                );

            } else {

                printf( '
                    <div class="brand-site-title-content">
                        <h1 class="site-title">
                            <a href="%1$s" title="%3$s" rel="home">%2$s</a>
                        </h1>
                        <span>%4$s</span>
                    </div>',

                    esc_url( home_url( '/' ) ),                 // 1
                    esc_html( get_bloginfo( 'name' ) ),         // 2
                    esc_attr__( 'Home', 'adri-ajdethemes' ),    // 3
                    esc_html( get_bloginfo( 'description' ) )   // 4
                );

            }

            ?>
            <span class="nav-burger"><span></span></span>
        </div><!-- end of .brand --><?php

	}
}


/**
 * Navbar Utility (Search & Cart)
 *
 * @return void
 */
if ( ! function_exists( 'adri_ajdethemes_nav_utility' ) ) {
	function adri_ajdethemes_nav_utility() { ?>

        <div class="menu-wrapper">
            <div class="menu-items-wrapper"><?php

                if ( get_theme_mod( 'nav_has_search' ) || get_theme_mod( 'nav_has_cart' ) ) {
                    echo '<ul class="menu-utility-items">';

                        if ( get_theme_mod( 'nav_has_search' ) ) {
                            echo '
                                <li>
                                    <button class="nav-search">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </li>';
                        }

                        if ( get_theme_mod( 'nav_has_cart' ) && class_exists( 'WooCommerce' ) ) {
                            echo '<li>';
                            adri_ajdethemes_nav_cart();
                            echo '</li>';
                        }

                    echo '</ul>';
                }

	}
}


/**
 * Nav (mobile) Footer
 *
 * @return void
 */
if ( ! function_exists( 'adri_ajdethemes_nav_footer_mobile' ) ) {
	function adri_ajdethemes_nav_footer_mobile() { 

                if ( get_theme_mod( 'nav_has_social_icons' ) || class_exists('SitePress') ) {
                    echo '<div class="menu-footer-mobile-only">';

                        if ( ( strlen( get_theme_mod( 'social_icons_shortcode' ) ) > 0 ) && ( get_theme_mod( 'nav_has_social_icons' ) === true ) ) {
                            echo do_shortcode( wp_kses_post( get_theme_mod( 'social_icons_shortcode' ) ) );
                        }

                        if ( class_exists('SitePress') ) {

                            echo '<div class="menu-lang-switcher">';
                                echo do_action( wp_kses_post('wpml_add_language_selector') );
                            echo '</div>';
                        }

                    echo '</div><!-- end of .menu-footer-mobile-only -->';
                } ?>

            </div><!-- end of .menu-items-wrapper -->
        </div><!-- end of .menu-wrapper --><?php

	}
} 


/**
 * Secondary Navigation Output
 * 
 * @package AdriAjdethemes
 */

get_template_part( 'template-parts/header-secondary' );


/**
 * Main Navigation Output
 *
 * @see adri_ajdethemes_has_nav_grid()
 * @see adri_ajdethemes_nav_brand()
 * @see adri_ajdethemes_nav_utility()
 * @see adri_ajdethemes_has_nav_grid_end()
 * 
 * @package AdriAjdethemes
 */

printf( 
    '<header class="site-header-nav %1$s %2$s %3$s %4$s %5$s %6$s %7$s %8$s">', 
    esc_attr( $nav_is_trans ),                                          // 1
    esc_attr( $nav_is_sticky ),                                         // 2
    esc_attr( $nav_trans_logo ),                                        // 3
    esc_attr( get_theme_mod( 'nav_style', '' ) ),                       // 4
    esc_attr( get_theme_mod( 'dropdown_style', '' ) ),                  // 5
    esc_attr( get_theme_mod( 'nav_menu_item_accent', '' ) ),            // 6
    esc_attr( get_theme_mod( 'nav_trans_menu_item_accent', '' ) ),      // 7
    esc_attr( get_theme_mod( 'nav_trans_color', 'nav-trans-light' ) )   // 8 
);

    adri_ajdethemes_has_nav_grid();

        printf( '<nav class="nav-main %s">', esc_attr( $nav_is_fw ) );

            adri_ajdethemes_nav_brand();

            if ( has_nav_menu( 'primary-menu' ) ) {

                adri_ajdethemes_nav_utility();

                echo '<div class="main-menu">';

                    wp_nav_menu( array( 'theme_location' => 'primary-menu' ) );

                echo '</div>';

                adri_ajdethemes_nav_footer_mobile();

            }

        echo '</nav><!-- end of .nav-main -->';

    adri_ajdethemes_has_nav_grid_end();
    
    ?>

</header>