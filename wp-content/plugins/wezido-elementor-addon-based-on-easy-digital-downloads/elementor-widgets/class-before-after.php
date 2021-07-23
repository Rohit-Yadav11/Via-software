<?php

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 
/**
 * @since 1.0.0
 */
class Wezido_Before_after extends Elementor\Widget_Base {

 
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
        return 'wezido-before-after';
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
        return __( 'Before After', 'wezido' );
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
        return 'eicon-image-before-after';
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
                'label' => __( 'Wezido Before After', 'wezido' ),
            ]
        );

     $this->add_control(
			'before_image',
			[
				'label' => __( 'Before Image', 'mayosis' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
				
			]
		);
       
       
       $this->add_control(
			'after_image',
			[
				'label' => __( 'After Image', 'mayosis' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
				
			]
		);
		
		
		$this->add_control(
			'before_title',
			[
				'label' => __( 'Before Image Title', 'mayosis' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'before', 'mayosis' ),
				'placeholder' => __( 'Type your text here', 'mayosis' ),
				
			]
		);
		
		$this->add_control(
			'after_title',
			[
				'label' => __( 'After Image Title', 'mayosis' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'after', 'mayosis' ),
				'placeholder' => __( 'Type your text here', 'mayosis' ),
				
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
			'border_radius_main',
			[
				'label' => __( 'Border Radius', 'wezido' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .beer-slider' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_control(
			'tooltip_color',
			[
				'label' => __( 'Tooltip Background Color', 'wezido' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_1,
				],
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .beer-slider[data-beer-label]:after, .beer-reveal[data-beer-label]:after' => 'background: {{VALUE}}',
				],
			]
		);
		
		$this->add_control(
			'tooltip_txt_color',
			[
				'label' => __( 'Tooltip Text Color', 'wezido' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_1,
				],
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .beer-slider[data-beer-label]:after, .beer-reveal[data-beer-label]:after' => 'color: {{VALUE}}',
				],
			]
		);
		
		$this->add_control(
			'handle_color',
			[
				'label' => __( 'Handle Background Color', 'wezido' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_1,
				],
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .beer-handle' => 'background: {{VALUE}}',
				],
			]
		);
		
		$this->add_control(
			'handle_txt_color',
			[
				'label' => __( 'Handle Text Color', 'wezido' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_1,
				],
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .beer-handle' => 'color: {{VALUE}}',
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


        
        ?>
            <div id="wezido-before-after" class="beer-slider" data-beer-label="<?php echo esc_html($settings['before_title']);?>">
                        <?php echo wp_get_attachment_image( $settings['before_image']['id'], 'full' ); ?>
                        <div class="beer-reveal" data-beer-label="<?php echo esc_html($settings['after_title']);?>">
                         <?php echo wp_get_attachment_image( $settings['after_image']['id'], 'full' ); ?>
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
