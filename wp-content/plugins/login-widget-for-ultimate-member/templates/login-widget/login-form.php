<?php echo do_shortcode( $params['before_form'] ); ?>
<?php
if ( empty( $params['form_type'] ) || 'default' == $params['form_type'] ) {
	$args = array();
	if ( ! empty( $params['hide_remember_me'] ) ) {
		$args['remember'] = false;
	}
	wp_login_form( $args );
} else {
	echo do_shortcode( '[ultimatemember form_id=' . absint( $params['form_type'] ) . ']' );
}
?>
<?php echo do_shortcode( $params['after_form'] ); ?>
