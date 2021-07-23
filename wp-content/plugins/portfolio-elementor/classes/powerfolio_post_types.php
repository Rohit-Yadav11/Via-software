<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Portfolio: Custom Post Types
 *
 *
 */
class ELPT_portfolio_Post_Types {
	
	public function __construct()
	{
		$this->register_post_type();
		$this->elpt_create_portfolio_taxonomies();
		
		//Flush rewrite rules
		add_action( 'init', array( __CLASS__, 'elpt_flush_rewrite_rules_maybe') , 20 );
	}

	//Register post type
	public function register_post_type()
	{
		$args = array();	

		// Portfolio
		$args['post-type-portfolio'] = array(
			'labels' => array(
				'name' => __( 'Portfolio', 'elemenfolio' ),
				'singular_name' => __( 'Item', 'elemenfolio' ),
				'add_new' => __( 'Add New Item', 'elemenfolio' ),
				'add_new_item' => __( 'Add New Item', 'elemenfolio' ),
				'edit_item' => __( 'Edit Item', 'elemenfolio' ),
				'new_item' => __( 'New Item', 'elemenfolio' ),
				'view_item' => __( 'View Item', 'elemenfolio' ),
				'search_items' => __( 'Search Through portfolio', 'elemenfolio' ),
				'not_found' => __( 'No items found', 'elemenfolio' ),
				'not_found_in_trash' => __( 'No items found in Trash', 'elemenfolio' ),
				'parent_item_colon' => __( 'Parent Item:', 'elemenfolio' ),
				'menu_name' => __( 'Portfolio', 'elemenfolio' ),				
			),		  
			'hierarchical' => false,
	        'description' => __( 'Add a Portfolio Item', 'elemenfolio' ),
	        'menu_icon' =>  'dashicons-images-alt',
	        'public' => true,
	        'publicly_queryable' => true,
			'exclude_from_search' => false,
			'has_archive' => false,
	        'query_var' => true,
			'rewrite' => array( 'slug' => 'portfolio' ),
			'show_in_rest' => true,
            'supports' => array('title','editor', 'thumbnail')
	        // This is where we add taxonomies to our CPT
        	//'taxonomies'          => array( 'category' ),
		);	

		// Register post type: name, arguments
		register_post_type('elemenfolio', $args['post-type-portfolio']);
	}	

	//Register Taxonomies
	static function elpt_create_portfolio_taxonomies() {
		// Add new taxonomy, make it hierarchical (like categories)
		$labels = array(
			'name'              => _x( 'Portfolio Categories', 'taxonomy general name', 'elemenfolio' ),
			'singular_name'     => _x( 'Portfolio Category', 'taxonomy singular name', 'elemenfolio' ),
			'search_items'      => __( 'Search Portfolio Categories', 'elemenfolio' ),
			'all_items'         => __( 'All Portfolio Categories', 'elemenfolio' ),
			'parent_item'       => __( 'Parent Portfolio Category', 'elemenfolio' ),
			'parent_item_colon' => __( 'Parent Portfolio Category:', 'elemenfolio' ),
			'edit_item'         => __( 'Edit Portfolio Category', 'elemenfolio' ),
			'update_item'       => __( 'Update Portfolio Category', 'elemenfolio' ),
			'add_new_item'      => __( 'Add New Portfolio Category', 'elemenfolio' ),
			'new_item_name'     => __( 'New Portfolio Category', 'elemenfolio' ),
			'menu_name'         => __( 'Portfolio Categories', 'elemenfolio' ),
		);
	
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'portfoliocategory' ),
			'show_in_rest' =>true,
		);
	
		register_taxonomy( 'elemenfoliocategory', array( 'elemenfolio' ), $args );
	}	

	static function elpt_flush_rewrite_rules_maybe() {
		if ( get_option( 'elpt_flush_rewrite_rules_flag' ) ) {
			flush_rewrite_rules();
			delete_option( 'elpt_flush_rewrite_rules_flag' );
		}
	}

	//Enable Elementor on portfolio post type
	//From https://wordpress.org/support/topic/option-to-enable-by-default-elementor-for-custom-post-type/
	public static function elpt_add_cpt_support() {
		//if exists, assign to $cpt_support var
		$cpt_support = get_option( 'elementor_cpt_support' );
		
		//check if option DOESN'T exist in db
		if( ! $cpt_support ) {
			$cpt_support = [ 'page', 'post', 'elemenfolio' ]; //create array of our default supported post types
			update_option( 'elementor_cpt_support', $cpt_support ); //write it to the database
		}
		
		//if it DOES exist, but portfolio is NOT defined
		else if( ! in_array( 'elemenfolio', $cpt_support ) ) {
			$cpt_support[] = 'elemenfolio'; //append to array
			update_option( 'elementor_cpt_support', $cpt_support ); //update database
		}
		
		//otherwise do nothing, portfolio already exists in elementor_cpt_support option
	}
}

function elpt_portfolio_types() { 
	new ELPT_portfolio_Post_Types(); 
}

add_action( 'init', 'elpt_portfolio_types' );




