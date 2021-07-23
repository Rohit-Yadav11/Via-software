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
class Ajdethemes_Widget_Neue_Slider extends \Elementor\Widget_Base {

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
		return 'neue_slider';
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
		return __( 'Neue Slider', 'adri-ajdethemes-elements' );
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
		return 'eicon-slider-3d';
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
				'label' => __( 'Neue Slider', 'adri-ajdethemes-elements' ),
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'image',
			[
				'label' => __( 'Choose Image (recommended: 570x390)', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => 'https://placehold.it/570x390',
				],
			]
		);

		$repeater->add_control(
			'title',
			[
				'label' => __( 'Title', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Project title here', 'adri-ajdethemes-elements' ),
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

		$this->add_control(
			'slides',
			[
				'label' => __( 'Slides', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'image' => __( 'https://placehold.it/570x390', 'adri-ajdethemes-elements' ),
						'title' => __( 'Project title 1', 'adri-ajdethemes-elements' ),
						'subtitle' => __( 'Consulting', 'adri-ajdethemes-elements' ),
					],					
					[
						'image' => __( 'https://placehold.it/570x390', 'adri-ajdethemes-elements' ),
						'title' => __( 'Project title 2', 'adri-ajdethemes-elements' ),
						'subtitle' => __( 'Consulting', 'adri-ajdethemes-elements' ),
					],
					[
						'image' => __( 'https://placehold.it/570x390', 'adri-ajdethemes-elements' ),
						'title' => __( 'Project title 3', 'adri-ajdethemes-elements' ),
						'subtitle' => __( 'Consulting', 'adri-ajdethemes-elements' ),
					],
					
				],
				'title_field' => '{{{ title }}}',
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
			'left_nav',
			[
				'label' => __( 'Navigation far left', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => __( 'On', 'adri-ajdethemes-elements' ),
				'label_off' => __( 'Off', 'adri-ajdethemes-elements' ),
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
					'{{WRAPPER}} .neue-slider-container.swiper-container .ns-item.swiper-slide .ns-content .ns-title' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .neue-slider-container.swiper-container .ns-item.swiper-slide .ns-content .ns-subtitle' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .ns-nav .ns-button-prev' => 'background: {{VALUE}};',
					'{{WRAPPER}} .ns-nav .ns-button-next' => 'background: {{VALUE}};',
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
					'{{WRAPPER}} .ns-nav .ns-button-prev' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ns-nav .ns-button-next' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ns-nav .ns-button-sep' => 'background: {{VALUE}};',
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

		$settings['left_nav'] === 'yes' ? $class_nav_left = 'ns-nav-far-left' : $class_nav_left = '';

		echo '<div class="neue-slider-container swiper-container">';
		echo '<div class="swiper-wrapper neue-slider">';

		foreach( $settings['slides'] as $index => $item ) {

			$image_setting_key = $this->get_repeater_setting_key( 'image', 'slides', $index );
			$title_setting_key = $this->get_repeater_setting_key( 'title', 'slides', $index );
			$subtitle_setting_key = $this->get_repeater_setting_key( 'subtitle', 'slides', $index );

			$this->add_render_attribute( $title_setting_key, [
                'class' => [ 'ns-title' ],
            ] );
			$this->add_render_attribute( $subtitle_setting_key, [
                'class' => [ 'ns-subtitle' ],
			] );

			$html = '<div class="swiper-slide ns-item">';
			$html .= sprintf( '<img src="%1$s" alt="%2$s">', $item['image']['url'], $item['title'] );
			$html .= '<div class="ns-content">';
			$html .= sprintf( '<span %2$s>%1$s</span>', $item['subtitle'], $this->get_render_attribute_string( $subtitle_setting_key ) );
			$html .= sprintf( '<%3$s %2$s>%1$s</%3$s>', $item['title'], $this->get_render_attribute_string( $title_setting_key ), $settings['title_size'] );
			$html .= '</div>';
			$html .= '</div>';

			echo $html;
		}

		echo '</div>';

		echo $settings['show_nav'] === 'yes' ? 
		'<div class="ns-nav ' . $class_nav_left . '">
			<div class="ns-button-prev"><i class="icon-Arrow-OutLeft"></i></div>
			<span class="ns-button-sep"></span>
			<div class="ns-button-next"><i class="icon-Arrow-OutRight"></i></div>
		</div>' : '';

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

		<div class="neue-slider-container swiper-container">
			<div class="swiper-wrapper neue-slider">

				<# _.each( settings.slides, function( item, index ) { #>
				<div class="swiper-slide ns-item">
					<img src="{{{ item.image.url }}}" alt="Placeholder">
					<div class="ns-content">
						<span class="ns-subtitle">{{{ item.subtitle }}}</span>
						<{{{ settings.title_size }}} class="ns-title">{{{ item.title }}}</{{{ settings.title_size }}}>
					</div>
				</div>
				<# }); #>

			</div>
			
			<# if ( settings.show_nav ) { #>
			<div class="ns-nav ns-nav-far-left">
				<div class="ns-button-prev"><i class="icon-Arrow-OutLeft"></i></div>
				<span class="ns-button-sep"></span>
				<div class="ns-button-next"><i class="icon-Arrow-OutRight"></i></div>
			</div>
			<# } #>
		</div>
		
		<?php
    }

}