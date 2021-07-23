<?php
/**
 * The template for displaying footer.
 *
 * @package AdriAjdethemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


get_theme_mod( 'footer_has_reveal' ) ? $ftr_has_revel = 'footer-reveal' : $ftr_has_revel = ''; 
?>


<footer id="site-footer" class="site-footer <?php printf( esc_attr( '%1$s %2$s' ), $ftr_has_revel, get_theme_mod( 'footer_text_color_style' ) ); ?>">
    <div class="footer-content">
        <div class="container">
            
            <?php if ( is_active_sidebar( 'footer-widgets' ) ) : ?>
            <div class="row footer-widgets">
                <?php dynamic_sidebar( 'footer-widgets' ); ?>
            </div><!-- end of .row -->
            <?php endif; ?>

        </div><!-- end of .container -->

        <div class="footer-bottom">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">

                        <div class="footer-bottom-content">

                            <?php 
                            // Footer Menu
                            if ( has_nav_menu( 'footer-menu' ) ) {
                                wp_nav_menu( array( 'theme_location' => 'footer-menu' ) );
                            } 

                            // Footer Copyright
                            if ( strlen( get_theme_mod( 'footer_txt_left', '© 2020 All rights reserved. Theme created by Ajdethemes.' ) ) > 0 ) {
                                printf( 
                                    '<div class="copyright">
                                        <span>' 
                                            . esc_html__( '%s', 'adri-ajdethemes' ) . 
                                        '</span>
                                    </div>', 
                                    get_theme_mod( 'footer_txt_left', '© 2020 All rights reserved.' ) 
                                );
                            }
                            ?>

                        </div><!-- end of .footer-bottom-content -->

                    </div><!-- end of .col-lg-8 -->
                    <div class="col-lg-4">

                        <div class="footer-bottom-social-icons">

                            <?php if ( ( strlen( get_theme_mod( 'social_icons_shortcode' ) ) > 0 ) && get_theme_mod( 'footer_social_icons', true ) ) {
                                echo do_shortcode( wp_kses_post( get_theme_mod( 'social_icons_shortcode' ) ) );
                            } ?>

                        </div><!-- end of .footer-bottom-social-icons -->

                    </div><!-- end of .col-lg-4 -->
                </div><!-- end of .row -->
            </div><!-- end of .container -->
        </div><!-- end of .footer-bottom -->

        <?php if ( get_theme_mod( 'footer_scroll' ) ) {
            printf( 
                '<a href="#" id="ftrBackToTop" class="scroll-top">
                    <span class="content">
                        <i class="icon-Arrow-OutRight"></i>
                        <span>' 
                            . esc_html__( '%1$s', 'adri-ajdethemes' ) . 
                        '</span>
                    </span>
                    <span class="screen-reader-text">' . esc_html__( '%1$s', 'adri-ajdethemes' ) . '</span>
                </a>',
                'scroll top'
            );
        } ?>
    </div><!-- end of .footer-content -->
</footer><!-- end of #site-footer -->

<?php if ( get_theme_mod( 'nav_has_search' ) ) {
    get_template_part( 'template-parts/search-modal' );
} ?>