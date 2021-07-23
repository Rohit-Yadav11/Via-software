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
class Ajdethemes_Widget_Contact_Form_7 extends \Elementor\Widget_Base {

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
		return 'adri-ajdethemes-elements-cf7';
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
		return __( 'Contact Form 7', 'adri-ajdethemes-elements' );
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
		return 'eicon-form-horizontal';
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
		return [ 'email', 'newsletter', 'form', 'contact', 'plugin', 'contact form 7' ];
	}


	/**
	 * Get CF7 forms.
	 *
	 * @since 1.0.0
	 *
	 * @return array CF7 forms.
	 */
	protected function adri_ajdethemes_elements_get_cf7_forms() {
		$contact_forms = array();

		$cf7 = get_posts( 'post_type="wpcf7_contact_form"&numberposts=-1' );
			if ( $cf7 ) {
				foreach ( $cf7 as $cform ) {
					$contact_forms[$cform->ID] = $cform->post_title;
				}
			} else {
				$contact_forms[0] = __( 'No contact forms found', 'adri-ajdethemes-elements' );
			}

		return $contact_forms;
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
			'section_frame_icon',
			[
				'label' => __( 'Contact Form 7', 'adri-ajdethemes-elements' ),
			]
		);

		$this->add_control(
			'contact_form',
			[
				'label'   => __( 'Contact Form', 'adri-ajdethemes-elements' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => $this->adri_ajdethemes_elements_get_cf7_forms(),
			]
		);

        $this->end_controls_section();
        
        $this->start_controls_section(
			'section_style_content',
			[
				'label' => __( 'Input Style', 'adri-ajdethemes-elements' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
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
					'{{WRAPPER}} .form-group' => 'background: {{VALUE}};',
					'{{WRAPPER}} .form-group-checkbox input:checked + label:before' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .form-style' => 'color: {{VALUE}};',
					'{{WRAPPER}} .form-style:focus' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .form-group.is-focused label, .form-group.is-not-empty label' => 'color: {{VALUE}};',
					'{{WRAPPER}} .form-group-checkbox label:before' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .form-group-checkbox input:checked + label:before' => 'background: {{VALUE}};',
				],
			]
		);
        
        $this->add_control(
			'input_color',
			[
				'label' => __( 'Text Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .form-style' => 'color: {{VALUE}};',
				],
			]
		);
        
        $this->add_control(
			'input_border_color',
			[
				'label' => __( 'Border Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .form-style' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .form-group-checkbox label:before' => 'border-color: {{VALUE}};',
				],
			]
        );
		
		$this->add_control(
			'input_bg_color',
			[
				'label' => __( 'Background Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .form-style' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'label_color',
			[
				'label' => __( 'Label/Placeholder Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .form-group label' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'label_focus_color',
			[
				'label' => __( 'Label/Placeholder Focus Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .form-group.is-focused label, .form-group.is-not-empty label' => 'color: {{VALUE}};',
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

		$html = '<div class="cf-form-wrapper">';
		$html .= do_shortcode( $this->get_cf7_shortcode() );
		$html .= '</div>';

		echo $html;
	}
	
	/**
	 * Gets and returns the CF7 shortcode
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	private function get_cf7_shortcode() {
		$settings = $this->get_settings_for_display();

		if ( ! $settings['contact_form'] ) {
			return __( 'Select a contact form, from settings.', 'adri-ajdethemes-elements' );
		}

		$attributes = [
			'id' => $settings['contact_form'],
		];

		$this->add_render_attribute( 'form_shortcode', $attributes );

		$shortcode = [];
		$shortcode[] = sprintf( '[contact-form-7 %s]', $this->get_render_attribute_string( 'form_shortcode' ) );

		return implode("", $shortcode);
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