<?php

use Elementor\Controls_Manager;
use Elementor\Scheme_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 
/**
 * @since 1.0.0
 */
class Wezido_Title extends Elementor\Widget_Base {

 
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
        return 'wezido-title';
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
        return __( 'Heading', 'wezido' );
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
        return 'eicon-heading';
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
                'label' => __( 'Wezido Heading', 'wezido' ),
            ]
        );

         $this->add_control(
                'wezido_title_html_tag',
                [
                    'label'   => __( 'Title HTML Tag', 'mayosis' ),
                    'type'    => Controls_Manager::SELECT,
                    'options' => wezido_html_tag_lists(),
                    'default' => 'h1',
                ]
            ); 
       $this->add_control(
			'widget_title',
			[
				'label' => __( 'Title', 'wezido' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Default title', 'wezido' ),
				'placeholder' => __( 'Type your title here', 'wezido' ),
			]
		);
		 
     
        $this->end_controls_section();
        
        $this->start_controls_section(
			'style_section',
			[
				'label' => __( 'Style', 'plugin-name' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		
		$this->add_responsive_control(
                'wezido_title_align',
                [
                    'label'        => __( 'Alignment', 'mayosis' ),
                    'type'         => Controls_Manager::CHOOSE,
                    'options'      => [
                        'left'   => [
                            'title' => __( 'Left', 'mayosis' ),
                            'icon'  => 'fa fa-align-left',
                        ],
                        'center' => [
                            'title' => __( 'Center', 'mayosis' ),
                            'icon'  => 'fa fa-align-center',
                        ],
                        'right'  => [
                            'title' => __( 'Right', 'mayosis' ),
                            'icon'  => 'fa fa-align-right',
                        ],
                    ],
                    'prefix_class' => 'elementor-align-%s',
                    'default'      => 'left',
                ]
            );
            
          
            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                array(
                    'name'      => 'wezido_title_typography',
                    'label'     => __( 'Typography', 'mayosis' ),
                    'selector'  => '{{WRAPPER}} .wezido_title',
                )
            );     
            
             $this->add_control(
                'title_color',
                [
                    'label'     => __( 'Title Color', 'mayosis' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .wezido_title' => 'color: {{VALUE}} !important;',
                    ],
                ]
            );
            
            
             $this->add_responsive_control(
                'wezido_title_margin',
                [
                    'label' => __( 'Margin', 'mayosis' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .wezido_title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                    ],
                    'separator' => 'before',
                ]
            );
		
		    $this->add_control(
			'heading_style',
			[
				'label' => __( 'Heading Style', 'wezido' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'style-one',
				'options' => [
					'style-one'  => __( 'Style One', 'wezido' ),
					'style-two' => __( 'Style Two', 'wezido' ),
					'style-three' => __( 'Style Three', 'wezido' ),
					'none' => __( 'None', 'wezido' ),
				],
			]
		);
		
		$this->add_control(
                'style_one_border_color',
                [
                    'label'     => __( 'Border Color', 'mayosis' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .wezido-section-title.wezido-title-style-one' => 'border-color: {{VALUE}} !important;',
                        
                        '{{WRAPPER}} .wezido-section-title.wezido-title-style-two:before,
                        {{WRAPPER}} .wezido-section-title.wezido-title-style-three:before' => 'background: {{VALUE}} !important;',
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
           <div class="wezido-heading">
               <?php echo sprintf( "<%s class='wezido_title wezido-section-title wezido-title-%s'>%s</%s>", $settings['wezido_title_html_tag'], $settings['heading_style'],$settings['widget_title'], $settings['wezido_title_html_tag']  ); ?>
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
