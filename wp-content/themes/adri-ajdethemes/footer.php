<?php
/**
 * The template for displaying the footer
 *
 * @package AdriAjdethemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


if ( get_theme_mod( 'footer_has_reveal' ) ) {
	echo '</div><!-- end of .main-wrapper -->';
}


if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) {
	get_template_part( 'template-parts/footer' );
}

wp_footer();
?>

</body>
</html>