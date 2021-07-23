<?php
/**
 * Plugin Name: Adri Ajdethemes Elements
 * Plugin URI: http://ajdethemes.com/adri/
 * Text Domain: adri-ajdethemes-elements
 * Domain Path: /languages/
 * Description: This plugins comes as a part of Adri theme for extended functionality.
 * Author: AjdeThemes
 * Version: 1.0.3
 * Author URI: http://ajdethemes.com/
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

final class Adri_Ajdethemes_Elements {

	const VERSION = '1.0.2';
	const MINIMUM_ELEMENTOR_VERSION = '2.0.0';
	const MINIMUM_PHP_VERSION = '7.0';

    /**
	 * Instance
	 *
	 * @since 1.0.0
	 */
    private static $_instance = null;
    
    /**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 */
	public static function instance() {

        if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
        return self::$_instance;
        
    }

    /**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

        add_action( 'init', [ $this, 'i18n' ] );
        add_action( 'plugins_loaded', [ $this, 'init' ] );
        
    }

    /**
	 * Load Textdomain
	 *
	 * Load plugin localization files.
	 *
	 * Fired by `init` action hook.
	 *
	 * @since 1.0.0
	 */
	public function i18n() {

		load_plugin_textdomain( 'adri-ajdethemes-elements' );

	}

    /**
	 * Initialize the plugin
	 *
	 * Load the plugin only after Elementor (and other plugins) are loaded.
	 * Checks for basic plugin requirements, if one check fail don't continue,
	 * if all check have passed load the files required to run the plugin.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since 1.0.0
	 */
	public function init() {

        // Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
			return;
        }
        
        // Check for required Elementor version
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
			return;
        }
        
        // Check for required PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
			return;
		}

        // Add a custom categories and reorder the Elementor category list
        add_action( 'elementor/elements/categories_registered', function(\Elementor\Elements_Manager $elements_manager ) {

            // use closure to gain access to private properties
            $set_categories = function( $categories ) {

                $this->categories = [
                    'ajdethemes-elements' => [
                        'title' => __( 'Ajdethemes Elements', 'adri-ajdethemes-elements' ),
                        'icon' => 'fa fa-plug',
                    ],
                    'basic' => [
                        'title' => __( 'Basic', 'adri-ajdethemes-elements' ),
                        'icon' => 'eicon-font',
                        'active' => false,
                    ],
                    'general' => [
                        'title' => __( 'General', 'adri-ajdethemes-elements' ),
                        'icon' => 'eicon-font',
                        'active' => false,
                    ],
                ];
            };
            $set_categories->call($elements_manager, '');

       } );
        
        // Add Plugin actions
		add_action( 'elementor/widgets/widgets_registered', 	[ $this, 'init_widgets' ] );
		add_action( 'elementor/controls/controls_registered', 	[ $this, 'init_controls' ] );

		// Register Widget Scripts & Styles
		add_action( 'elementor/frontend/after_enqueue_scripts', [ $this, 'widget_scripts' ] );
		add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'widget_styles' ] );

	   	// Icons Manager Custom Icon Tab
		add_filter( 'elementor/icons_manager/additional_tabs', 	[ $this, 'iconsmind_icon_font_custom_tab' ] );

		// Include theme shortcodes
		require_once( __DIR__ . '/includes/shortcodes.php' );
		require_once( __DIR__ . '/includes/widgets/class-widget_ft-inline-icon.php' );
		require_once( __DIR__ . '/includes/widgets/class-widget-adri-recent-posts-slider.php' );
    }


    /**
	 * Admin notice
	 *
	 * Warning when the site doesn't have Elementor installed or activated.
	 *
	 * @since 1.0.0
	 */
	public function admin_notice_missing_main_plugin() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'elementor-test-extension' ),
			'<strong>' . esc_html__( 'Elementor Test Extension', 'elementor-test-extension' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'elementor-test-extension' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

    }
	
	
    /**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required Elementor version.
	 *
	 * @since 1.0.0
	 */
	public function admin_notice_minimum_elementor_version() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'elementor-test-extension' ),
			'<strong>' . esc_html__( 'Elementor Test Extension', 'elementor-test-extension' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'elementor-test-extension' ) . '</strong>',
			 self::MINIMUM_ELEMENTOR_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

    }
	
	
    /**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required PHP version.
	 *
	 * @since 1.0.0
	 */
	public function admin_notice_minimum_php_version() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'elementor-test-extension' ),
			'<strong>' . esc_html__( 'Elementor Test Extension', 'elementor-test-extension' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'elementor-test-extension' ) . '</strong>',
			 self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

    }
	
	
    /**
	 * Init Widgets
	 *
	 * Include widgets files and register them
	 *
	 * @since 1.0.0
	 */
	public function init_widgets() {

		// Include Widget files
		require_once( __DIR__ . '/widgets/section-title.php' );
		require_once( __DIR__ . '/widgets/page-title.php' );
		require_once( __DIR__ . '/widgets/counter.php' );
		require_once( __DIR__ . '/widgets/inline-icon.php' );
		require_once( __DIR__ . '/widgets/frame-icon.php' );
		require_once( __DIR__ . '/widgets/feature-image.php' );
		require_once( __DIR__ . '/widgets/team-card.php' );
		require_once( __DIR__ . '/widgets/team-inline.php' );
		require_once( __DIR__ . '/widgets/pricing-table.php' );
		require_once( __DIR__ . '/widgets/accordion.php' );
		require_once( __DIR__ . '/widgets/testimonial-slider.php' );
		require_once( __DIR__ . '/widgets/slider-neue.php' );

		require_once( __DIR__ . '/widgets/wp-blog-posts.php' );
		require_once( __DIR__ . '/widgets/portfolio.php' );
		require_once( __DIR__ . '/widgets/slider.php' );
		require_once( __DIR__ . '/widgets/button.php' );
		require_once( __DIR__ . '/widgets/contact-form-7.php' );
		require_once( __DIR__ . '/widgets/intro.php' );
		require_once( __DIR__ . '/widgets/contact.php' );
		require_once( __DIR__ . '/widgets/slider-thirds.php' );
		// require_once( __DIR__ . '/widgets/woo-products.php' );
		// require_once( __DIR__ . '/widgets/woo-addtocart.php' );

		// Register widget
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Ajdethemes_Widget_SectionTitle() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Ajdethemes_Widget_Page_Title() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Ajdethemes_Widget_Counter() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Ajdethemes_Widget_Inline_Icon() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Ajdethemes_Widget_Frame_Icon() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Ajdethemes_Widget_Feature_Image() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Ajdethemes_Widget_Team_Card() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Ajdethemes_Widget_Team_Inline() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Ajdethemes_Widget_Pricing_Table() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Ajdethemes_Widget_Accordion() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Ajdethemes_Widget_Testimonials() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Ajdethemes_Widget_Neue_Slider() );

		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Ajdethemes_Widget_Blog_Posts() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Ajdethemes_Widget_Portfolio() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Ajdethemes_Widget_Image_Slider() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Ajdethemes_Widget_Button() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Ajdethemes_Widget_Contact_Form_7() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Ajdethemes_Widget_Intro() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Ajdethemes_Widget_Contact() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Ajdethemes_Widget_Thirds_Slider() );
		// \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Ajdethemes_Widget_Woo_Products() );
		// \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Ajdethemes_Widget_Woo_AddToCart() );
	}


	/**
	 * Init Controls
	 *
	 * Include controls files and register them
	 *
	 * @since 1.0.0
	 */
	public function init_controls() {

		// Include Control files
		// require_once( __DIR__ . '/controls/im-icons.php' );

		// Register control
		// \Elementor\Plugin::$instance->controls_manager->register_control( 'control-im-icons-', new \Test_Control() );

	}
	

	/**
	 * Register Widget Styles
	 *
	 * @since 1.0.0
	 */
	public function widget_styles() {
		
		wp_register_style( 'magnific-popup', plugins_url( 'assets/js/vendors/jquery.magnific-popup/magnific-popup.css', __FILE__ ) );
		wp_enqueue_style( 'magnific-popup', plugins_url( 'assets/js/vendors/jquery.magnific-popup/magnific-popup.css', __FILE__ ) );
		// wp_register_style( 'adri_ajdethemes_elements_widget_styles', plugins_url( 'assets/styles.css', __FILE__ ) );

	}
	

	/**
	 * Register Widget Scripts
	 *
	 * @since 1.0.0
	 */
	public function widget_scripts() {

		wp_enqueue_script( 'imagesLoaded', plugins_url( 'assets/js/vendors/imagesloaded.pkgd.min.js', __FILE__ ) );
		wp_enqueue_script( 'magnific-popup', plugins_url( 'assets/js/vendors/jquery.magnific-popup/jquery.magnific-popup.min.js', __FILE__ ) );
		wp_enqueue_script( 'isotope', plugins_url( 'assets/js/vendors/isotope.pkgd.min.js', __FILE__ ) );
		wp_enqueue_script( 'adri_ajdethemes_elements_main_js', plugins_url( 'assets/js/main.js', __FILE__ ) );
	}


	/**
	 * Register Custom Icon Font
	 *
	 * @since 1.0.0
	 */
	public function iconsmind_icon_font_custom_tab( $tabs = array() ) {

		$icon_font_style_path = plugins_url( 'assets/fonts/im/im-style.css', __FILE__ );

		require_once( __DIR__ . '/controls/iconsmind_icons.php' );

		$tabs['iconsmind-icons'] = array(
			'name'          => 'iconsmind-icons',
			'label'         => esc_html__( 'Ajdethemes Icons', 'adri-ajdethemes-elements' ),
			'labelIcon'     => 'icon-Coffee',
			'prefix'        => 'icon-',
			'displayPrefix' => 'icon',
			'url'           => $icon_font_style_path,
			'icons'         => get_iconsmind_icons(),
			'ver'           => '1.3.0',
		);
	
		return $tabs;
	}
}
Adri_Ajdethemes_Elements::instance();