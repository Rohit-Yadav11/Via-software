<?php
/**
 * The default template for pages
 *
 * @package AdriAjdethemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Has page title?
if ( get_theme_mod( 'page_title_layout', 'st-as-pt' ) !== 'pt_disable' ) {
    $has_page_title = true;
    $no_pt_spacing = '';
} else {
    $has_page_title = false;
    $no_pt_spacing = ' no-pt-spacing ';
}

$cart_is_empty = class_exists( 'WooCommerce' ) && is_cart() && WC()->cart->get_cart_contents_count() == 0;

get_header();

if ( apply_filters( 'hello_elementor_page_title', true ) && $has_page_title && ! adri_ajdethemes_is_woocommerce() ) {
    get_template_part( 'template-parts/page-title' );
}

$post_class = $no_pt_spacing . 'site-page-default';
?>


<?php if ( ! adri_ajdethemes_is_woocommerce() ) : ?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
<?php endif; ?>
        
            <main <?php post_class( $post_class ); ?> >
                
                <?php
                if ( $cart_is_empty ) adri_ajdethemes_cart_empty_wrapper();

                // Page loop
                while ( have_posts() )  {
                    the_post();
        
                    the_content();
                    
                    wp_link_pages(
                        array(
                            'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'adri-ajdethemes' ),
                            'after'  => '</div>',
                        )
                    );
        
                    // If comments are open or we have at least one comment, load up the comment template.
                    if ( comments_open() || get_comments_number() ) comments_template();
                }

                if ( $cart_is_empty ) adri_ajdethemes_cart_empty_wrapper_end();
                ?>
                        
            </main><!-- end of .site-page-default -->

<?php if ( ! adri_ajdethemes_is_woocommerce() ) : ?>
        </div><!-- end of .col-lg-12 -->
    </div><!-- end of .row -->
</div><!-- end of .container -->
<?php 
endif;

get_footer(); ?>