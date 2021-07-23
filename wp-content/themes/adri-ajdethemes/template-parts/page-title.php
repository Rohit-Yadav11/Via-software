<?php
/**
 * The template for displaying the page title.
 *
 * @package AdriAjdethemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Page title has bg. image?
if ( get_theme_mod( 'page_title_bg_img' ) ) {
    $pt_has_bg_img = ' pt-has-bg-img ';
} else {
    $pt_has_bg_img = '';
}

// Has section title instead of page title.
if ( get_theme_mod( 'page_title_layout', 'st-as-pt' ) === 'st-as-pt' ) {
    $pt_st = 'section-title';
} else {
    $pt_st = 'page-title';
}

?>

<div class="<?php printf( esc_attr( '%s %s %s %s' ), $pt_st, get_theme_mod( 'page_title_txt_align', 'text-center' ), get_theme_mod( 'page_title_layout', 'st-as-pt' ), $pt_has_bg_img ); ?>">

    <div class="container">
        <div class="row">
            <div class="col-lg-12">

                <?php if ( strlen( get_theme_mod( 'page_title_subtitle', 'Our blog' ) ) > 0 && ( ! is_page() ) ) : ?>
                    <span class="st-subtitle">
                        <?php printf( esc_html__( '%s', 'adri-ajdethemes' ), get_theme_mod( 'page_title_subtitle', 'Our blog' ) ); ?>
                    </span>
                <?php endif; ?>

                <h1 class="entry-title">
                    <?php 
                        if ( is_archive() ) {
                            printf( esc_html__( '%s', 'adri-ajdethemes' ), the_archive_title() );
                        } elseif( is_search() ) {
                            esc_html_e( 'Search results for: ', 'adri-ajdethemes' );
                            printf( '<span>' .  esc_html__( '%s', 'adri-ajdethemes' ) , get_search_query() . '</span>' );
                        } elseif ( is_page() ) {
                            printf( esc_html__( '%s', 'adri-ajdethemes' ), get_the_title() );
                        } else {
                            printf( esc_html__( '%s', 'adri-ajdethemes' ), get_theme_mod( 'page_title_title', 'Latest news from our blog' ) );
                        }
                    ?>
                </h1>

            </div><!-- end of .col-lg-12 -->
        </div><!-- end of .row -->
    </div><!-- end of .container -->

</div><!-- end of .page-title -->