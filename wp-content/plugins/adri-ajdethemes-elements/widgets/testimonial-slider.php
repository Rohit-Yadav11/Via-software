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
class Ajdethemes_Widget_Testimonials extends \Elementor\Widget_Base {

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
		return 'testimonials';
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
		return __( 'Testimonials', 'adri-ajdethemes-elements' );
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
		return 'eicon-testimonial-carousel';
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
	// public function get_script_depends() {
	// 	return [ 'Swiper' ];
	// }

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
		return [ 'testimonial', 'review', 'comment' ];
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
				'label' => __( 'Testimonial Slider', 'adri-ajdethemes-elements' ),
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'image',
			[
				'label' => __( 'Choose Image (recommended: 140x140)', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => 'https://placehold.it/140x140',
				],
			]
		);

		$repeater->add_control(
			'name',
			[
				'label' => __( 'Name', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Chad Champion', 'adri-ajdethemes-elements' ),
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
			]
		);
		
		$repeater->add_control(
			'role',
			[
				'label' => __( 'Role/Company', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Founder & CEO, Yes', 'adri-ajdethemes-elements' ),
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'quote',
			[
				'label' => __( 'Testimonial', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::WYSIWYG,
				'default' => __( 'Officia nulla anim non ullamco sit in adipisic exercitation qui elit. Duis excepteur nostrud elit do ipsum anim incididunt ad tempor laboris dolore elit. In consectetur nostrud nulla elit ullamco.', 'adri-ajdethemes-elements' ),
				'show_label' => false,
			]
		);

		$this->add_control(
			'testimonial_slides',
			[
				'label' => __( 'Testimonials', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'image' => __( 'https://placehold.it/140x140', 'adri-ajdethemes-elements' ),
						'name' => __( 'Steve Works', 'adri-ajdethemes-elements' ),
						'role' => __( 'Founder & CEO, Dongle', 'adri-ajdethemes-elements' ),
						'quote' => __( 'Officia nulla anim non ullamco sit in adipisic exercitation qui elit. Duis excepteur nostrud elit do ipsum anim incididunt ad tempor laboris dolore elit. In consectetur nostrud nulla elit ullamco.', 'adri-ajdethemes-elements' ),
					],
					[
						'image' => __( 'https://placehold.it/140x140', 'adri-ajdethemes-elements' ),
						'name' => __( 'Zuck Marks', 'adri-ajdethemes-elements' ),
						'role' => __( 'Founder, Facesmash', 'adri-ajdethemes-elements' ),
						'quote' => __( 'Officia nulla anim non ullamco sit in adipisic exercitation qui elit. Duis excepteur nostrud elit do ipsum anim incididunt ad tempor laboris dolore elit. In consectetur nostrud nulla elit ullamco.', 'adri-ajdethemes-elements' ),
					],
					[
						'image' => __( 'https://placehold.it/140x140', 'adri-ajdethemes-elements' ),
						'name' => __( 'Elton Must', 'adri-ajdethemes-elements' ),
						'role' => __( 'CEO, Juice Car', 'adri-ajdethemes-elements' ),
						'quote' => __( 'Officia nulla anim non ullamco sit in adipisic exercitation qui elit. Duis excepteur nostrud elit do ipsum anim incididunt ad tempor laboris dolore elit. In consectetur nostrud nulla elit ullamco.', 'adri-ajdethemes-elements' ),
					],
				],
				'title_field' => '{{{ name }}}',
			]
        );

		$this->add_control(
			'show_nav',
			[
				'label' => __( 'Show Navigation', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => __( 'Show', 'adri-ajdethemes-elements' ),
				'label_off' => __( 'Hide', 'adri-ajdethemes-elements' ),
			]
		);
		
		$this->add_control(
			'show_pagination',
			[
				'label' => __( 'Show Pagination', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => __( 'Show', 'adri-ajdethemes-elements' ),
				'label_off' => __( 'Hide', 'adri-ajdethemes-elements' ),
			]
		);
		
		$this->add_control(
			'show_quote',
			[
				'label' => __( 'Show Quote Symbol', 'adri-ajdethemes-elements' ),
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
				'label' => __( 'Testimonial Slider', 'adri-ajdethemes-elements' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'name_color',
			[
				'label' => __( 'Name Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_2,
				],
				'selectors' => [
					'{{WRAPPER}} .ft-testimonial-slider .ft-testimonial-item .ft-content cite .tst-name' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'role_color',
			[
				'label' => __( 'Role Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_2,
				],
				'selectors' => [
					'{{WRAPPER}} .ft-testimonial-slider .ft-testimonial-item .ft-content cite .tst-role' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'text_color',
			[
				'label' => __( 'Testimonial Text Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_2,
				],
				'selectors' => [
					'{{WRAPPER}} .ft-testimonial-slider .ft-testimonial-item > .ft-content' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_shadow_color',
			[
				'label' => __( 'Icon Shadow Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_2,
				],
				'selectors' => [
					'{{WRAPPER}} .ft-testimonial-slider .ft-testimonial-item .ft-image .tst-symbol' => 'box-shadow: 5px 5px 0 {{VALUE}};',
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
					'{{WRAPPER}} .ft-testimonial-slider .ft-testimonial-item .ft-image .tst-symbol' => 'background: {{VALUE}};',
					'{{WRAPPER}} .swiper-nav-arrow.swiper-button-prev' => 'background: {{VALUE}};',
					'{{WRAPPER}} .swiper-nav-arrow.swiper-button-next' => 'background: {{VALUE}};',
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
					'{{WRAPPER}} .ft-testimonial-slider .ft-testimonial-item .ft-image .tst-symbol' => 'color: {{VALUE}};',
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

		$settings['show_pagination'] === 'yes' ? $pagination = '<div class="swiper-pagination-lines swiper-pagination"></div>' : $pagination = '';
		$settings['show_nav'] === 'yes' ? $nav = '<div class="swiper-nav-arrow swiper-button-prev"></div><div class="swiper-nav-arrow swiper-button-next"></div>' : $nav = '';

		echo '<div class="swiper-container testimonial-slider-container">';
		echo '<div class="swiper-wrapper ft-testimonial-slider">';

		foreach ( $settings['testimonial_slides'] as $index => $item ) {
            $name_setting_key = $this->get_repeater_setting_key( 'name', 'testimonial_slides', $index );
            $role_setting_key = $this->get_repeater_setting_key( 'role', 'testimonial_slides', $index );
            $quote_setting_key = $this->get_repeater_setting_key( 'quote', 'testimonial_slides', $index );

			$this->add_render_attribute( $name_setting_key, [
                'class' => [ 'tst-name' ],
            ] );
			
			$this->add_render_attribute( $role_setting_key, [
                'class' => [ 'tst-role' ],
            ] );
			
			$this->add_render_attribute( $quote_setting_key, [
                'class' => [ 'ft-content' ],
			] );
			
			$html = '<div class="ft-testimonial-item swiper-slide">';
			$html .= '<div class="ft-image">';
			$html .= $settings['show_quote'] === 'yes' ? '<span class="tst-symbol"></span>' : '';
			$html .= sprintf( '<img src="%1$s" alt="%2$s - %3$s" />', $item['image']['url'], $item['name'], $item['role'] );
			$html .= '</div>';
			$html .= sprintf( '<blockquote %2$s>%1$s', $item['quote'], $this->get_render_attribute_string( $quote_setting_key ) );
			$html .= '<footer><cite>';
			$html .= sprintf( '<span %2$s>%1$s</span>', $item['name'], $this->get_render_attribute_string( $name_setting_key ) );
			$html .= sprintf( '<span %2$s>%1$s</span>', $item['role'], $this->get_render_attribute_string( $role_setting_key ) );
			$html .= '</cite></footer>';
			$html .= '</blockquote>';
			$html .= '</div>';

			echo $html;
		}

		echo '</div>';

		echo $settings['show_pagination'] === 'yes' ? '<div class="swiper-pagination-lines swiper-pagination"></div>' : '';
		echo $settings['show_nav'] === 'yes' ? '<div class="swiper-nav-arrow swiper-button-prev"></div><div class="swiper-nav-arrow swiper-button-next"></div>' : '';

		echo '</div>';

		?>		
		<?php
    }
    
    /**
	 * Render image box widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 2.9.0
	 */
    protected function content_template() {
		?>
		<div class="swiper-container testimonial-slider-container">
			<div class="swiper-wrapper ft-testimonial-slider">

				<# _.each( settings.testimonial_slides, function( item, index ) { #>
				<div class="ft-testimonial-item swiper-slide">
					<div class="ft-image">
						<# if ( settings.show_quote ) { #>
						<span class="tst-symbol"></span>
						<# } #>
						<img src="{{{ item.image.url }}}" alt="Placeholder">
					</div>
					
					<blockquote class="ft-content">
						{{{ item.quote }}}
						<footer>
							<cite>
								<span class="tst-name">{{{ item.name }}}</span>
								<span class="tst-role">{{{ item.role }}}</span>
							</cite>
						</footer>
					</blockquote>
				</div>
				<# }); #>

			</div>

			<# if ( settings.show_pagination ) { #>
			<div class="swiper-pagination-lines swiper-pagination"></div>
			<# } #>
			
			<# if ( settings.show_nav ) { #>
				<div class="swiper-nav-arrow swiper-button-prev"></div>
				<div class="swiper-nav-arrow swiper-button-next"></div>
			<# } #>
		</div>			

		
		<?php
    }

}