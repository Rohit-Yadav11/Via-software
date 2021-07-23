<?php
/**
 * WooCommerce Compatibility File
 *
 * @link https://woocommerce.com/
 *
 * @package AdriAjdethemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * WooCommerce setup function.
 *
 * @return void
 */
function adri_ajdethemes_woocommerce_setup() {
	add_theme_support( 'woocommerce', array(
			'thumbnail_image_width' => 380,
			'single_image_width'    => 600,
			'product_grid'          => array(
				'default_rows'    => 3,
				'min_rows'        => 1,
				'default_columns' => 3,
				'min_columns'     => 2,
				'max_columns'     => 4,
			),
		)
	);
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'adri_ajdethemes_woocommerce_setup' );


/**
 * WooCommerce specific scripts & stylesheets.
 *
 * @return void
 */
function adri_ajdethemes_woocommerce_scripts() {
	wp_enqueue_style( 'adri-ajdethemes-woocommerce-style', get_template_directory_uri() . '/woocommerce.css', array(), ADRI_AJDETHEMES_VERSION );
}
add_action( 'wp_enqueue_scripts', 'adri_ajdethemes_woocommerce_scripts' );


/**
 * Disable the default WooCommerce stylesheet.
 *
 */
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );


/**
 * Related Products - Filter
 *
 * @param array $args related products args.
 * @return array $args related products args.
 */
function adri_ajdethemes_woocommerce_related_products_args( $args ) {
	$defaults = array(
		'posts_per_page' => 4,
		'columns'        => 4,
	);

	$args = wp_parse_args( $defaults, $args );

	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'adri_ajdethemes_woocommerce_related_products_args' );


/**
 * Shop Page Title - Filter
 *
 * @param array $args related products args.
 * @return array $args related products args.
 */
function adri_ajdethemes_woocommerce_page_title( $page_title ) { 

	if ( is_cart() || is_checkout() || is_account_page() ) {

		the_title( '<h1>', '</h1>' );

	} else {

		return $page_title;

	}
}
add_filter( 'woocommerce_page_title', 'adri_ajdethemes_woocommerce_page_title', 10 );


/**
 * Navbar Cart Menu Item
 *
 * @return void
 */
if ( ! function_exists( 'adri_ajdethemes_nav_cart' ) ) {
	function adri_ajdethemes_nav_cart() {
		if ( class_exists( 'WooCommerce' ) ) { ?>

		<ul id="site-header-cart" class="site-header-cart menu">
			<li>
				<a class="cart-contents" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'adri-ajdethemes' ); ?>">
					<?php /* translators: %d: number of items in cart */ ?>
					<?php echo wp_kses_post( WC()->cart->get_cart_subtotal() ); ?> <span class="count"><?php echo wp_kses_data( sprintf( _n( '%d item', '%d items', WC()->cart->get_cart_contents_count(), 'adri-ajdethemes' ), WC()->cart->get_cart_contents_count() ) ); ?></span>
				</a>

				<div class="nav-cart-items">
					<?php the_widget( 'WC_Widget_Cart', 'title=' ); ?>
				</div>
			</li>
		</ul>
		
		<?php
		}
	}
}


/**
 * Main WooCommerce wrapper
 * 
 * @see adri_ajdethemes_woocommerce_wrapper()
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
add_action( 'woocommerce_before_main_content', 'adri_ajdethemes_woocommerce_wrapper' );
add_action( 'woocommerce_after_main_content', 'adri_ajdethemes_woocommerce_wrapper_end' );


/**
 * Cart Page Layout
 *
 * @see adri_ajdethemes_page_title_wrapper()
 * @see adri_ajdethemes_container()
 * @see adri_ajdethemes_col_lg_12()
 * @see adri_ajdethemes_woocommerce_page_title()
 */

// cart - page title
remove_action( 'woocommerce_before_cart', 'woocommerce_output_all_notices', 10 );
add_action( 'woocommerce_before_cart', 'adri_ajdethemes_page_title_wrapper', 11 );
add_action( 'woocommerce_before_cart', 'adri_ajdethemes_woocommerce_page_title', 12 );
add_action( 'woocommerce_before_cart', 'woocommerce_breadcrumb', 13 );
add_action( 'woocommerce_before_cart', 'adri_ajdethemes_page_title_wrapper_end', 14 );
// notice grid wrapper
add_action( 'woocommerce_before_cart', 'adri_ajdethemes_container', 15 );
add_action( 'woocommerce_before_cart', 'adri_ajdethemes_col_lg_12', 16 );
add_action( 'woocommerce_before_cart', 'woocommerce_output_all_notices', 17 );
add_action( 'woocommerce_before_cart', 'adri_ajdethemes_col_lg_12_end', 18 );
add_action( 'woocommerce_before_cart', 'adri_ajdethemes_container_end', 19 );
// cart content
add_action( 'woocommerce_before_cart', 'adri_ajdethemes_container', 20 );
add_action( 'woocommerce_before_cart', 'adri_ajdethemes_col_lg_12', 21 );
add_action( 'woocommerce_after_cart', 'adri_ajdethemes_col_lg_12_end', 10 );
add_action( 'woocommerce_after_cart', 'adri_ajdethemes_container_end', 11 );


