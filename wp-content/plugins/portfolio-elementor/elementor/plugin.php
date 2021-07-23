<?php
namespace Powerfolio;
use Powerfolio\Widgets\ELPT_Portfolio_Widget;
use Powerfolio\Widgets\ELPT_Powerfolio_Gallery_Widget;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Main Plugin Class
 *
 * Register new elementor widget.
 *
 * @since 1.0.0
 */
class ELPT_Register_Powerfolio_Elementor {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function __construct() {
		$this->add_actions();
	}

	/**
	 * Add Actions
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function add_actions() {
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'on_widgets_registered' ] );

		add_action( 'elementor/frontend/before_register_scripts', function() {
			//wp_register_script( 'hello-world', plugins_url( '/assets/js/hello-world.js', ELEMENTOR_Pando_Slideshow__FILE__ ), [ 'jquery' ], false, true );
			wp_enqueue_script( 'jquery-isotope', plugin_dir_url( __FILE__ ) . '../vendor/isotope/js/isotope.pkgd.js', array('jquery', 'imagesloaded'), '2.0.1', true );
			wp_enqueue_script( 'jquery-packery', plugin_dir_url( __FILE__ ) . '../vendor/isotope/js/packery-mode.pkgd.min.js', array('jquery','jquery-isotope', 'imagesloaded'), '3.0.6', true );		

			//Image Lightbox
			wp_enqueue_script( 'simple-lightbox-js', plugin_dir_url( __FILE__ ) .  '../vendor/simplelightbox/dist/simple-lightbox.min.js', array('jquery'), '20151218', true );
			wp_enqueue_style( 'simple-lightbox-css', plugin_dir_url( __FILE__ ) .  '../vendor/simplelightbox/dist/simplelightbox.min.css' );
		
			//Custom CSS
			wp_enqueue_style( 'elpt-portfolio-css', plugin_dir_url( __FILE__ ) .  '../css/powerfolio_css.css' );
							
			//JS
			wp_enqueue_script( 'elpt-portfolio-js', plugin_dir_url( __FILE__ ) . '../js/custom-portfolio.js', array('jquery','jquery-isotope','jquery-packery'), '20151215', true );				
			
		} );

		add_action( 'elementor/frontend/element_ready/widget', function() {	
			//wp_enqueue_script( 'elpt-portfolio-js-elementor', plugin_dir_url( __FILE__ ) . '../js/custom-portfolio-elementor.js', array('jquery', 'isotope'), '99999999', true );				
		} );

		add_action( 'elementor/editor/before_enqueue_scripts', function() {			
			wp_enqueue_script( 'jquery-isotope', plugin_dir_url( __FILE__ ) . '../vendor/isotope/js/isotope.pkgd.js', array('jquery', 'imagesloaded'), '20151215', true );
			wp_enqueue_script( 'jquery-packery', plugin_dir_url( __FILE__ ) . '../vendor/isotope/js/packery-mode.pkgd.min.js', array('jquery', 'jquery-isotope'), '20151216', true );
			

			wp_enqueue_script( 'elpt-portfolio-js-elementor', plugin_dir_url( __FILE__ ) . '../js/custom-portfolio-elementor.js', array('jquery', 'jquery-isotope'), '99999999', true );	
		} );

		
	}

	/**
	 * On Widgets Registered
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function on_widgets_registered() {
		$this->includes();
		$this->register_widget();
	}

	/**
	 * Includes
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function includes() {
		require __DIR__ . '/widgets/portfolio_widget.php';
		require __DIR__ . '/widgets/powerfolio_gallery_widget.php';
	}

	/**
	 * Register Widget
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function register_widget() {
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new ELPT_Portfolio_Widget() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new ELPT_Powerfolio_Gallery_Widget() );
	}
}

new ELPT_Register_Powerfolio_Elementor();
