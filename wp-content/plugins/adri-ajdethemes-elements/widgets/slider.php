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
class Ajdethemes_Widget_Image_Slider extends \Elementor\Widget_Base {

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
		return 'adri-ajdethemes-elements-slider';
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
		return __( 'Slider', 'adri-ajdethemes-elements' );
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
		return [ 'slider', 'image', 'carousel' ];
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
				'label' => __( 'Image Slider', 'adri-ajdethemes-elements' ),
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'image',
			[
				'label' => __( 'Choose Image', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => 'https://placehold.it/900x590',
				],
			]
		);

		$repeater->add_control(
			'title',
			[
				'label' => __( 'Title', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Project title here', 'adri-ajdethemes-elements' ),
				'label_block' => true,
			]
		);
		
		$repeater->add_control(
			'subtitle',
			[
				'label' => __( 'Subtitle', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Consulting', 'adri-ajdethemes-elements' ),
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
			]
		);
		
		$repeater->add_control(
			'subtitle',
			[
				'label' => __( 'Subtitle', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Consulting', 'adri-ajdethemes-elements' ),
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'link_on',
			[
				'label' => __( 'Image has link', 'adri-ajdethemes-elements' ),
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
				'label' => __( 'Add Link', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::URL,
				'placeholder' => __( 'https://your-project-page.com', 'adri-ajdethemes-elements' ),
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
						'image' => __( 'https://placehold.it/900x590', 'adri-ajdethemes-elements' ),
						'title' => __( 'Slide item 1', 'adri-ajdethemes-elements' ),
						'subtitle' => __( 'Example', 'adri-ajdethemes-elements' ),
					],					
					[
						'image' => __( 'https://placehold.it/900x590', 'adri-ajdethemes-elements' ),
						'title' => __( 'Slide item 2', 'adri-ajdethemes-elements' ),
						'subtitle' => __( 'Example', 'adri-ajdethemes-elements' ),
					],
					[
						'image' => __( 'https://placehold.it/900x590', 'adri-ajdethemes-elements' ),
						'title' => __( 'Slide item 3', 'adri-ajdethemes-elements' ),
						'subtitle' => __( 'Example', 'adri-ajdethemes-elements' ),
					],
					
				],
				'title_field' => '{{{ title }}}',
			]
        );

		$this->add_control(
			'show_nav_arrow',
			[
				'label' => __( 'Navigation Arrows', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => __( 'Show', 'adri-ajdethemes-elements' ),
				'label_off' => __( 'Hide', 'adri-ajdethemes-elements' ),
				'separator' => 'before',
			]
		);
		
		$this->add_control(
			'show_pagination',
			[
				'label' => __( 'Pagination Dots', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => __( 'Show', 'adri-ajdethemes-elements' ),
				'label_off' => __( 'Hide', 'adri-ajdethemes-elements' ),
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
				'default' => 'h4',
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
					'{{WRAPPER}} .image-slider-container figcaption .isl-subtitle' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .image-slider-container figcaption .isl-title' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .swiper-nav-arrow.swiper-button-prev, .swiper-nav-arrow.swiper-button-next' => 'background: {{VALUE}};',
					'{{WRAPPER}} .swiper-pagination-lines.swiper-pagination .swiper-pagination-bullet.swiper-pagination-bullet-active' => 'background: {{VALUE}};',
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
					'{{WRAPPER}} .swiper-nav-arrow.swiper-button-prev' => 'color: {{VALUE}};',
					'{{WRAPPER}} .swiper-nav-arrow.swiper-button-next' => 'color: {{VALUE}};',
					'{{WRAPPER}} .swiper-pagination-lines.swiper-pagination .swiper-pagination-bullet' => 'background: {{VALUE}};',
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

		$html = '<div class="image-slider-container swiper-container">';
		$html .= '<div class="swiper-wrapper">';

		foreach ( $settings['slides'] as $index => $item ) {

			$title_setting_key = $this->get_repeater_setting_key( 'title', 'slides', $index );
			$subtitle_setting_key = $this->get_repeater_setting_key( 'subtitle', 'slides', $index );

			$this->add_render_attribute( $title_setting_key, [
                'class' => [ 'isl-subtitle' ],
            ] );
			
			$this->add_render_attribute( $subtitle_setting_key, [
                'class' => [ 'isl-title' ],
            ] );

			$target = $item['link']['is_external'] ? ' target="_blank"' : '';
			$nofollow = $item['link']['nofollow'] ? ' rel="nofollow"' : '';

			$html .= '<figure class="swiper-slide">';
			$item['link_on'] === 'yes' ? $html .= sprintf( '<a href="%1$s" %2$s %3$s>', $item['link']['url'], $target, $nofollow ) : $html .= '';
			$html .= sprintf( '<img src="%1$s" alt="zz">', $item['image']['url'], $item['image']['id'] );
			$item['link_on'] === 'yes' ? $html .= '</a>' : $html .= '';
			$html .= '<figcaption>';
			$html .= sprintf( '<span %2$s>%1$s</span>', $item['subtitle'], $this->get_render_attribute_string( $subtitle_setting_key ) );
			$html .= sprintf( '<%3$s %2$s>%1$s</%3$s>', $item['title'], $this->get_render_attribute_string( $title_setting_key ), $settings['title_size'] );
			$html .= '</figcaption>';
			$html .= '</figure>';
		}

		$html .= '</div>';

		$settings['show_pagination'] === 'yes' ? $html .= '<div class="swiper-pagination-lines swiper-pagination"></div>' : '';

		$has_pagination = $settings['show_pagination'] === 'yes' ? 'has-pagination' : '';

		$settings['show_nav_arrow'] === 'yes' ? 
		$html .= '<button class="swiper-nav-arrow ' . $has_pagination . ' swiper-button-prev"></button>
		<button class="swiper-nav-arrow ' . $has_pagination . ' swiper-button-next"></button>' : '';

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