/**
 * Checkout Page Layout
 *
 * @see adri_ajdethemes_page_title_wrapper()
 * @see adri_ajdethemes_container()
 * @see adri_ajdethemes_col_lg_12()
 * @see adri_ajdethemes_woocommerce_page_title()
 */

//  checkout - page title
add_action( 'woocommerce_before_checkout_form', 'adri_ajdethemes_page_title_wrapper', 4 );
add_action( 'woocommerce_before_checkout_form', 'adri_ajdethemes_woocommerce_page_title', 5 );
add_action( 'woocommerce_before_checkout_form', 'woocommerce_breadcrumb', 6 );
add_action( 'woocommerce_before_checkout_form', 'adri_ajdethemes_page_title_wrapper_end', 7 );
// checkout content
add_action( 'woocommerce_before_checkout_form', 'adri_ajdethemes_container', 8 );
add_action( 'woocommerce_before_checkout_form', 'adri_ajdethemes_col_lg_12', 9 );
add_action( 'woocommerce_after_checkout_form', 'adri_ajdethemes_col_lg_12_end', 10 );
add_action( 'woocommerce_after_checkout_form', 'adri_ajdethemes_container_end', 11 );


/**
 * Account Pages Layout
 *
 * @see adri_ajdethemes_page_title_wrapper()
 * @see adri_ajdethemes_container()
 * @see adri_ajdethemes_col_lg_12()
 * @see adri_ajdethemes_woocommerce_page_title()
 */

// account - page title
add_action( 'woocommerce_account_navigation', 'adri_ajdethemes_page_title_wrapper', 4 );
add_action( 'woocommerce_account_navigation', 'adri_ajdethemes_woocommerce_page_title', 5 );
add_action( 'woocommerce_account_navigation', 'woocommerce_breadcrumb', 6 );
add_action( 'woocommerce_account_navigation', 'adri_ajdethemes_page_title_wrapper_end', 7 );
// account - content
add_action( 'woocommerce_account_navigation', 'adri_ajdethemes_container', 8 );
add_action( 'woocommerce_account_navigation', 'adri_ajdethemes_col_lg_12', 9 );
add_action( 'woocommerce_account_dashboard', 'adri_ajdethemes_col_lg_12_end', 10 );
add_action( 'woocommerce_account_dashboard', 'adri_ajdethemes_container_end', 11 );
// account - login
add_action( 'woocommerce_before_customer_login_form', 'adri_ajdethemes_woocommerce_account_login', 5 );
add_action( 'woocommerce_after_customer_login_form', 'adri_ajdethemes_woocommerce_account_login_end', 40 );


/**
 * Shop Page Title
 *
 * @see adri_ajdethemes_page_title_wrapper()
 * @see woocommerce_breadcrumb()
 */

remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
add_action( 'woocommerce_before_main_content', 'adri_ajdethemes_page_title_wrapper', 10 );
add_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 11 );
add_action( 'woocommerce_before_shop_loop', 'adri_ajdethemes_page_title_wrapper_end', 7 );

add_action( 'woocommerce_before_single_product', 'adri_ajdethemes_page_title_wrapper_end', 7 );


/**
 * Shop Layout
 *
 * @see adri_ajdethemes_container()
 * @see adri_ajdethemes_col_lg_12()
 * @see adri_ajdethemes_sorting_wrapper()
 * @see adri_ajdethemes_shop_loop_wrapper()
 */

