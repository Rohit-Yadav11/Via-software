<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class FeatureInlineIcon_Widget extends WP_Widget {
 
    function __construct() {
 
        parent::__construct(
            'ft-inline-icon-widget',  // Base ID
            'Adri Feature Inline Icon'   // Name
        );
 
        add_action( 'widgets_init', function() {
            register_widget( 'FeatureInlineIcon_Widget' );
        });
 
    }
 
    public $args = array(
        'before_widget' => '<div class="widget-wrap">',
        'after_widget'  => '</div></div>'
    );
 
    public function widget( $args, $instance ) {
 
        echo $args['before_widget'];

        printf( '<a href="%s" class="ft-inline-icon-link-wrapper">', esc_url( $instance['ft_url_wrapper'] ) );

        echo '<div class="ft-inline-icon">';

        printf( '<span class="ft-icon"><i class="fas fa-%s"></i></span>', esc_html( $instance['ft_icon_name'] ) );
        
        echo '<div class="ft-content">';
        
        printf( '<h5>%s</h5>', esc_html__( $instance['ft_title'], 'adri-ajdethemes-elements' ) );
        
		printf( '<p>%s</p>', esc_html__( $instance['ft_desc'], 'adri-ajdethemes-elements' ) );

        echo '</div></div></a>';
        
        echo $args['after_widget'];
    }
 
    public function form( $instance ) {

        $ft_url_wrapper = ! empty( $instance['ft_url_wrapper'] ) ? $instance['ft_url_wrapper'] : esc_url( '' );
        $ft_icon_name = ! empty( $instance['ft_icon_name'] ) ? $instance['ft_icon_name'] : esc_html( '' );
        $ft_title = ! empty( $instance['ft_title'] ) ? $instance['ft_title'] : esc_html( '', 'adri-ajdethemes-elements' );
        $ft_desc = ! empty( $instance['ft_desc'] ) ? $instance['ft_desc'] : esc_html( '', 'adri-ajdethemes-elements' );
        ?>

        <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'ft_title' ) ); ?>"><?php echo esc_html__( 'Feature Title:', 'adri-ajdethemes-elements' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'ft_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'ft_title' ) ); ?>" type="text" value="<?php echo esc_attr( $ft_title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'Text' ) ); ?>"><?php echo esc_html__( 'Description:', 'adri-ajdethemes-elements' ); ?></label>
            <textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'ft_desc' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'ft_desc' ) ); ?>" type="text" cols="30" rows="10"><?php echo esc_attr( $ft_desc ); ?></textarea>
        </p>
        <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'ft_icon_name' ) ); ?>"><?php echo esc_html__( 'Icon Name (fontawesome):', 'adri-ajdethemes-elements' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'ft_icon_name' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'ft_icon_name' ) ); ?>" type="text" value="<?php echo esc_attr( $ft_icon_name ); ?>">
        </p>
        <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'ft_url_wrapper' ) ); ?>"><?php echo esc_html__( 'Link/URL:', 'adri-ajdethemes-elements' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'ft_url_wrapper' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'ft_url_wrapper' ) ); ?>" type="text" value="<?php echo esc_attr( $ft_url_wrapper ); ?>">
        </p>
        <?php
 
    }
 
    public function update( $new_instance, $old_instance ) {
 
        $instance = array();
 
        $instance['ft_url_wrapper'] = ( !empty( $new_instance['ft_url_wrapper'] ) ) ? $new_instance['ft_url_wrapper'] : '';
        $instance['ft_icon_name'] = ( !empty( $new_instance['ft_icon_name'] ) ) ? $new_instance['ft_icon_name'] : '';
        $instance['ft_title'] = ( !empty( $new_instance['ft_title'] ) ) ? $new_instance['ft_title'] : '';
        $instance['ft_desc'] = ( !empty( $new_instance['ft_desc'] ) ) ? $new_instance['ft_desc'] : '';
 
        return $instance;
    }
 
}
$my_widget = new FeatureInlineIcon_Widget();