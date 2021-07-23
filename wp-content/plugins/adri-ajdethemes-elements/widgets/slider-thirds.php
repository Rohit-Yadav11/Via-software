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
class Ajdethemes_Widget_Thirds_Slider extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve oEmbed widget name.
	 * 
	 * Note: overwrite the default elementor counter element
	 * and uses is JS lib.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'adri-ajdethemes-elements-slider-thirds';
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
		return __( 'Slider Thirds', 'adri-ajdethemes-elements' );
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
		return 'eicon-post-slider';
	}

	/**
	 * Retrieve the list of scripts the counter widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return [ 'Swiper' ];
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
		return [ 'hero', 'header', 'slider', 'image', 'carousel' ];
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
			'section_counter',
			[
				'label' => __( 'Thirds Slider', 'adri-ajdethemes-elements' ),
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'image',
			[
				'label' => __( 'Choose Image', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => 'https://placehold.it/800x950',
				],
			]
		);

		$repeater->add_control(
			'slide_nbr',
			[
				'label' => __( 'Slide Number', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '01',
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'title',
			[
				'label' => __( 'Title', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Title here', 'adri-ajdethemes-elements' ),
				'label_block' => true,
			]
		);
		
		$repeater->add_control(
			'subtitle',
			[
				'label' => __( 'Subtitle', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Subtitle', 'adri-ajdethemes-elements' ),
				'label_block' => true,
			]
		);
		
		$repeater->add_control(
			'description',
			[
				'label' => __( 'Description', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'Enter a description here...', 'adri-ajdethemes-elements' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'link_on',
			[
				'label' => __( 'Show link/button', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => __( 'On', 'adri-ajdethemes-elements' ),
				'label_off' => __( 'Off', 'adri-ajdethemes-elements' ),
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'link',
			[
				'label' => __( 'Add Link URL', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::URL,
				'placeholder' => __( 'https://your-project-page.com', 'adri-ajdethemes-elements' ),
				'condition' => [
					'link_on' => 'yes',
				],
			]
		);
		
		$repeater->add_control(
			'link_text',
			[
				'label' => __( 'Link/Button Text', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Learn More', 'adri-ajdethemes-elements' ),
				'label_block' => true,
				'condition' => [
					'link_on' => 'yes',
				],
			]
		);

		$this->add_control(
			'slides',
			[
				'label' => __( 'Slides', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'image' => 'https://placehold.it/800x950',
						'slide_nbr' => '01',
						'title' => __( 'Business consulting', 'adri-ajdethemes-elements' ),
						'subtitle' => __( 'We offer', 'adri-ajdethemes-elements' ),
						'description' => __( 'Velit consequat eu cupidatat irure qui dolore qui consectetur in non culpa labore duis. Velit ad nisi dolore fugiat pariatur consequat.', 'adri-ajdethemes-elements' ),
						'link_on' => 'yes',
						'link_text' => 'Learn More',
					],					
					[
						'image' => 'https://placehold.it/800x950',
						'slide_nbr' => '02',
						'title' => __( 'Strategy & Management', 'adri-ajdethemes-elements' ),
						'subtitle' => __( 'We offer', 'adri-ajdethemes-elements' ),
						'description' => __( 'Velit consequat eu cupidatat irure qui dolore qui consectetur in non culpa labore duis. Velit ad nisi dolore fugiat pariatur consequat.', 'adri-ajdethemes-elements' ),
						'link_on' => 'yes',
						'link_text' => 'Learn More',
					],
					[
						'image' => 'https://placehold.it/800x950',
						'slide_nbr' => '03',
						'title' => __( 'Finance & Investing', 'adri-ajdethemes-elements' ),
						'subtitle' => __( 'We offer', 'adri-ajdethemes-elements' ),
						'description' => __( 'Velit consequat eu cupidatat irure qui dolore qui consectetur in non culpa labore duis. Velit ad nisi dolore fugiat pariatur consequat.', 'adri-ajdethemes-elements' ),
						'link_on' => 'yes',
						'link' => '#',
						'link_text' => 'Learn More',
					],
					
				],
				'title_field' => '{{{ title }}}',
			]
        );

		$this->end_controls_section();

		$this->start_controls_section(
			'style',
			[
				'label' => __( 'Neue Slider', 'adri-ajdethemes-elements' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Title Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_2,
				],
				'selectors' => [
					'{{WRAPPER}} .slider-thirds .s-item .s-item-content .title' => 'color: {{VALUE}};',
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
					'value' => Schemes\Color::COLOR_2,
				],
				'selectors' => [
					'{{WRAPPER}} .slider-thirds .s-item .s-item-content .subtitle' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'description_color',
			[
				'label' => __( 'Description Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_2,
				],
				'selectors' => [
					'{{WRAPPER}} .slider-thirds .s-item .s-item-content .description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label' => __( 'Button Text Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_2,
				],
				'selectors' => [
					'{{WRAPPER}} .slider-thirds .s-item .s-item-footer button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .slider-thirds .s-item .s-item-content .s-item-cta .ft-btn' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_accent_color',
			[
				'label' => __( 'Button Accent Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_2,
				],
				'selectors' => [
					'{{WRAPPER}} .slider-thirds .btn-txt-arr .arr-box' => 'background: {{VALUE}};',
					'{{WRAPPER}} .slider-thirds .s-item .s-item-footer button > span' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'slider_nbr_color',
			[
				'label' => __( 'Slider Number Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_2,
				],
				'selectors' => [
					'{{WRAPPER}} .slider-thirds .s-item .s-item-header .s-item-nbr' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'close_button_color',
			[
				'label' => __( 'Close Button Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_2,
				],
				'selectors' => [
					'{{WRAPPER}} .slider-thirds .s-item .s-item-header .s-item-btn-close:after' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'open_button_accent_content_color',
			[
				'label' => __( 'Open Button "+" color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_2,
				],
				'selectors' => [
					'{{WRAPPER}} .slider-thirds .s-item .s-item-footer button > span span' => 'color: {{VALUE}};',
					'{{WRAPPER}} .slider-thirds .s-item .s-item-footer button > span span:before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'overlay_bg',
			[
				'label' => __( 'Overlay Background', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_2,
				],
				'selectors' => [
					'{{WRAPPER}} .slider-thirds .s-item .s-item-footer' => 'background: {{VALUE}};',
					'{{WRAPPER}} .slider-thirds .s-item .s-item-content:before' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'primary_color',
			[
				'label' => __( 'Primary Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .slider-thirds .s-item .s-item-header .s-item-nbr' => 'color: {{VALUE}};',
					'{{WRAPPER}} .slider-thirds .s-item .s-item-header .s-item-btn-close:hover:after' => 'background: {{VALUE}};',
					'{{WRAPPER}} .slider-thirds .s-item .s-item-content .subtitle' => 'color: {{VALUE}};',
					'{{WRAPPER}} .slider-thirds .btn-txt-arr .arr-box' => 'background: {{VALUE}};',
					'{{WRAPPER}} .slider-thirds .s-item .s-item-footer button > span' => 'background: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'secondary_color',
			[
				'label' => __( 'Secondary Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .slider-thirds .s-item .s-item-footer button > span span' => 'background: {{VALUE}};',
					'{{WRAPPER}} .slider-thirds .s-item .s-item-footer button > span span:before' => 'background: {{VALUE}};',
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

		$html = '<div class="slider-thirds swiper-container">';
		$html .= '<div class="swiper-wrapper">';

		foreach ( $settings['slides'] as $index => $item ) {

			$title_setting_key = $this->get_repeater_setting_key( 'title', 'slides', $index );
			$subtitle_setting_key = $this->get_repeater_setting_key( 'subtitle', 'slides', $index );
			$description_setting_key = $this->get_repeater_setting_key( 'description', 'slides', $index );

			$this->add_render_attribute( $title_setting_key, [
                'class' => [ 'title' ],
            ] );
			
			$this->add_render_attribute( $subtitle_setting_key, [
                'class' => [ 'subtitle' ],
			] );
			
			$this->add_render_attribute( $description_setting_key, [
                'class' => [ 'description' ],
			] );

			$target = $item['link']['is_external'] ? ' target="_blank"' : '';
			$nofollow = $item['link']['nofollow'] ? ' rel="nofollow"' : '';

			$html .= '<div class="s-item swiper-slide">';
			$html .= sprintf( '<img src="%1$s" alt="%2$s">', $item['image']['url'], $item['title'] );
			$html .= '<div class="s-item-content">';

			$html .= '<div class="s-item-header">';
			$item['slide_nbr'] ? $html .= sprintf( '<span class="s-item-nbr">%s</span>', $item['slide_nbr'] ) : $html .= '';
			$html .= '<button class="s-item-btn-close"></button>';
			$html .= '</div>';

			$html .= '<div class="s-item-text">';
			$item['subtitle'] ? $html .= sprintf( '<span %2$s>%1$s</span>', $item['subtitle'], $this->get_render_attribute_string( $subtitle_setting_key ) ) : $html .= '';
			$html .= sprintf( '<h3 %2$s>%1$s</h3>', $item['title'], $this->get_render_attribute_string( $title_setting_key ) );
			$item['description'] ? $html .= sprintf( '<p %2$s>%1$s</p>', $item['description'], $this->get_render_attribute_string( $description_setting_key ) ) : $html .= '';
			$html .= '</div>';
			
			if ( $item['link_on'] ) {

				$html .= '<div class="s-item-cta">';
				$html .= sprintf( '<a href="%2$s" class="ft-btn btn-txt-arr" %3$s %4$s>%1$s <span class="arr-box"><i class="icon-Arrow-OutRight"></i></span></a>', $item['link_text'], $item['link']['url'], $target, $nofollow );
				$html .= '</div><!-- End of .s-item-cta -->';

			}

			$html .= '</div><!-- End of .s-item-content -->';

			$html .= '<div class="s-item-footer">';
			$html .= sprintf( '<button>%1$s <span class="plus-btn"><span></span></span></button>', $item['title'] );
			$html .= '</div><!-- End of .s-item-footer -->';
			
			$html .= '</div><!-- End of .s-item -->';
		}

		$html .= '</div><!-- End of .swiper-wrapper -->';
		$html .= '</div><!-- End of .slider-thirds -->';
				
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