<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    wezido
 * @subpackage wezido/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    wezido
 * @subpackage wezido/admin
 * @author     Your Name <email@example.com>
 */
class wezido_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $wezido    The ID of this plugin.
	 */
	private $wezido;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $wezido       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $wezido, $version ) {

		$this->wezido = $wezido;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function wezido_enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in wezido_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The wezido_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->wezido, plugin_dir_url( __FILE__ ) . 'css/wezido-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function wezido_enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in wezido_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The wezido_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->wezido, plugin_dir_url( __FILE__ ) . 'js/wezido-admin.js', array( 'jquery' ), $this->version, false );

	}

}
