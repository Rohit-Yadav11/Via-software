<?php

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 
/**
 * @since 1.0.0
 */
class Wezido_flipbox extends Elementor\Widget_Base {

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
        return 'wezido-flipbox';
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
        return __( 'Flipbox', 'wezido' );
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
        return 'eicon-flip-box';
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
                'label' => __( 'Wezido Flipbox', 'wezido' ),
            ]
        );

       $this->add_control(
			'frontside',
			[
				'label' => __( 'Front Panel Data', 'wezido' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'frontside-title',
			[
				'label' => __( 'Title', 'wezido' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Frontside title', 'wezido' ),
				'label_block' => true,
				'placeholder' => __( 'Type your title here', 'wezido' ),
			]
		);
		$this->add_control(
			'frontside-description',
			[
				'label' => __( 'Description', 'wezido' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'rows' => 10,
				'default' => __( 'Default description', 'wezido' ),
				'placeholder' => __( 'Type your description here', 'wezido' ),
			]
		);
		
		$this->add_control(
			'frontside-icon',
			[
				'label' => __( 'Icon', 'wezido' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				
			]
		);
		
		
		 $this->add_control(
			'backside',
			[
				'label' => __( 'Back Panel Data', 'wezido' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		
		$this->add_control(
			'backside-title',
			[
				'label' => __( 'Title', 'wezido' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Backside title', 'wezido' ),
				'label_block' => true,
				'placeholder' => __( 'Type your title here', 'wezido' ),
			]
		);
		$this->add_control(
			'backside-description',
			[
				'label' => __( 'Description', 'wezido' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'rows' => 10,
				'default' => __( 'Default description', 'wezido' ),
				'placeholder' => __( 'Type your description here', 'wezido' ),
			]
		);
		$this->add_control(
			'backside-icon',
			[
				'label' => __( 'Icon', 'wezido' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				
			]
		);
		$this->add_control(
			'backside-btn-title',
			[
				'label' => __( 'Button Title', 'wezido' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Submit', 'wezido' ),
				'label_block' => true,
				'placeholder' => __( 'Type your title here', 'wezido' ),
			]
		);
		
		$this->add_control(
			'backside-btn-url',
			[
				'label' => __( 'Link', 'wezido' ),
				'type' => \Elementor\Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'wezido' ),
				'show_external' => true,
				'default' => [
					'url' => '',
					'is_external' => true,
					'nofollow' => true,
				],
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
			'hover_style',
			[
				'label' => __( 'Hover Style', 'wezido' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'side-flip',
				'options' => [
				    'side-flip' => __( 'Side Flip', 'wezido' ),
					'slide-flip'  => __( 'Slide Flip', 'wezido' ),
					'down-flip' => __( 'Down Flip', 'wezido' ),
					'up-flip' => __( 'Up Flip', 'wezido' ),
					'lift-flip' => __( 'Lift Flip', 'wezido' ),
					'diag-flip' => __( 'Diagonal Flip', 'wezido' ),
					'diag-inv-flip' => __( 'Diagonal Invert Flip', 'wezido' ),
				],
			]
		);
		
		$this->add_control(
			'border_radius_main',
			[
				'label' => __( 'Border Radius', 'wezido' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .wezido-flip-box .wezido-flip-inner .wezido-flip-front,.wezido-flip-box .wezido-flip-inner .wezido-flip-back' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        
        $this->end_controls_section();
        
        $this->start_controls_section(
			'fpanel_section',
			[
				'label' => __( 'Front Panel Style', 'wezido' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'frontside_background',
				'label' => __( 'Background', 'wezido' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .wezido-flip-box .wezido-flip-inner .wezido-flip-front',
			]
		);
		
		$this->add_control(
			'overlay_color',
			[
				'label' => __( 'Overlay Color', 'wezido' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .wezido-flip-box .wezido-flip-inner .wezido-flip-front .wezido-flip-front-content.fbg-overlay' => 'background: {{VALUE}}',
				],
			]
		);
		
		$this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                array(
                    'name'      => 'fpanel_typegrpahy',
                    'label'     => __( 'Title Typography', 'wezido' ),
                    'selector'  => '{{WRAPPER}} .wezido-flip-box .wezido-flip-inner .wezido-flip-front h2',
                )
            );  
		
		
		$this->add_control(
			'fapnel_title_color',
			[
				'label' => __( 'Title Color', 'wezido' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .wezido-flip-box .wezido-flip-inner .wezido-flip-front h2' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                array(
                    'name'      => 'fpanel_desc_typegrpahy',
                    'label'     => __( 'Description Typography', 'wezido' ),
                    'selector'  => '{{WRAPPER}} .wezido-flip-front-content p',
                )
            );  
		
		
		$this->add_control(
			'fapnel_description_title_color',
			[
				'label' => __( 'Description Color', 'wezido' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .wezido-flip-front-content p' => 'color: {{VALUE}}',
				],
			]
		);
		
		$this->add_control(
			'fpanel_icon_color',
			[
				'label' => __( 'Icon Color', 'wezido' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_1,
				],
					'selectors' => [
					'{{WRAPPER}}  .wezido-flip-front i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wezido-flip-front svg' => 'fill: {{VALUE}};',
				],
			]
		);
		
		$this->add_responsive_control(
			'fpanel_icon_size',
			[
				'label' => __( 'Icon Size', 'wezido' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .wezido-flip-front i' => 'font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);
			
		 $this->end_controls_section();
		 
		 $this->start_controls_section(
			'bpanel_section',
			[
				'label' => __( 'Back Panel Style', 'wezido' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'backside_background',
				'label' => __( 'Background', 'wezido' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .wezido-flip-box .wezido-flip-inner .wezido-flip-back',
			]
		);
		
		
		$this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                array(
                    'name'      => 'bpanel_typegrpahy',
                    'label'     => __( 'Title Typography', 'wezido' ),
                    'selector'  => '{{WRAPPER}} .wezido-flip-box .wezido-flip-inner .wezido-flip-back h3',
                )
            );  
		
		
		$this->add_control(
			'bapnel_title_color',
			[
				'label' => __( 'Title Color', 'wezido' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .wezido-flip-box .wezido-flip-inner .wezido-flip-back h3' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                array(
                    'name'      => 'bpanel_desc_typegrpahy',
                    'label'     => __( 'Description Typography', 'wezido' ),
                    'selector'  => '{{WRAPPER}} .wezido-flip-back-content p',
                )
            );  
		
		
		$this->add_control(
			'bapnel_description_title_color',
			[
				'label' => __( 'Description Color', 'wezido' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .wezido-flip-back-content p' => 'color: {{VALUE}}',
				],
			]
		);
		
			$this->add_control(
			'bpanel_icon_color',
			[
				'label' => __( 'Icon Color', 'wezido' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_1,
				],
					'selectors' => [
					'{{WRAPPER}} .wezido-flip-back-content i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wezido-flip-back-content svg' => 'fill: {{VALUE}};',
				],
			]
		);
		
		$this->add_responsive_control(
			'bapenl_icon_size',
			[
				'label' => __( 'Icon Size', 'wezido' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .wezido-flip-back-content i' => 'font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);
		$this->add_control(
			'backpanel_button',
			[
				'label' => __( 'Button Style', 'wezido' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->start_controls_tabs( 'tabs_button_style' );
		
		    $this->start_controls_tab(
			'normal',
			[
				'label' => __( 'Normal', 'wezido' ),
			]
		);
		      $this->add_control(
			'purchase_button_color',
			[
				'label' => __( 'Background Color', 'wezido' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_1,
				],
				 'default' => '#1e3c78',
				'selectors' => [
					'{{WRAPPER}} .wezido-flip-box .wezido-flip-inner .wezido-flip-back .wezido-flip-back-content .flip-btn' => 'background-color: {{VALUE}}',
					
						
				],
			]
		);
		
		 $this->add_control(
			'purchase_buttonborder_color',
			[
				'label' => __( 'Border Color', 'wezido' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_1,
				],
				 'default' => '#1e3c78',
				'selectors' => [
					'{{WRAPPER}} .wezido-flip-box .wezido-flip-inner .wezido-flip-back .wezido-flip-back-content .flip-btn' => 'border-color: {{VALUE}}',
					
						
				],
			]
		);
		
		 $this->add_control(
			'purchase_button_txt_color',
			[
				'label' => __( 'Text Color', 'wezido' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_1,
				],
				 'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .wezido-flip-box .wezido-flip-inner .wezido-flip-back .wezido-flip-back-content .flip-btn' => 'color: {{VALUE}}',
					
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
			'preview_button_color',
			[
				'label' => __( 'Background Color', 'wezido' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_1,
				],
				 'default' => '#A10A8000',
				'selectors' => [
					'{{WRAPPER}} .wezido-flip-box .wezido-flip-inner .wezido-flip-back .wezido-flip-back-content .flip-btn:hover' => 'background-color: {{VALUE}}',
					
						
				],
			]
		);
		
		 $this->add_control(
			'preview_buttonborder_color',
			[
				'label' => __( 'Border Color', 'wezido' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_1,
				],
				 'default' => 'rgb(30 60 120 / 0.5)',
				'selectors' => [
					'{{WRAPPER}} .wezido-flip-box .wezido-flip-inner .wezido-flip-back .wezido-flip-back-content .flip-btn:hover' => 'border-color: {{VALUE}}',
					
						
				],
			]
		);
		
		 $this->add_control(
			'preview_button_txt_color',
			[
				'label' => __( 'Text Color', 'wezido' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_1,
				],
				 'default' => 'rgb(30 60 120 / 0.5)',
				'selectors' => [
					'{{WRAPPER}} .wezido-flip-box .wezido-flip-inner .wezido-flip-back .wezido-flip-back-content .flip-btn:hover' => 'color: {{VALUE}}',
					
				],
			]
		);
		$this->end_controls_tab();
			
			
		$this->end_controls_tabs();
		
		
		$this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                array(
                    'name'      => 'fpanel_btn_typegrpahy',
                    'label'     => __( 'Button Typography', 'wezido' ),
                    'selector'  => '{{WRAPPER}} .wezido-flip-box .wezido-flip-inner .wezido-flip-back .wezido-flip-back-content .flip-btn',
                )
            );  
		
		$this->add_control(
			'btn_padding',
			[
				'label' => __( 'Button Padding', 'wezido' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .wezido-flip-box .wezido-flip-inner .wezido-flip-back .wezido-flip-back-content .flip-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_control(
			'btn_border_radius',
			[
				'label' => __( 'Button Border Radius', 'wezido' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .wezido-flip-box .wezido-flip-inner .wezido-flip-back .wezido-flip-back-content .flip-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
        $target = $settings['backside-btn-url']['is_external'] ? ' target="_blank"' : '';
		$nofollow = $settings['backside-btn-url']['nofollow'] ? ' rel="nofollow"' : '';
		$buttontext = $settings['backside-btn-title'];


        ?>
           <div class="wezido-flip-box flex-column <?php echo $settings['hover_style'];?>">
            <div class="wezido-flip-inner">
               <div class="wezido-flip-front">
                   
                  <div class='wezido-flip-front-content fbg-overlay'>
                      <?php 
                      if ($settings['frontside-icon']){
                      \Elementor\Icons_Manager::render_icon( $settings['frontside-icon'], [ 'aria-hidden' => 'true' ] ); 
                      }
                      ?>
                     <h2><?php echo $settings['frontside-title'];?></h2>
                     <p><?php echo $settings['frontside-description']; ?></p>
                  </div>
               </div>
               <div class="wezido-flip-back">
                  <div class='wezido-flip-back-content'>
                       <?php 
                      if ($settings['backside-icon']){
                      \Elementor\Icons_Manager::render_icon( $settings['backside-icon'], [ 'aria-hidden' => 'true' ] ); 
                      }
                      ?>
                     <h3><?php echo $settings['backside-title'];?></h3>
                     <p><?php echo $settings['backside-description']; ?></p>
                     <?php 
                     if ($settings['backside-btn-url']['url']){
                     echo '<a class="flip-btn" href="' . $settings['backside-btn-url']['url'] . '"' . $target . $nofollow . '> '.$buttontext.' </a>'; 
                     }
                     ?>
                  </div>
               </div>
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
