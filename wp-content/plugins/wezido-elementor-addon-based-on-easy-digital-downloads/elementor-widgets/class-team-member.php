<?php

use Elementor\Controls_Manager;
use Elementor\Scheme_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 
/**
 * @since 1.0.0
 */
class Wezido_Team extends Elementor\Widget_Base {

    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);
	    wp_register_script( 'wezido-team', plugin_dir_url( __FILE__ ) . 'js/wezido-team.js', ['elementor-frontend'], 1.0 , true );
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
        return 'wezido-team';
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
        return __( 'Team Member', 'wezido' );
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
        return 'eicon-user-circle-o';
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
                'label' => __( 'Wezido Team Member', 'wezido' ),
            ]
        );
        
        $this->add_control(
			'image',
			[
				'label' => __( 'Choose Team Member Image', 'wezido' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
			]
		);

       $this->add_control(
			'widget_title',
			[
				'label' => __( 'Member Name', 'wezido' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Default title', 'wezido' ),
				'placeholder' => __( 'Type your title here', 'wezido' ),
				'label_block' => true,
			]
		);
		
		$this->add_control(
			'team_designation',
			[
				'label' => __( 'Member Designation', 'wezido' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Default Designation', 'wezido' ),
				'label_block' => true,
				'placeholder' => __( 'Type your designation here', 'wezido' ),
			]
		);
		
		$this->add_control(
			'team_description',
			[
				'label' => __( 'Description', 'wezido' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'rows' => 10,
				'default' => __( 'Default description', 'wezido' ),
				'placeholder' => __( 'Type your description here', 'wezido' ),
			]
		);
		 
     $repeater = new \Elementor\Repeater();

	
		
		$repeater->add_control(
			'icon',
			[
				'label' => __( 'Icon', 'wezido' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-star',
					'library' => 'solid',
				],
			]
		);
		
		$repeater->add_control(
			'link',
			[
				'label' => __( 'Link', 'wezido' ),
				'type' => Controls_Manager::URL,
				'default' => [
					'is_external' => 'true',
				],
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'https://your-link.com', 'wezido' ),
			]
		);
		
		$repeater->add_control(
			'item_icon_color',
			[
				'label' => __( 'Color', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __( 'Official Color', 'elementor' ),
					'custom' => __( 'Custom', 'elementor' ),
				],
			]
		);

		$repeater->add_control(
			'item_icon_primary_color',
			[
				'label' => __( 'Primary Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'item_icon_color' => 'custom',
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.elementor-social-icon' => 'background-color: {{VALUE}};',
				],
			]
		);

		$repeater->add_control(
			'item_icon_secondary_color',
			[
				'label' => __( 'Secondary Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'item_icon_color' => 'custom',
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.elementor-social-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} {{CURRENT_ITEM}}.elementor-social-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'member_social',
			[
				'label' => __( 'Member Social', 'wezido' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'icon' => [
							'value' => 'fab fa-facebook',
							'library' => 'fa-brands',
						],
					],
					
				],
				'title_field' => '<# var migrated = "undefined" !== typeof __fa4_migrated, social = ( "undefined" === typeof social ) ? false : social; #>{{{ elementor.helpers.getSocialNetworkNameFromIcon( icon, social, true, migrated, true ) }}}',
			]
		);
        $this->end_controls_section();
        
        $this->start_controls_section(
			'style_section',
			[
				'label' => __( 'Style', 'wezido' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		
		$this->add_responsive_control(
                'Wezido_Team_align',
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
                    'name'      => 'Wezido_Team_typography',
                    'label'     => __( 'Member Name Typography', 'mayosis' ),
                    'selector'  => '{{WRAPPER}} .wezido-team-title',
                )
            );     
            
             $this->add_control(
                'title_color',
                [
                    'label'     => __( 'Member Name Color', 'mayosis' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .wezido-team-title' => 'color: {{VALUE}} !important;',
                    ],
                ]
            );
            
            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                array(
                    'name'      => 'Wezido_Team_deg_typography',
                    'label'     => __( 'Member Designation Typography', 'mayosis' ),
                    'selector'  => '{{WRAPPER}} .wezido-team-desig',
                )
            );     
            
             $this->add_control(
                'deg_color',
                [
                    'label'     => __( 'Member Designation Color', 'mayosis' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .wezido-team-desig' => 'color: {{VALUE}} !important;',
                    ],
                ]
            );
            
            
           
		
		    $this->add_control(
			'member_style',
			[
				'label' => __( 'Member Style', 'wezido' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'style-one',
				'options' => [
					'style-one'  => __( 'Style One', 'wezido' ),
					'style-two' => __( 'Style Two', 'wezido' ),
					'style-three' => __( 'Style Three', 'wezido' ),
					
				],
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'background',
				'label' => __( 'Background', 'wezido' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .wezido-team-style-one .image-style-one-caption,{{WRAPPER}} .wezido-team.wezido-team-style-two .wezido-team-caption,{{WRAPPER}} .wezido-team-style-three .wezido-team-caption,{{WRAPPER}} .wezido-team-style-three .wezido-team-caption:before',
			]
		);
		$this->add_control(
			'team-b-radius',
			[
				'label' => __( 'Image Border Radius', 'wezido' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'condition' => [ 'member_style' => 'style-one' ],
				'selectors' => [
					'{{WRAPPER}} .wezido-team-member-image,{{WRAPPER}} .wezido-team-style-one .image-style-one-caption' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'social_icon_bg_s3',
			[
				'label' => __( 'Social Background', 'plugin-domain' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_1,
				],
				'condition' => [ 'member_style' => 'style-three' ],
				'selectors' => [
					'{{WRAPPER}} .wezido-team-style-three .wezido-team-caption .image-style-two-caption .wezido-team-social-ul' => 'background: {{VALUE}}',
				],
			]
		);
	$this->add_control(
			'team_desc_color',
			[
				'label' => __( 'Description Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'condition' => [
					'icon_color' => 'custom',
				],
				'selectors' => [
					'{{WRAPPER}} .wezido-team-desc' => 'color: {{VALUE}};',
				],
			]
		);
        
		$this->end_controls_section();
		
		
		
		$this->start_controls_section(
			'section_social_style',
			[
				'label' => __( 'Icon', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		
		
		$this->add_control(
			'shape',
			[
				'label' => __( 'Shape', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'rounded',
				'options' => [
					'rounded' => __( 'Rounded', 'elementor' ),
					'square' => __( 'Square', 'elementor' ),
					'circle' => __( 'Circle', 'elementor' ),
				],
				'prefix_class' => 'elementor-shape-',
			]
		);
		
		$start = is_rtl() ? 'end' : 'start';
		$end = is_rtl() ? 'start' : 'end';

		$this->add_responsive_control(
			'align',
			[
				'label' => __( 'Alignment', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'flex-start'    => [
						'title' => __( 'Left', 'elementor' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'elementor' ),
						'icon' => 'eicon-text-align-center',
					],
					'flex-end' => [
						'title' => __( 'Right', 'elementor' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'prefix_class' => 'e-grid-align-',
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .wezido-team-social-ul' => 'justify-content: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label' => __( 'Color', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __( 'Official Color', 'elementor' ),
					'custom' => __( 'Custom', 'elementor' ),
				],
			]
		);

		$this->add_control(
			'icon_primary_color',
			[
				'label' => __( 'Primary Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'icon_color' => 'custom',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-social-icon' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_secondary_color',
			[
				'label' => __( 'Secondary Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'icon_color' => 'custom',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-social-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-social-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => __( 'Size', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--icon-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'icon_padding',
			[
				'label' => __( 'Padding', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .elementor-social-icon' => '--icon-padding: {{SIZE}}{{UNIT}}',
				],
				'default' => [
					'unit' => 'em',
				],
				'tablet_default' => [
					'unit' => 'em',
				],
				'mobile_default' => [
					'unit' => 'em',
				],
				'range' => [
					'em' => [
						'min' => 0,
						'max' => 5,
					],
				],
			]
		);

		$this->add_responsive_control(
			'icon_spacing',
			[
				'label' => __( 'Spacing', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 5,
				],
				'selectors' => [
					'{{WRAPPER}} .wezido-team-social-ul li' => 'margin: {{SIZE}}{{UNIT}}',
				],
			]
		);

	

		$this->add_group_control(
			\Elementor\Group_Control_border::get_type(),
			[
				'name' => 'image_wezidoer', // We know this mistake - TODO: 'icon_wezidoer' (for hover control condition also)
				'selector' => '{{WRAPPER}} .elementor-social-icon',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'wezidoer_radius',
			[
				'label' => __( 'wezidoer Radius', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon' => 'wezidoer-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_social_hover',
			[
				'label' => __( 'Icon Hover', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'hover_primary_color',
			[
				'label' => __( 'Primary Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'condition' => [
					'icon_color' => 'custom',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-social-icon:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_secondary_color',
			[
				'label' => __( 'Secondary Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'condition' => [
					'icon_color' => 'custom',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-social-icon:hover i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-social-icon:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_wezidoer_color',
			[
				'label' => __( 'wezidoer Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'condition' => [
					'image_wezidoer_wezidoer!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-social-icon:hover' => 'wezidoer-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_animation',
			[
				'label' => __( 'Hover Animation', 'elementor' ),
				'type' => Controls_Manager::HOVER_ANIMATION,
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
        $team_style = $settings['member_style'];
       
        $class_animation = '';
        
        		if ( ! empty( $settings['hover_animation'] ) ) {
        			$class_animation = ' elementor-animation-' . $settings['hover_animation'];
        		}
        		

        ?>
           <div class="wezido-team wezido-team-<?php echo $team_style;?>">
               <div class="wezido-team-member-image">
                   <?php echo '<img src="' . $settings['image']['url'] . '" class="wezido-team-image">';?>
                   <?php if ($team_style=='style-one'){?>
                       <div class="image-style-one-caption">
                           <p class="wezido-team-desc"><?php echo $settings['team_description'];?></p>
                           
                         <?php
                           if ( $settings['member_social'] ) { ?>
                			<ul class="wezido-team-social-ul">
                			<?php foreach (  $settings['member_social'] as $index => $item ) {
                			    
                			    $link_key = 'link_' . $index;
                                $social = $item['icon']['value'];
                                 $item_icon_color = $item['item_icon_color'];
                                 
                				$this->add_render_attribute( $link_key, 'class', [
                					'elementor-icon',
                					'elementor-social-icon',
                					'elementor-social-icon-' . str_replace(array('fab',' ','fa-'),array('','',''),$social) . $class_animation,
                					'elementor-repeater-item-' . $item['_id'],'wezido-'.$item_icon_color.''
                				] );
                
                				$this->add_link_attributes( $link_key, $item['link'] );
    							?>
                				<li class="wezido-social-items"><a <?php echo $this->get_render_attribute_string( $link_key ) ?>><?php \Elementor\Icons_Manager::render_icon( $item['icon'], [ 'aria-hidden' => 'true' ] ); ?></a></li>
                			
                			<?php } ?>
                			</ul>
                		<?php } ?>
                     </div>
                   <?php } ?>
               </div>
               <div class="wezido-team-caption">
                   <h3 class="wezido-team-title"><?php echo $settings['widget_title']; ?></h3>
                   <p class="wezido-team-desig"><?php echo $settings['team_designation']; ?></p>
                   
                   <?php if ($team_style=='style-two'|| $team_style=='style-three'){?>
                       <div class="image-style-two-caption">
                           <p class="wezido-team-desc"><?php echo $settings['team_description'];?></p>
                           
                         <?php
                           if ( $settings['member_social'] ) { ?>
                			<ul class="wezido-team-social-ul">
                			<?php foreach (  $settings['member_social'] as $index => $item ) {
                			    
                			    $link_key = 'link_' . $index;
                                $social = $item['icon']['value'];
                                 $item_icon_color = $item['item_icon_color'];
                                 
                				$this->add_render_attribute( $link_key, 'class', [
                					'elementor-icon',
                					'elementor-social-icon',
                					'elementor-social-icon-' . str_replace(array('fab',' ','fa-'),array('','',''),$social) . $class_animation,
                					'elementor-repeater-item-' . $item['_id'],'wezido-'.$item_icon_color.''
                				] );
                
                				$this->add_link_attributes( $link_key, $item['link'] );
    							?>
                				<li class="wezido-social-items"><a <?php echo $this->get_render_attribute_string( $link_key ) ?>><?php \Elementor\Icons_Manager::render_icon( $item['icon'], [ 'aria-hidden' => 'true' ] ); ?></a></li>
                			
                			<?php } ?>
                			</ul>
                		<?php } ?>
                     </div>
                   <?php } ?>
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

    public function get_script_depends() {
        return array('wezido-team');
    }
}
