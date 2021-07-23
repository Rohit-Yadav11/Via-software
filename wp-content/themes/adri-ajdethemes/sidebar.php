<?php
/**
 * The sidebar containing the main widget area
 *
 * @package AdriAjdethemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Sidebar Blog
if ( adri_ajdethemes_is_blog() && is_active_sidebar( 'sidebar-blog' ) ) : ?>
	<aside class="widgets">

		<?php dynamic_sidebar( 'sidebar-blog' ); ?>
		
    </aside><!-- end of .widgets -->
<?php endif;


// Sidebar Shop
if ( class_exists( 'WooCommerce' ) ) :
	if ( is_shop() && is_active_sidebar( 'sidebar-shop-x' ) ) : ?>
		<div class="container">
			<div class="row">
				
				<?php dynamic_sidebar( 'sidebar-shop-x' ); ?>

			</div><!-- end of .row -->
		</div><!-- end of .container -->
	<?php endif;
endif;