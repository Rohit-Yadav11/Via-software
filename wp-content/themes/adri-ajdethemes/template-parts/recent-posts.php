<?php
/**
 * The template part for displaying the Recent Posts.
 *
 * @package AdriAjdethemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


?>
<div class="col-lg-12">
    <h2 class="recent-posts-title"><?php esc_html_e( 'Recent posts', 'adri-ajdethemes' ) ?></h2>
</div>

<?php $recent_posts = wp_get_recent_posts( array(
    'numberposts' => 3,
    'post_status' => 'publish',
    'post__not_in'=> array( $post->ID )
) );

foreach($recent_posts as $rpost) {
    printf( wp_kses_post( '
        <div class="col-lg-4">

            <article class="post-card" style="background-image: url(%1$s);">
                <a href="%2$s">
                    <span class="post-date">' . __( '%3$s', 'adri-ajdethemes' ) . '</span>
                    <h4 class="post-title">' . __( '%4$s', 'adri-ajdethemes' ) . '</h4>
                    <span class="post-cta">
                        <span>' . __( '%5$s', 'adri-ajdethemes' ) . '</span>
                    </span>
                </a>
            </article>

        </div>' ),

        get_the_post_thumbnail_url($rpost['ID'], 'full'),   // 1
        get_permalink($rpost['ID']),                        // 2
        get_the_time(get_option('date_format')),            // 3
        $rpost['post_title'],                               // 4
        get_theme_mod( 'blog_more_btn_text', 'Read more' )  // 5
    );
} 

wp_reset_query();
