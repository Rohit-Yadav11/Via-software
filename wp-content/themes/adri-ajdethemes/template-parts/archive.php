<?php
/**
 * The template for displaying archive pages.
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

?>

<main class="site-main <?php echo esc_attr( $no_pt_spacing ); ?>">
    <?php if ( apply_filters( 'hello_elementor_page_title', true ) && $has_page_title ) {
        get_template_part( 'template-parts/page-title' );
    }

    get_template_part( 'template-parts/posts' ); ?>
</main>