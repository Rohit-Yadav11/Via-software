<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class AdriSliderRecentPosts_Widget extends WP_Widget {
 
    function __construct() {
 
        parent::__construct(
            'recent_posts_slider',  // Base ID
            'Adri Recent Posts Slider'   // Name
        );
 
        add_action( 'widgets_init', function() {
            register_widget( 'AdriSliderRecentPosts_Widget' );
        });
 
    }
 
    public $args = array(
        'before_widget' => '<div class="widget-wrap">',
        'after_widget'  => '</div></div>'
    );
 
    public function widget( $args, $instance ) {

        global $post;

        $default_title = esc_html__( 'Recent posts', 'adri-ajdethemes-elements' );
		$title         = ! empty( $instance['title'] ) ? $instance['title'] : $default_title;

        $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

        $recent_posts = wp_get_recent_posts( array(
            'numberposts' => 3,
            'post_status' => 'publish',
            'post__not_in'=> array( $post->ID )
        ) );

        echo $args['before_widget'];
        
        if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

        echo '<div class="slider-recent-posts swiper-container"><div class="swiper-wrapper">';

        foreach($recent_posts as $rpost) {
            printf( wp_kses_post( '
                <div class="swiper-slide">
        
                    <article class="slide-recent-post" style="background-image: url(%1$s);">
                        <a href="%2$s">
                            <div class="post-content">
                                <span class="post-date">' . __( '%3$s', 'adri-ajdethemes' ) . '</span>
                                <h4 class="post-title">' . __( '%4$s', 'adri-ajdethemes' ) . '</h4>
                            </div>
                        </a>
                    </article>
        
                </div>' ),
        
                get_the_post_thumbnail_url($rpost['ID'], 'full'),   // 1
                get_permalink($rpost['ID']),                        // 2
                get_the_time(get_option('date_format')),            // 3
                $rpost['post_title']                                // 4
        );
        } 
        
        wp_reset_query();

        echo '</div>';
        
        echo '<div class="sl-nav">';
        echo '<button class="swiper-nav-arrow swiper-button-prev"><i class="icon-Arrow-OutLeft"></i> prev</button>';
        echo '<span></span>';
        echo '<button class="swiper-nav-arrow swiper-button-next">next <i class="icon-Arrow-OutRight"></i></button>';
        echo '</div>';

        echo '</div>';

        echo $args['after_widget'];
    }
 
    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html( '', 'adri-ajdethemes-elements' );

        ?>
        <p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
 
        $instance = array();

        $instance['title'] = ( !empty( $new_instance['title'] ) ) ? $new_instance['title'] : '';
 
        return $instance;
    }
}
$my_widget = new AdriSliderRecentPosts_Widget();