remove_action( 'woocommerce_before_shop_loop', 'woocommerce_output_all_notices', 10 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

add_action( 'woocommerce_before_shop_loop', 'adri_ajdethemes_container', 8 );

add_action( 'woocommerce_before_shop_loop', 'adri_ajdethemes_col_lg_12', 9 );
add_action( 'woocommerce_before_shop_loop', 'woocommerce_output_all_notices', 10 );
add_action( 'woocommerce_before_shop_loop', 'adri_ajdethemes_col_lg_12_end', 11 );

add_action( 'woocommerce_before_shop_loop', 'adri_ajdethemes_sorting_wrapper', 15 );
add_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
add_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
add_action( 'woocommerce_before_shop_loop', 'adri_ajdethemes_sorting_wrapper_end', 31 );

add_action( 'woocommerce_before_shop_loop', 'adri_ajdethemes_shop_loop_wrapper', 40 );
add_action( 'woocommerce_after_shop_loop', 'adri_ajdethemes_shop_loop_wrapper_end', 40 );

add_action( 'woocommerce_after_shop_loop', 'adri_ajdethemes_container_end', 50 );


/**
 * Shop Products
 *
 * @see adri_ajdethemes_product_img_wrapper()
 */

remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
add_action( 'woocommerce_after_shop_loop_item', 'adri_ajdethemes_add_to_cart_plus_btn', 10 );

add_action( 'woocommerce_before_shop_loop_item_title', 'adri_ajdethemes_product_img_wrapper', 15 );
add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 16 );
add_action( 'woocommerce_before_shop_loop_item_title', 'adri_ajdethemes_product_img_wrapper_end', 17 );

add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );



/**
 * Single Product Page
 *
 * @see adri_ajdethemes_product_img_wrapper()
 */

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

add_action( 'woocommerce_before_main_content', 'adri_ajdethemes_single_product_wrapper' );
// notice grid wrapper
add_action( 'woocommerce_before_single_product', 'adri_ajdethemes_container', 8 );
add_action( 'woocommerce_before_single_product', 'adri_ajdethemes_col_lg_12', 9 );
add_action( 'woocommerce_before_single_product', 'adri_ajdethemes_col_lg_12_end', 11 );
add_action( 'woocommerce_before_single_product', 'adri_ajdethemes_container_end', 12 );

add_action( 'woocommerce_before_single_product_summary', 'adri_ajdethemes_single_product_image_wrapper', 9 );
add_action( 'woocommerce_before_single_product_summary', 'adri_ajdethemes_single_product_image_wrapper_end', 30 );

add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 15 );

add_action( 'woocommerce_after_single_product_summary', 'adri_ajdethemes_single_product_content_wrapper_end', 9 );

add_action( 'woocommerce_after_single_product_summary', 'adri_ajdethemes_single_product_tabs_wrapper', 9 );
add_action( 'woocommerce_after_single_product_summary', 'adri_ajdethemes_single_product_tabs_wrapper_end', 11 );

add_action( 'woocommerce_after_single_product_summary', 'adri_ajdethemes_single_related_products_wrapper', 19 );
add_action( 'woocommerce_after_single_product_summary', 'adri_ajdethemes_single_related_products_wrapper_end', 21 );

add_action( 'woocommerce_after_single_product', 'adri_ajdethemes_single_product_wrapper_end' );


/**
 * Products - Custom Add to Cart [+] button
 *
 * @return  void
 */
if ( ! function_exists( 'adri_ajdethemes_add_to_cart_plus_btn' ) ) {
	function adri_ajdethemes_add_to_cart_plus_btn() {
		global $product;

		$add_to_cart = sprintf( 
			'<a href="' . esc_url( '%2$s' ) . '" 
				class="add-to-cart-plus-btn add_to_cart_button ajax_add_to_cart" 
				title="add to cart" 
				data-product_id="' . esc_attr( '%3$s' ) . '" 
				data-product_sku="' . esc_attr( '%4$s' ) . '" 
				data-quantity="' . esc_attr( '%5$s' ) . '"
			>
				<span></span>
				<p class="screen-reader-text">' . esc_attr( '%1$s', 'adri-ajdethemes' ) . '</p>
			</a>',
			
			'add to cart', 						// 1
			$product->add_to_cart_url(), 		// 2
			$product->get_id(), 				// 3
			$product->get_sku(),				// 4
			isset( $quantity ) ? $quantity : 1	// 5
		);

		echo wp_kses_post( $add_to_cart );
	}
}


/**
 * Single Product - Related Products Wrapper
 *
 * @return  void
 */
if ( ! function_exists( 'adri_ajdethemes_single_related_products_wrapper' ) ) {
	function adri_ajdethemes_single_related_products_wrapper() {
		echo '
			<div class="woocommerce-related-products">
				<div class="container">
					<div class="row">
						<div class="col-lg-12">';
	}
}


/**
 * Closing Single Product - Related Products Wrapper
 *
 * @return  void
 */
if ( ! function_exists( 'adri_ajdethemes_single_related_products_wrapper_end' ) ) {
	function adri_ajdethemes_single_related_products_wrapper_end() {
		echo '
						</div><!-- .col-lg-12 -->
					</div><!-- end of .row -->
				</div><!-- end of .container -->
			</div><!-- end of .woocommerce-related-products -->';
	}
}


