<?php

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 
/**
 * @since 1.0.0
 */
class Wezido_pricing_table extends Elementor\Widget_Base {

 
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
        return 'wezido-pricing-table';
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
        return __( 'Pricing Table', 'wezido' );
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
        return 'eicon-price-list';
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
                'label' => __( 'Wezido Pricing', 'wezido' ),
            ]
        );
        
        $this->add_control(
         'title',
         [
            'label' => __( 'Title', 'wezido' ),
            'type' => Controls_Manager::TEXT,
            'default' => 'Title',
            'title' => __( 'Enter Table Title', 'wezido' ),
            
         ]
      );
      
       $this->add_control(
         'currency',
         [
            'label' => __( 'Currency', 'wezido' ),
            'type' => Controls_Manager::TEXT,
            'default' => '$',
            'title' => __( 'Enter Table Price Currency', 'wezido' ),
            
         ]
      );
       
       $this->add_control(
         'price',
         [
            'label' => __( 'Price', 'wezido' ),
            'type' => Controls_Manager::TEXT,
            'default' => '25',
            'title' => __( 'Enter Table Price Value', 'wezido' ),
            
         ]
      );
      
      $this->add_control(
         'time',
         [
            'label' => __( 'Timeframe', 'wezido' ),
            'type' => Controls_Manager::TEXT,
            'default' => '/mo',
            'title' => __( 'Enter Table Price Timeframe', 'wezido' ),
            
         ]
      );
       
       $this->add_control(
         'icon',
         [
            'label' => __( 'Icon', 'wezido' ),
            'type' => Controls_Manager::ICONS,
           'default' => [
					'value' => 'fas fa-star',
					'library' => 'solid',
				],
            'title' => __( 'Enter Table Title Icon', 'wezido' ),
            
         ]
      );
      
    
      
      $this->add_control(
	'list',
	[
		'label' => __( 'Table Option List', 'wezido' ),
		'type' => Controls_Manager::REPEATER,
       
		'default' => [
			[
				'list_title' => __( 'Title #1', 'wezido' ),
			],
		],
		'fields' => [
			[
				'name' => 'list_title',
				'label' => __( 'Title', 'wezido' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'List Title' , 'wezido' ),
				'label_block' => true,
			],
            
            [
				'name' => 'list_icon',
				'label' => __( 'Icon', 'wezido' ),
				'type' => Controls_Manager::ICONS,
				
			],
			
			 [
				'name' => 'list_icon_color',
				'label' => __( 'Icon Color', 'wezido' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wezido_pricing_content ul li i' => 'color: {{VALUE}};',
				],
				
			],
			
		],
		'title_field' => '{{{ list_title }}}',
	]
);
       $this->add_control(
         'button_text',
         [
            'label' => __( 'Button Text', 'wezido' ),
            'type' => Controls_Manager::TEXT,
            'default' => '',
            'title' => __( 'Enter Button Text', 'wezido' ),
           
         ]
      );
       
       $this->add_control(
         'button_url',
         [
            'label' => __( 'Button Url', 'wezido' ),
            'type' => Controls_Manager::TEXT,
            'default' => 'https://teconce.com',
            'title' => __( 'Enter Button Url', 'wezido' ),
           
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
       
       $this->add_control(
         'icon-color',
         [
            'label' => __( 'Icon Color', 'mayosis' ),
            'type' => Controls_Manager::COLOR,
            'default' => '#666666',
            'title' => __( 'Select Icon Color', 'mayosis' ),
            
            'selectors' => [
					'{{WRAPPER}} .wezido_pricing_table h2 i' => 'color: {{VALUE}};',
				],
         ]
      );
       
        $this->add_control(
         'title-color',
         [
            'label' => __( 'Title Color', 'mayosis' ),
            'type' => Controls_Manager::COLOR,
            'default' => '#ffffff',
            'title' => __( 'Select Title Color', 'mayosis' ),
            'selectors' => [
					'{{WRAPPER}} .wezido_pricing_title h2' => 'color: {{VALUE}};',
				],
         ]
      );
       
       $this->add_control(
         'title-bg',
         [
            'label' => __( 'Title Background Color', 'mayosis' ),
            'type' => Controls_Manager::COLOR,
            'default' => '#c6c9cc',
            'title' => __( 'Select Title Color', 'mayosis' ),
            'selectors' => [
					'{{WRAPPER}} .wezido_pricing_title' => 'background: {{VALUE}};',
				],
         ]
      );
       $this->add_control(
         'amount-color',
         [
            'label' => __( 'Pricing Amount Color', 'mayosis' ),
            'type' => Controls_Manager::COLOR,
            'default' => '#1d314f',
            'title' => __( 'Select Pricing Amount Color', 'mayosis' ),
            
            'selectors' => [
					'{{WRAPPER}} .table_pricing_amount' => 'color: {{VALUE}}',
				],
         ]
      );
      
      $this->add_control(
         'content-color',
         [
            'label' => __( 'Pricing Content Color', 'mayosis' ),
            'type' => Controls_Manager::COLOR,
            'default' => '#1d314f',
            'title' => __( 'Select Pricing Content Color', 'mayosis' ),
            
            'selectors' => [
					'{{WRAPPER}} .wezido_pricing_content ul' => 'color: {{VALUE}}',
				],
         ]
      );
     	$this->start_controls_tabs( 'tabs_button_style' );
		
		    $this->start_controls_tab(
			'normal',
			[
				'label' => __( 'Normal', 'mayosis' ),
			]
		);
		      $this->add_control(
			'pricing_button_color',
			[
				'label' => __( 'Background Color', 'mayosis' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_1,
				],
				 'default' => '#1e3c78',
				'selectors' => [
					'{{WRAPPER}} .wezido_btn_blue_pricing' => 'background-color: {{VALUE}}',
					
						
				],
			]
		);
		
		 $this->add_control(
			'pricing_buttonborder_color',
			[
				'label' => __( 'Border Color', 'mayosis' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_1,
				],
				 'default' => '#1e3c78',
				'selectors' => [
					'{{WRAPPER}} .wezido_btn_blue_pricing' => 'border-color: {{VALUE}}',
					
						
				],
			]
		);
		
		 $this->add_control(
			'pricing_button_txt_color',
			[
				'label' => __( 'Text Color', 'mayosis' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_1,
				],
				 'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .wezido_btn_blue_pricing' => 'color: {{VALUE}}',
					
				],
			]
		);
		$this->end_controls_tab();
		
		
		
		  $this->start_controls_tab(
			'hover',
			[
				'label' => __( 'Hover', 'mayosis' ),
			]
		);
		      $this->add_control(
			'pricing_button_hover_color',
			[
				'label' => __( 'Background Color', 'mayosis' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_1,
				],
				 'default' => '#A10A8000',
				'selectors' => [
					'{{WRAPPER}} .wezido_btn_blue_pricing:hover' => 'background-color: {{VALUE}}',
					
						
				],
			]
		);
		
		 $this->add_control(
			'pricing_button_hoverborder_color',
			[
				'label' => __( 'Border Color', 'mayosis' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_1,
				],
				 'default' => 'rgb(30 60 120 / 0.5)',
				'selectors' => [
					'{{WRAPPER}} .wezido_btn_blue_pricing:hover' => 'border-color: {{VALUE}}',
					
						
				],
			]
		);
		
		 $this->add_control(
			'pricing_button_hover_txt_color',
			[
				'label' => __( 'Text Color', 'mayosis' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_1,
				],
				 'default' => 'rgb(30 60 120 / 0.5)',
				'selectors' => [
					'{{WRAPPER}} .wezido_btn_blue_pricing:hover' => 'color: {{VALUE}}',
					
				],
			]
		);
		$this->end_controls_tab();
			
			
		$this->end_controls_tabs();
		
		$this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                array(
                    'name'      => 'button_typography',
                    'label'     => __( 'Button Typography', 'mayosis' ),
                    'selector'  => '{{WRAPPER}} .wezido_btn_blue_pricing',
                )
            );  
            
            $this->add_control(
			'btn_border_radius',
			[
				'label' => __( 'Button Border Radius', 'plugin-domain' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .wezido_btn_blue_pricing' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
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
         $list = $this->get_settings( 'list' );

        
        ?>
             <div class="wezido_pricing_table">
        	<div class="wezido_pricing_title" >
				<h2><?php \Elementor\Icons_Manager::render_icon( $settings['icon'], [ 'aria-hidden' => 'true' ] ); ?> <?php echo $settings['title']; ?></h2>
			</div>
			
			<div class="wezido_pricing_content">
			    <div class="wezido_pricing_table_title_box">
				<h3 class="wezido_price_tag_table  table_pricing_amount"> <sub class="wezido_pricing_currency"><?php echo $settings['currency']; ?></sub> <?php echo $settings['price']; ?><span class="wezido_pricing_timeframe"><?php echo $settings['time']; ?></span></h3>
				</div>
			  
				
				<div class="wezido_main_price_content">
				<?php if ( $list ) {
                    echo '<ul>';
                    foreach ( $list as $index => $item  ) {
                         ?>
                        <li><?php \Elementor\Icons_Manager::render_icon( $item['list_icon'], [ 'aria-hidden' => 'true' ] ); ?> <?php echo $item['list_title'];?></li>
                        
                        
                    <?php }
                    echo '</ul>';
                          
                }?>
				</div>
				<a href="<?php echo $settings['button_url']; ?>" class="wezido_btn_blue_pricing"><?php echo $settings['button_text']; ?></a>
			</div>
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
