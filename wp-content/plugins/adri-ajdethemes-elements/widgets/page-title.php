<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use \Elementor\Core\Schemes;
use \Elementor\Core\Settings\Manager;

/**
 * Section Title - Ajdethemes Elementor Widget.
 *
 * @since 1.0.0
 */
class Ajdethemes_Widget_Page_Title extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve oEmbed widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'page-title';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve oEmbed widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Page Title', 'adri-ajdethemes-elements' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve oEmbed widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-site-title';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the oEmbed widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'ajdethemes-elements' ];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'title', 'site title', 'page title', 'heading' ];
	}
	

	/**
	 * Register oEmbed widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() {

		$this->start_controls_section(
			'section_page_title',
			[
				'label' => __( 'Page Title', 'adri-ajdethemes-elements' ),
			]
		);

		$this->add_control(
			'size',
			[
				'label'   => __( 'Select Size', 'adri-ajdethemes-elements' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => __( 'Small', 'adri-ajdethemes-elements' ),
					'pt-md' => __( 'Medium', 'adri-ajdethemes-elements' ),
					'pt-lg' => __( 'Large', 'adri-ajdethemes-elements' ),
					'custom-height'   => __( 'Custom Size', 'adri-ajdethemes-elements' ),
				],
			]
		);
		
		$this->add_control(
			'style',
			[
				'label'   => __( 'Select Style', 'adri-ajdethemes-elements' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1' => __( 'Style 1', 'adri-ajdethemes-elements' ),
					'style-2' => __( 'Style 2', 'adri-ajdethemes-elements' ),
				],
			]
        );
        
        $this->add_responsive_control(
			'custom_size',
			[
				'label' => __( 'Custom Size', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'default' => [
					'size' => 220,
				],
				'size_units' => [ 'px', 'vh', 'em' ],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 600,
					],
					'em' => [
						'min' => 0.1,
						'max' => 20,
					],
                ],
                'condition' => [
					'size' => 'custom-height',
				],
				'selectors' => [
					'{{WRAPPER}} .page-title.custom-height' => 'height: {{SIZE}}{{UNIT}}; align-items: center;',
				],
			]
		);


		$this->add_control(
			'title',
			[
				'label' => __( 'Title', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => __( 'Page title example', 'adri-ajdethemes-elements' ),
				'placeholder' => __( 'Enter your title', 'adri-ajdethemes-elements' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'subtitle',
			[
				'label' => __( 'Subtitle', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => __( 'Subtitle here', 'adri-ajdethemes-elements' ),
				'placeholder' => __( 'Enter your subtitle', 'adri-ajdethemes-elements' ),
				'label_block' => true,
			]
        );
        
        $this->add_responsive_control(
			'align',
			[
				'label' => __( 'Text Alignment', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'adri-ajdethemes-elements' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'adri-ajdethemes-elements' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'adri-ajdethemes-elements' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'left',
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
			]
        );

        $this->add_control(
			'title_size',
			[
				'label' => __( 'Title HTML Tag', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
				],
				'default' => 'h1',
			]
        );

        $this->end_controls_section();
        
        $this->start_controls_section(
			'section_style_content',
			[
				'label' => __( 'Style', 'adri-ajdethemes-elements' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
        );
        
        $this->add_control(
			'title_color',
			[
				'label' => __( 'Title Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .page-title .entry-title' => 'color: {{VALUE}};',
				],
			]
		);
        
        $this->add_control(
			'subtitle_color',
			[
				'label' => __( 'Subtitle Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .page-title .subtitle' => 'color: {{VALUE}};',
				],
			]
		);
        
        $this->add_control(
			'background_color',
			[
				'label' => __( 'Background Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .page-title' => 'background-color: {{VALUE}};',
				],
			]
        );

        $this->add_control(
			'has_bg_image',
			[
				'label' => __( 'Add Background Image', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => __( 'On', 'adri-ajdethemes-elements' ),
				'label_off' => __( 'Off', 'adri-ajdethemes-elements' ),
			]
		);
        
        $this->add_control(
			'bg_image',
			[
				'label' => __( 'Background Image', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
                ],
                'condition' => [
					'has_bg_image' => 'yes',
				],
				'default' => [
					'url' => 'https://placehold.it/1600x600',
                ],
			]
        );
        
        $this->add_control(
			'bg_image_overlay',
			[
				'label' => __( 'Overlay', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
                ],
                'condition' => [
					'has_bg_image' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .page-title.pt-has-bg-img:before' => 'background: {{VALUE}};',
				],
			]
        );

        $this->end_controls_section();
	}

	/**
	 * Render oEmbed widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		// Title
        $this->add_render_attribute( 'title', 'class', 'entry-title' );
        $this->add_inline_editing_attributes( 'title' );
        // Subtitle
        $this->add_render_attribute( 'subtitle', 'class', 'subtitle' );
        $this->add_inline_editing_attributes( 'subtitle' );

        $pt_bg_img = sprintf( 'style="background-image: url(%s);"', $settings['bg_image']['url'] );

        
        $html = '';
        $html .= sprintf( '<div class="page-title %1$s %2$s" %3$s>', 
                    $settings['size'], 
                    $settings['bg_image']['url'] ? 'pt-has-bg-img' : '', 
                    $settings['bg_image']['url'] ? $pt_bg_img : ''
                );
        
        $html .= '<div class="container">';
        $html .= '<div class="row">';
		$html .= '<div class="col-lg-12">';
		
		if ( $settings['style'] === 'style-2' ) {

			$html .= '<div class="pt-style-2">';
			$html .= $settings['subtitle'] ? sprintf( '<span %2$s>%1$s</span>', $settings['subtitle'], $this->get_render_attribute_string( 'subtitle' ) ) : '';			
			$html .= sprintf( '<%3$s %2$s>%1$s</%3$s>', $settings['title'], $this->get_render_attribute_string( 'title' ), $settings['title_size'] );
			$html .= '</div>';

		} else {

			$html .= sprintf( '<%3$s %2$s>%1$s</%3$s>', $settings['title'], $this->get_render_attribute_string( 'title' ), $settings['title_size'] );
			$html .= $settings['subtitle'] ? sprintf( '<span %2$s>%1$s</span>', $settings['subtitle'], $this->get_render_attribute_string( 'subtitle' ) ) : '';

		}

        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '</div>';

        echo $html;
    }
    
    /**
	 * Render image box widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 2.9.0
	 */
    protected function content_template() {}
}