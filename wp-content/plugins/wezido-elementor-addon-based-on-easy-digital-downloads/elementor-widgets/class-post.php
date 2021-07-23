<?php

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 
/**
 * @since 1.0.0
 */
class Wezido_post extends Elementor\Widget_Base {

    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);
	   
    }
 
    /**
     * Retrieve the widget name.
     *
     * @since 1.0.0
     *
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'wezido-post';
    }

    /**
     * Retrieve the widget title.
     *
     * @since 1.0.0
     *
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return __( 'Blog Post', 'wezido' );
    }
 
    /**
     * Retrieve the widget icon.
     *
     * @since 1.0.0
     *
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'eicon-posts-grid';
    }
 
    /**
     * Retrieve the list of categories the widget belongs to.
     *
     * Used to determine where to display the widget in the editor.
     *
     * Note that currently Elementor supports only one category.
     * When multiple categories passed, Elementor uses the first one.
     *
     * @since 1.0.0
     *
     * @access public
     *
     * @return array Widget categories.
     */
    public function get_categories() {
        return [ 'wezido-general' ];
    }

  

    /**
     * Register the widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     *
     * @access protected
     */
    protected function _register_controls() {
        $this->start_controls_section(
            'section_content',
            [
                'label' => __( 'Wezido post Recent Grid', 'wezido' ),
            ]
        );
        
        $this->add_control(
            'item_per_page',
            [
                'label'   => esc_html_x( 'Amount of item to display', 'Admin Panel', 'wezido' ),
                'type'    => Controls_Manager::NUMBER,
                'default' =>  "10",
                'label_block' => true,
            ]
        );
        $this->add_control(
            'list_layout',
            [
                'label'     => esc_html_x( 'Layout', 'Admin Panel','wezido' ),
                'description' => esc_html_x('Column layout for the list"', 'wezido' ),
                'type'      =>  Controls_Manager::SELECT,
                'default'    =>  "1/3",
                'label_block' => true,
                "options"    => array(
                    "full" => "1",
                    "1/2" => "2",
                    "1/3" => "3",
                    "1/4" => "4",
                    "1/5" => "5",
                    "1/6" => "6",
                ),
            ]

        );
         $this->add_control(
      'category',
      array(
        'label'       => esc_html__( 'Select Categories', 'wezido' ),
        'type'        => Controls_Manager::SELECT2,
        'multiple'    => true,
        
        'options'     => array_flip(wezido_items_extracts( 'categories', array(
          'sort_order'  => 'ASC',
          'taxonomy'    => 'download_category',
          'hide_empty'  => false,
        ) )),
        'label_block' => true,
      )
    );
    
        $this->add_control(
            'categorynotin',
            [
                'label' => __( 'Exclude Category', 'wezido' ),
                'description' => __('Add one category slug','wezido'),
                'type' =>  Controls_Manager::SELECT2,
                'multiple'    => true,
                 'options'     => array_flip(wezido_items_extracts( 'categories', array(
                      'sort_order'  => 'ASC',
                      'taxonomy'    => 'download_category',
                      'hide_empty'  => false,
                    ) )),
                    'label_block' => true,
                
            ]
        );
        
  $this->add_control(
      'tags',
      array(
        'label'       => esc_html__( 'Select Tags', 'wezido' ),
        'type'        => Controls_Manager::SELECT2,
        'multiple'    => true,
        
        'options'     => array_flip(wezido_items_extracts( 'tags', array(
          'sort_order'  => 'ASC',
          'taxonomies'    => 'download_tag',
          'hide_empty'  => false,
        ) )),
        'label_block' => true,
      )
    );
    
    $this->add_control(
            'order',
            [
                'label' => __( 'Order', 'wezido' ),
                'type' => Controls_Manager::SELECT,
                'label_block' => true,
                'options' => [
                    'asc' => 'Ascending',
                    'desc' => 'Descending'
                ],
                'default' => 'desc',

            ]
        );
        $this->end_controls_section();
        
        $this->start_controls_section(
			'style',
			[
				'label' => __( 'Style', 'wezido' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		
			$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'background',
				'label' => __( 'Background', 'wezido' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .wezido-post-item.post-style-one .wezido-post-box',
			]
		);
		
		$this->add_control(
			'border_color',
			[
				'label' => __( 'Border Color', 'wezido' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .wezido-post-item.post-style-one .wezido-post-box' => 'border-color: {{VALUE}}',
				],
			]
		);
		
		$this->add_control(
			'border_hover_color',
			[
				'label' => __( 'Border Hover Color', 'wezido' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .wezido-post-item.post-style-one:hover .wezido-post-box' => 'border-color: {{VALUE}}',
				],
			]
		);
		
		$this->add_control(
			'border_radius',
			[
				'label' => __( 'Border Radius', 'wezido' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .wezido-post-box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
	
		
		
		
			$this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                array(
                    'name'      => 'title_typography',
                    'label'     => __( 'Title Typography', 'wezido' ),
                    'selector'  => '{{WRAPPER}} .wezido-post-meta h3',
                )
            );  
            
            $this->add_control(
			'title_color',
			[
				'label' => __( 'Title Color', 'wezido' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wezido-post-meta h3' => 'color: {{VALUE}}',
				],
			]
		);
		
		$this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                array(
                    'name'      => 'pricing_typography',
                    'label'     => __( 'Pricing Typography', 'wezido' ),
                    'selector'  => '{{WRAPPER}} .wizido-post-price',
                )
            ); 
            $this->add_control(
			'pricing_bg',
			[
				'label' => __( 'Pricing Background', 'wezido' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wizido-post-price' => 'background: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'pricing_color',
			[
				'label' => __( 'Pricing Color', 'wezido' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wizido-post-price' => 'color: {{VALUE}}',
				],
			]
		);
			$this->add_control(
			'meta_color',
			[
				'label' => __( 'Meta Color', 'wezido' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wezido-post-meta ul li,{{WRAPPER}} .wezido-post-meta ul li a' => 'color: {{VALUE}}',
				],
			]
		);
			$this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                array(
                    'name'      => 'button_typography',
                    'label'     => __( 'Button Typography', 'wezido' ),
                    'selector'  => '{{WRAPPER}} .wezido-post-item .post-submit',
                )
            ); 
		$this->start_controls_tabs( 'tabs_button_style' );
		
		    $this->start_controls_tab(
			'normal',
			[
				'label' => __( 'Normal', 'wezido' ),
			]
		);
		      $this->add_control(
			'pricing_button_color',
			[
				'label' => __( 'Background Color', 'wezido' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				 'default' => '#1e3c78',
				'selectors' => [
					'{{WRAPPER}} .wezido-post-item .post-submit' => 'background-color: {{VALUE}}',
					
						
				],
			]
		);
		
		 $this->add_control(
			'pricing_buttonborder_color',
			[
				'label' => __( 'Border Color', 'wezido' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				 'default' => '#1e3c78',
				'selectors' => [
					'{{WRAPPER}} .wezido-post-item .post-submit' => 'border-color: {{VALUE}}',
					
						
				],
			]
		);
		
		 $this->add_control(
			'pricing_button_txt_color',
			[
				'label' => __( 'Text Color', 'wezido' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				 'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .wezido-post-item .post-submit' => 'color: {{VALUE}}',
					
				],
			]
		);
		$this->end_controls_tab();
		
		
		
		  $this->start_controls_tab(
			'hover',
			[
				'label' => __( 'Hover', 'wezido' ),
			]
		);
		      $this->add_control(
			'pricing_button_hover_color',
			[
				'label' => __( 'Background Color', 'wezido' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				 'default' => '#A10A8000',
				'selectors' => [
					'{{WRAPPER}} .wezido-post-item .post-submit:hover' => 'background-color: {{VALUE}}',
					
						
				],
			]
		);
		
		 $this->add_control(
			'pricing_button_hoverborder_color',
			[
				'label' => __( 'Border Color', 'wezido' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				 'default' => 'rgb(30 60 120 / 0.5)',
				'selectors' => [
					'{{WRAPPER}} .wezido-post-item .post-submit:hover' => 'border-color: {{VALUE}}',
					
						
				],
			]
		);
		
		 $this->add_control(
			'pricing_button_hover_txt_color',
			[
				'label' => __( 'Text Color', 'wezido' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				 'default' => 'rgb(30 60 120 / 0.5)',
				'selectors' => [
					'{{WRAPPER}} .wezido-post-item .post-submit:hover' => 'color: {{VALUE}}',
					
				],
			]
		);
		$this->end_controls_tab();
			
		$this->end_controls_tabs();
		
		
		$this->add_control(
			'overlay_section',
			[
				'label' => __( 'Overlay Color', 'wezido' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		
			$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'overlay_bg',
				'label' => __( 'Overlay Background', 'wezido' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .overlay-purchase',
			]
		);
		$this->end_controls_section();
    }

    /**
     * Render the widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     *
     * @access protected
     */
    protected function render() {

        $settings = $this->get_settings();
        $post_count = ! empty( $settings['item_per_page'] ) ? (int)$settings['item_per_page'] : 5;
        $post_order_term=$settings['order'];
        $categories= $settings['category'];
        $tags = $settings['tags'];
        $downloads_category_not=$settings['categorynotin'];

        ?>
        
        <div class="wezido-post-grid flex flex-wrap overflow-hidden sm:-mx-2 lg:-mx-2 xl:-mx-2">
            <?php
              
        global $post;
          global $wp_query; 
						if ( get_query_var('paged') ) {
							$paged = get_query_var('paged');
						} else if ( get_query_var('page') ) {
							$paged = get_query_var('page');
						} else {
							$paged = 1;
						}
						
						
						    
						     $args = array(
                            'post_type' => 'post',
                            'posts_per_page' => $post_count,
                            'order' => (string)trim($post_order_term),);
						
           
        
        
         if(!empty($categories[0])) {
      $args['tax_query'] = array(
        array(
          'taxonomy' => 'category',
          'field'    => 'ids',
          'terms'    => $categories
        )
      );
    }
    
    if(!empty($tags[0])) {
      $args['tax_query'] = array(
        array(
          'taxonomy' => 'tag',
          'field'    => 'ids',
          'terms'    => $tags
        )
      );
    }
    
    
     if(!empty($downloads_category_not[0])) {
      $args['tax_query'] = array(
        array(
          'taxonomy' => 'category',
          'field'    => 'ids',
          'terms'    => $downloads_category_not,
          'operator' => 'NOT IN'
        )
      );
    }
      $the_query =new \WP_Query($args);
    while ($the_query -> have_posts()) : $the_query -> the_post();
    $max_num_pages = $the_query->max_num_pages;?>
    
        <div class="wezido-post-item post-style-one w-1/2 overflow-hidden sm:my-2 sm:px-2 sm:w-1/2 md:w-1/2 lg:my-2 lg:px-2 lg:w-<?php echo $settings['list_layout'];?> xl:my-2 xl:px-2">
            <div class="wezido-post-box">
                
                <div class="wezido-post-thumbnail">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                            <?php the_post_thumbnail(); ?>
                        </a>
                    <?php endif; ?>
                    <div class="overlay-purchase">
                        <span class="overlay-plus">+</span>
                    </div>
                    
                </div>
                <div class="wezido-post-meta">
                    <h3> <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title();?></a></h3>
                    <?php
                    global $post;
                    $post_cats = get_the_term_list( get_the_ID(), 'category', '', _x(', ', '', 'wezido' ), '' );
                     $author_id=$post->post_author;
                    ?>
                    <ul>
                        <li><span>in</span> <?php echo $post_cats;?></li>
                        <li><span>by</span> <?php echo get_the_author_meta( 'display_name',$author_id ); ?></li>
                    </ul>
                     
									
                </div>
            </div>
        </div>
    <?php endwhile; wp_reset_postdata(); ?>
        </div>
           
        <?php
    }
 
    /**
     * Render the widget output in the editor.
     *
     * Written as a Backbone JavaScript template and used to generate the live preview.
     *
     * @since 1.0.0
     *
     * @access protected
     */
    protected function _content_template() {
        
    }

}
