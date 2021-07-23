<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    wezido
 * @subpackage wezido/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    wezido
 * @subpackage wezido/includes
 * @author     Your Name <email@example.com>
 */
class wezido {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      wezido_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $wezido    The string used to uniquely identify this plugin.
	 */
	protected $wezido;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'WEZIDO_VERSION' ) ) {
			$this->version = WEZIDO_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->wezido = 'wezido';

		$this->wezido_load_dependencies();
		$this->wezido_set_locale();
		$this->wezido_define_admin_hooks();
		$this->wezido_define_public_hooks();

		$this->wezido_define_elementor_widgets_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - wezido_Loader. Orchestrates the hooks of the plugin.
	 * - wezido_i18n. Defines internationalization functionality.
	 * - wezido_Admin. Defines all hooks for the admin area.
	 * - wezido_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function wezido_load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wezido-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wezido-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wezido-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wezido-public.php';

		/**
		 * The class responsible for defining all actions that occur in the front side of our widgets.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'elementor-widgets/class-wezido-elementor-widgets.php';

		$this->loader = new wezido_Loader();

	}
	
	

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the wezido_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function wezido_set_locale() {

		$plugin_i18n = new wezido_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

    /**
     * Load the required wigdets...
     *
     * @since    1.0.0
     * @access   private
     */
    private function wezido_load_elementor_widgets_dependencies() {        
     
        
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'elementor-widgets/class-title.php'; 
        
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'elementor-widgets/class-team-member.php'; 
        
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'elementor-widgets/class-flipbox.php'; 
        
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'elementor-widgets/class-before-after.php';
        
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'elementor-widgets/class-pricing-table.php';
        
         require_once plugin_dir_path( dirname( __FILE__ ) ) . 'elementor-widgets/class-post.php';
        
         require_once plugin_dir_path( dirname( __FILE__ ) ) . 'elementor-widgets/class-edd-recent.php';
    }

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function wezido_define_admin_hooks() {

		$plugin_admin = new wezido_Admin( $this->get_wezido(), $this->wezido_get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'wezido_enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'wezido_enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function wezido_define_public_hooks() {

		$plugin_public = new wezido_Public( $this->get_wezido(), $this->wezido_get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'wezido_enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'wezido_enqueue_scripts' );

    }
    
    /**
     * Register all of the hooks related to the Elementor widgets
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     * @return   bool
     */
    private function wezido_define_elementor_widgets_hooks() {
        
        $plugin_elementor_widgets = new wezido_Elementor_Widgets( $this->get_wezido(), $this->wezido_get_version() );

        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_elementor_widgets, 'wezido_enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_elementor_widgets, 'wezido_enqueue_scripts' );
        
    }

    /**
     * Register all of the widgets related to Elementor
     * @since    1.0.0
     * @access   public
     */
    public function wezido_register_elementor_widgets() {
        $this->wezido_load_elementor_widgets_dependencies();
        Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Wezido_Title() );
        
        Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Wezido_Team() );
        
         Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Wezido_flipbox() );
         
         Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Wezido_Before_after() );
         
         Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Wezido_pricing_table() );
         
         Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Wezido_post() );
         
         if (class_exists('Easy_Digital_Downloads')){
          Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Wezido_edd_recent() );
         }
    }


	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_wezido() {
		return $this->wezido;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    wezido_Loader    Orchestrates the hooks of the plugin.
	 */
	public function wezido_get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function wezido_get_version() {
		return $this->version;
	}

}
