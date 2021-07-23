<?php
/**
 * Template part for displaying posts
 *
 * @package AdriAjdethemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( get_theme_mod( 'blog_sidebar', 'sb_right' ) === 'sb_null' || get_theme_mod( 'blog_sidebar', 'sb_right' ) === 'sb_bottom' || ! is_active_sidebar( 'sidebar-blog' ) ) {

    if ( ! ( get_theme_mod( 'blog_layout', 'post-classic' ) === 'post-classic' ) ) {
        $post_col       = 'col-lg-4';
        $all_posts_col  = 'col-lg-12';
    } else {
        $post_col       = 'col-lg-12';
        $all_posts_col  = 'offset-lg-2 col-lg-8';
    }
    
} else {

    if ( ! ( get_theme_mod( 'blog_layout', 'post-classic' ) === 'post-classic' ) ) {
        $post_col       = 'col-lg-6';
        $all_posts_col  = 'col-lg-8';
        $sidebar_col    = 'col-lg-3';
    } else {
        $post_col       = 'col-lg-12';
        $all_posts_col  = 'col-lg-7';
        $sidebar_col    = 'col-lg-4';
    }
}

if ( get_theme_mod( 'blog_sidebar', 'sb_right' ) === 'sb_left' ) {
    $sb_offset_left = 'offset-lg-1';
} else {
    $sb_offset_left = '';
}
?>

<section class="container">
    <div class="row">
        <?php if ( get_theme_mod( 'blog_sidebar', 'sb_right' ) === 'sb_left' && is_active_sidebar( 'sidebar-blog' ) ) : ?>
        <div class="<?php echo esc_attr( $sidebar_col ); ?>">
            <aside class="widgets">
                <?php dynamic_sidebar( 'sidebar-blog' ); ?>
            </aside>
        </div><!-- end of .col-lg-* -->
        <?php endif; ?>

        <div class="<?php printf( '%s %s', esc_attr( $all_posts_col ), esc_attr( $sb_offset_left ) ); ?>">

            <div class="row">

                <?php
                if ( have_posts() ) :

                    while ( have_posts() ) :
                        esc_attr( the_post() );
                        ?>
                        <div class="<?php echo esc_attr( $post_col ); ?>">

                            <?php 
                            switch ( get_theme_mod( 'blog_layout', 'post-classic' ) ) {
                                case 'post-classic' :
                                    get_template_part( 'template-parts/post-classic' );
                                    break;
                                case 'post-card' :
                                    get_template_part( 'template-parts/post-card' );
                                    break;
                                case 'post-min' :
                                    get_template_part( 'template-parts/post-min' );
                                    break;
                                default:
                                    get_template_part( 'template-parts/post-col' );
                            }
                            ?>

                        </div><!-- end of .col-lg-* -->
                <?php 
                endwhile;

                else :
                    
                    get_template_part( 'template-parts/none' );

                endif;
                ?>
            </div><!-- end of .row -->

            <div class="row">
                <div class="col-lg-12">

                    <?php 
                    global $wp_query;
                    if ( $wp_query->max_num_pages > 1 ) {
                        the_posts_pagination( array(
                            'mid_size'  => 2,
                            'prev_text' => wp_kses_post( __( '<span class="icon-Arrow-Left2"><span class="screen-reader-text">previous page</span></span>', 'adri-ajdethemes' ) ),
                            'next_text' => wp_kses_post( __( '<span class="icon-Arrow-Right2"><span class="screen-reader-text">next page</span></span>', 'adri-ajdethemes' ) ),
                        ));
                    } ?>

                </div><!-- end of .col-lg-12 -->
            </div><!-- end of .row -->

        </div><!-- end of .col-lg-* -->

        <?php if ( get_theme_mod( 'blog_sidebar', 'sb_right' ) === 'sb_right' && is_active_sidebar( 'sidebar-blog' ) ) : ?>
        <div class="offset-lg-1 <?php echo esc_attr( $sidebar_col ); ?>">
            <aside class="widgets">
                <?php dynamic_sidebar( 'sidebar-blog' ); ?>
            </aside>
        </div><!-- end of .col-lg-4 -->
        <?php endif; ?>
    </div><!-- end of .row -->
    
    <?php if ( ( get_theme_mod( 'blog_sidebar', 'sb_right' ) === 'sb_bottom' ) && is_active_sidebar( 'sidebar-blog-x' ) ) : ?>
    <div class="row">
        <?php dynamic_sidebar( 'sidebar-blog-x' ); ?>
    </div><!-- end of .row -->
    <?php endif; ?>
</section><!-- end of .container -->