/**
 * Single Product Image Wrapper
 *
 * @return  void
 */
if ( ! function_exists( 'adri_ajdethemes_single_product_image_wrapper' ) ) {
	function adri_ajdethemes_single_product_image_wrapper() {
		echo '
			<div class="container">
				<div class="row">
					<div class="col-lg-6">';
	}
}


/**
 * Closing Single Product Image Wrapper
 *
 * @return  void
 */
if ( ! function_exists( 'adri_ajdethemes_single_product_image_wrapper_end' ) ) {
	function adri_ajdethemes_single_product_image_wrapper_end() {
		echo '
			</div><!-- end of .col-lg-6 -->
			<div class="col-lg-6">';
	}
}


/**
 * Closing Single Product Content Wrapper
 *
 * @return  void
 */
if ( ! function_exists( 'adri_ajdethemes_single_product_content_wrapper_end' ) ) {
	function adri_ajdethemes_single_product_content_wrapper_end() {
		echo '
					</div><!-- end of .col-lg-6 -->
				</div><!-- end of .row -->
			</div><!-- end of .container -->';
	}
}


/**
 * Single Product Tabs Wrapper
 *
 * @return  void
 */
if ( ! function_exists( 'adri_ajdethemes_single_product_tabs_wrapper' ) ) {
	function adri_ajdethemes_single_product_tabs_wrapper() {
		echo '
				<div class="single-product-tabs">
					<div class="container">
						<div class="row">
							<div class="col-lg-12">';
	}
}

/**
 * Closing Single Product Tabs Wrapper
 *
 * @return  void
 */
if ( ! function_exists( 'adri_ajdethemes_single_product_tabs_wrapper_end' ) ) {
	function adri_ajdethemes_single_product_tabs_wrapper_end() {
		echo '
						</div><!-- end of .col-lg-12 -->
					</div><!-- end of .row -->
				</div><!-- end of .container -->
			</div><!-- end of .single-product-tabs -->';
	}
}


/**
 * Single Product Wrapper
 *
 * @return  void
 */
if ( ! function_exists( 'adri_ajdethemes_single_product_wrapper' ) ) {
	function adri_ajdethemes_single_product_wrapper() {		
		if ( is_product() ) {
			echo '<div class="woocommerce-single-product">';
		}
	}
}


/**
 * Closing Single Product Wrapper
 *
 * @return  void
 */
if ( ! function_exists( 'adri_ajdethemes_single_product_wrapper_end' ) ) {
	function adri_ajdethemes_single_product_wrapper_end() {
		if ( is_product() ) {
			echo '</div><!-- end of .woocommerce-single-product -->';
		}
	}
}


/**
 * Shop Page Title Wrapper
 *
 * @return  void
 */
if ( ! function_exists( 'adri_ajdethemes_page_title_wrapper' ) ) {
	function adri_ajdethemes_page_title_wrapper() {

		if ( ! is_product() ) {

			echo '
				<div class="woocommerce-page-title">
					<div class="container">
						<div class="row">
							<div class="col-lg-12">
							
								<div class="pt-content">';

		}
	}
}


/**
 * Closing Shop Page Title Wrapper
 *
 * @return  void
 */
if ( ! function_exists( 'adri_ajdethemes_page_title_wrapper_end' ) ) {
	function adri_ajdethemes_page_title_wrapper_end() {

		if ( ! is_product() ) {

			echo '
								</div><!-- end of .pt-content -->
	
							</div><!-- end of .col-lg-12 -->
						</div><!-- end of .row -->
					</div><!-- end of .container -->
				</div><!-- end of .woocommerce-page-title -->';

		}
	}
}


/**
 * Custom WooCommerce Wrappers
 *
 * @return void
 */
if ( ! function_exists( 'adri_ajdethemes_woocommerce_wrapper' ) ) {
	function adri_ajdethemes_woocommerce_wrapper() {
		echo '<main class="woocommerce-main">';
	}
}


/**
 * Closing Custom WooCommerce Wrappers
 *
 * @return void
 */
if ( ! function_exists( 'adri_ajdethemes_woocommerce_wrapper_end' ) ) {
	function adri_ajdethemes_woocommerce_wrapper_end() {
		echo '</main>';
	}
}


/**
 * Container Wrapper
 *
 * @return  void
 */
if ( ! function_exists( 'adri_ajdethemes_container' ) ) {
	function adri_ajdethemes_container() {
		echo '<div class="container">';
	}
}


