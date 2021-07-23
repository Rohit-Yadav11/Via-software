<?php

/**
 * The elementor-widget-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    wezido
 * @subpackage wezido/admin
 */

/**
 * The elementor-widget-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the elementor-widget-specific stylesheet and JavaScript.
 *
 * @package    wezido
 * @subpackage wezido/elementor-widgets
 * @author     Your Name <email@example.com>
 */
class wezido_Elementor_Widgets {

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
         add_action( 'wp_enqueue_scripts', [ $this, 'wezido_register_assets' ] );
         add_action( 'admin_enqueue_scripts', [ $this, 'wezido_register_assets' ] );
	}

	/**
	 * Register the stylesheets for the elementor-widget.
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

        wp_enqueue_style( 'wezido-elemenor-frontend', plugin_dir_url( __FILE__ ) . 'css/wezido-elementor-frontend.css', array(), $this->version, 'all' );
    
        // wp_enqueue_style( 'autre-exemple', plugin_dir_url( __FILE__ ) . 'css/autre-exemple.css', array(), $this->version, 'all' );
	}
    
	/**
	 * Register the JavaScript for the elementor-widget.
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

	     wp_enqueue_script( 'beerslider', plugin_dir_url( __FILE__ ) . 'js/vendor/jquery.beerslider.js', array( 'jquery' ), $this->version, false );
	   
	     wp_enqueue_script( 'wezido_flipbox' );
	     wp_enqueue_script( 'wezido_before_after' );
	}
	
	   /**
         * All available scripts
         *
         * @return array
         */
        public function wezido_get_scripts(){


            $script_list = [

                'wezido_flipbox' => [
                    'src'     => plugin_dir_url( __FILE__ ) . 'js/wezido-flipbox.js',
                    'version' => 1.1,
                    'deps'    => [ 'jquery' ]
                ],
                
                'wezido_before_after' => [
                    'src'     => plugin_dir_url( __FILE__ ) . 'js/wezido-before-after.js',
                    'version' => 1.1,
                    'deps'    => [ 'jquery' ]
                ],
                

            ];

        

            return $script_list;
}
	 /**
         * Register scripts and styles
         *
         * @return void
         */
        public function wezido_register_assets() {
            $scripts = $this->wezido_get_scripts();
            

            // Register Scripts
            foreach ( $scripts as $handle => $script ) {
                $deps = ( isset( $script['deps'] ) ? $script['deps'] : false );
                wp_register_script( $handle, $script['src'], $deps, $script['version'], true );
            }

         

            
            
        }

}



function wezido_add_elementor_widget_categories( $elements_manager ) {

	$elements_manager->add_category(
		'wezido-general',
		[
			'title' => __( 'Wezido', 'wezido' ),
			'icon' => 'fa fa-plug',
		]
	);
	

}
add_action( 'elementor/elements/categories_registered', 'wezido_add_elementor_widget_categories' );


/*
 * HTML Tag list
 * return array
 */
function wezido_html_tag_lists() {
    $html_tag_list = [
        'h1'   => __( 'H1', 'wezido' ),
        'h2'   => __( 'H2', 'wezido' ),
        'h3'   => __( 'H3', 'wezido' ),
        'h4'   => __( 'H4', 'wezido' ),
        'h5'   => __( 'H5', 'wezido' ),
        'h6'   => __( 'H6', 'wezido' ),
        'p'    => __( 'p', 'wezido' ),
        'div'  => __( 'div', 'wezido' ),
        'span' => __( 'span', 'wezido' ),
    ];
    return $html_tag_list;
}
/*
 * Item Extracts
 * return array
 */
if ( ! function_exists( 'wezido_items_extracts' ) ) {
  function wezido_items_extracts(  $type = '', $query_args = array() ) {

    $options = array();

    switch( $type ) {

      case 'pages':
      case 'page':
      $pages = get_pages( $query_args );

      if ( !empty($pages) ) {
        foreach ( $pages as $page ) {
          $options[$page->post_title] = $page->ID;
        }
      }
      break;

      case 'posts':
      case 'post':
      $posts = get_posts( $query_args );

      if ( !empty($posts) ) {
        foreach ( $posts as $post ) {
          $options[$post->post_title] = lcfirst($post->ID);
        }
      }
      break;

      case 'tags':
      case 'tag':

	  if (isset($query_args['taxonomies']) && taxonomy_exists($query_args['taxonomies'])) {
		$tags = get_terms( $query_args['taxonomies'] );
		  if ( !is_wp_error($tags) && !empty($tags) ) {
			foreach ( $tags as $tag ) {
			  $options[$tag->name] = $tag->term_id;
		  }
		}
	  }
      break;

      case 'categories':
      case 'category':

	  if (isset($query_args['taxonomy']) && taxonomy_exists($query_args['taxonomy'])) {
		$categories = get_categories( $query_args );
  		if ( !empty($categories) && is_array($categories) ) {

  		  foreach ( $categories as $category ) {
  			 $options[$category->name] = $category->term_id;
  		  }
  		}
	  }
      break;

    }

    return $options;

  }
}

#-----------------------------------------------------------------#
# elementor pagination
#-----------------------------------------------------------------#/

if ( ! function_exists( 'wezido_paging_nav' ) ) {
  function wezido_paging_nav( $max_num_pages = false, $args = array() ) {

    if (get_query_var('paged')) {
      $paged = get_query_var('paged');
    } elseif (get_query_var('page')) {
      $paged = get_query_var('page');
    } else {
      $paged = 1;
    }

    if ($max_num_pages === false) {
      global $wp_query;
      $max_num_pages = $wp_query->max_num_pages;
    }


    $defaults = array(
      'nav'            => 'load',
      'posts_per_page' => get_option( 'posts_per_page' ),
      'max_pages'      => $max_num_pages,
      'post_type'      => 'download',
    );


    $args = wp_parse_args( $args, $defaults );

    if ( $max_num_pages < 2 ) { return; }

 

      $big = 999999999; // need an unlikely integer

      $links = paginate_links( array(
        'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
        'format'    => '?paged=%#%',
        'current'   => $paged,
        'total'     => $max_num_pages,
        'prev_next' => true,
        'prev_text' => esc_html__('Previous', 'mayosis'),
        'next_text' => esc_html__('Next', 'mayosis'),
        'end_size'  => 1,
        'mid_size'  => 2,
      ) );

      if (!empty($links)): ?>
       <div class="common-paginav text-center">
           <div class="pagination">
           <?php echo wp_kses_post($links); ?>    
           </div>
        </div>
  
      <?php endif;
    }

  
}
