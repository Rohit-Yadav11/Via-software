<?php
/**
 * The template for displaying singular post-types: posts, pages and user-defined custom post types.
 *
 * @package AdriAjdethemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

while ( have_posts() ) : the_post();
?>

<main class="site-single-post">
    <article <?php post_class( 'post-single' ); ?>>
        <?php 
        // Featured Image
        if ( has_post_thumbnail() ) : ?>
        <div class="post-single-ft-img">
            <?php 
            the_post_thumbnail( 'full', [
                'alt' => get_the_title(),
                'class' => 'rellax',
                'data-rellax-speed' => '2'
            ] ); ?>
        </div>
        <?php 
        endif; ?>

        <div class="container">
            <div class="row">
                <div class="offset-lg-2 col-lg-8">

                    <header class="page-header">
                        <?php
                        // Categories
                        $categories_list = get_the_category_list( esc_html__( ', ', 'adri-ajdethemes' ) );
                        if ( $categories_list && get_theme_mod( 'blog_cat', true ) ) {
                            printf( '<div class="post-cat">' .  esc_html__( '%s', 'adri-ajdethemes' ) . '</div>', $categories_list );
                        }

                        // Title
                        wp_kses_post( the_title( '<h1 class="entry-title">', '</h1>' ) ); ?>

                        <div class="post-meta">
                            <?php 
                            // Author
                            if ( get_theme_mod( 'blog_author', true ) ) {
                                printf( '
                                    <div class="post-author">
                                        ' . esc_attr__( '%1$s', 'adri-ajdethemes' ) . '
                                        <div class="author-content">
                                            <span>' . esc_html__( '%3$s', 'adri-ajdethemes' ) . '</span>
                                            <span class="author-name">' . esc_html__( '%2$s', 'adri-ajdethemes' ) . '</span>
                                        </div>
                                    </div>', 

                                    get_avatar( get_the_author_meta( 'ID' ) ),  // 1
                                    get_the_author_link(),                      // 2
                                    'Author'                                    // 3
                                );
                            }

                            // Date
                            if ( get_theme_mod( 'blog_date', true ) ) {
                                printf( '
                                    <div class="post-date">
                                        <span>' . esc_html__( '%2$s', 'adri-ajdethemes' ) . '</span>
                                        ' . esc_html__( '%1$s', 'adri-ajdethemes' ) . '
                                    </div>', 

                                    get_the_date(), // 1
                                    'Published'     // 2
                                );
                            }
                            ?>

                            <?php 
                            // Comments
                            if ( get_theme_mod( 'blog_comments', true ) && ( comments_open() && get_comments_number() > 0 ) ) : ?>
                                <div class="post-comments">
                                    <?php comments_popup_link( 
                                        '', 
                                        '<p><span class="comment-nbr">1</span> ' . esc_html__( 'Comment', 'adri-ajdethemes' ) . '</p>
                                        <p>' . esc_html__( 'See the comment', 'adri-ajdethemes' ) . '</p>', 
                                        '<p><span class="comment-nbr">%</span> ' . esc_html__( 'Comments', 'adri-ajdethemes' ) . '</p>
                                        <p>' . esc_html__( 'See all comments', 'adri-ajdethemes' ) . '</p>',  
                                        ''
                                    ); ?>
                                </div>
                            <?php 
                            endif; ?>
                        </div>
                    </header>

                    <div class="page-content">
                        <?php wp_kses_post( the_content() ); ?>
                    </div><!-- end of .page-content -->

                    <footer class="post-footer">
                        <?php 
                        // Tags
                        $tags_list = get_the_tag_list( '', esc_html_x( '', 'list item separator', 'adri-ajdethemes' ) );
                        if ( $tags_list ) : ?>
                            <div class="tagcloud">
                                <?php printf( esc_html__( 'Tagged with: %1$s', 'adri-ajdethemes' ), $tags_list ); ?>
                            </div>
                        <?php 
                        endif; 
                        
                        // Post Pages
                        wp_link_pages();
                        ?>
                    </footer>

                </div><!-- end of .col-ld-col-lg-8 -->
            </div><!-- end of .row -->
        </div><!-- end of .container -->
    </article>

    <div class="container">
        <?php 
        // Prev/Next Post
        if ( get_theme_mod( 'blog_post_nav' ) ) {
            get_template_part( 'template-parts/post-nav' );
        }

        // Author Bio
        if ( get_the_author_meta( 'description' ) && get_theme_mod( 'blog_author', true ) ) {
            get_template_part( 'template-parts/bio' );
        } ?>

        <?php 
        // Recent Posts
        if ( get_theme_mod( 'blog_recent_posts' ) ) : ?>

            <div class="row recent-posts">
                <?php get_template_part( 'template-parts/recent-posts' ); ?>
            </div><!-- end of .row -->

        <?php endif; ?>
    </div><!-- end of .container -->

    <?php 
    // Comments
    if ( comments_open() || get_comments_number() ) : ?>
    <div class="blog-post-comments">

        <div class="container">
            <div class="row">
                <div class="col-lg-12">
        
                    <?php comments_template(); ?>
        
                </div><!-- end of .col-lg-12 -->
            </div><!-- end of .row -->
        </div><!-- end of .container -->

    </div><!-- end of .blog-post-comments -->
    <?php 
    endif; ?>
</main><!-- end of .site-single-post -->

<?php
endwhile;