/**
 * Closing Container Wrapper
 *
 * @return  void
 */
if ( ! function_exists( 'adri_ajdethemes_container_end' ) ) {
	function adri_ajdethemes_container_end() {
		echo '</div><!-- end of .container -->';
	}
}


/**
 * Row & Col-lg-12 Wrapper
 *
 * @return  void
 */
if ( ! function_exists( 'adri_ajdethemes_col_lg_12' ) ) {
	function adri_ajdethemes_col_lg_12() {
		echo '
			<div class="row">
				<div class="col-lg-12">';
	}
}


/**
 * Closing Row & Col-lg-12 Wrapper
 *
 * @return  void
 */
if ( ! function_exists( 'adri_ajdethemes_col_lg_12_end' ) ) {
	function adri_ajdethemes_col_lg_12_end() {
		echo '
				</div><!-- end of .col-lg-12 -->
			</div><!-- end of .row -->';
	}
}


/**
 * Shop Row - Sorting Wrapper
 *
 * @return  void
 */
if ( ! function_exists( 'adri_ajdethemes_sorting_wrapper' ) ) {
	function adri_ajdethemes_sorting_wrapper() {
		echo '
			<div class="row">
				<div class="col-lg-12">

					<div class="woocommerce-sorting">';
	}
}


/**
 * Shop Closing Row - Sorting Wrapper
 *
 * @return  void
 */
if ( ! function_exists( 'adri_ajdethemes_sorting_wrapper_end' ) ) {
	function adri_ajdethemes_sorting_wrapper_end() {
		echo '
				</div><!-- end of .woocommerce-sorting -->

			</div><!-- end of .col-lg-12 -->
		</div><!-- end of .row -->';
	}
}


/**
 * Shop Row - Products Wrapper
 *
 * @return void
 */
if ( ! function_exists( 'adri_ajdethemes_shop_loop_wrapper' ) ) {
	function adri_ajdethemes_shop_loop_wrapper() {
		echo '
			<div class="row">
				<div class="col-lg-12">

					<div class="woocommerce-shop-wrapper">
		';
	}
}


/**
 * Shop Closing Row - Products Wrapper
 *
 * @return void
 */
if ( ! function_exists( 'adri_ajdethemes_shop_loop_wrapper_end' ) ) {
	function adri_ajdethemes_shop_loop_wrapper_end() {
		echo '	
					</div><!-- end of .woocommerce-shop-wrapper -->

				</div><!-- end of .col-lg-12 -->
			</div><!-- end of .row -->';
	}
}


/**
 * Shop Product Image Wrapper
 *
 * @return void
 */
if ( ! function_exists( 'adri_ajdethemes_product_img_wrapper' ) ) {
	function adri_ajdethemes_product_img_wrapper() {
		echo '<div class="woocommerce-product-img-wrapper">';
	}
}
 

/**
 * Closing Shop Product Image Wrapper
 *
 * @return void
 */
if ( ! function_exists( 'adri_ajdethemes_product_img_wrapper_end' ) ) {
	function adri_ajdethemes_product_img_wrapper_end() {
		echo '</div><!-- end of .woocommerce-product-img-wrapper -->';
	}
}


/**
 * Cart Empty Wrapper
 *
 * @return void
 */
if ( ! function_exists( 'adri_ajdethemes_cart_empty_wrapper' ) ) {
	function adri_ajdethemes_cart_empty_wrapper() {
		echo '
		<div class="woocommerce-cart-empty">
			<div class="container">
				<div class="row">
					<div class="col-lg-12">
		';
	}
}


/**
 * Closing Cart Empty Wrapper
 *
 * @return void
 */
if ( ! function_exists( 'adri_ajdethemes_cart_empty_wrapper_end' ) ) {
	function adri_ajdethemes_cart_empty_wrapper_end() {
		echo '
					</div><!-- end of .col-lg-12 -->
				</div><!-- end of .row -->
			</div><!-- end of .container -->
		</div><!-- end of .woocommerce-cart-empty -->
		';
	}
}


/**
 * Account Login Wrapper
 *
 * @return void
 */
if ( ! function_exists( 'adri_ajdethemes_woocommerce_account_login' ) ) {
	function adri_ajdethemes_woocommerce_account_login() {
		echo '<div class="woocommerce-account-login">';
	}
}


/**
 * Closing Account Login Wrapper
 *
 * @return void
 */
if ( ! function_exists( 'adri_ajdethemes_woocommerce_account_login_end' ) ) {
	function adri_ajdethemes_woocommerce_account_login_end() {
		echo '</div><!-- end of .woocommerce-account-login -->';
	}
}