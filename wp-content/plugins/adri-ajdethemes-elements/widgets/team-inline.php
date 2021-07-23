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
class Ajdethemes_Widget_Team_Inline extends \Elementor\Widget_Base {

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
		return 'team-inline';
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
		return __( 'Team Inline', 'adri-ajdethemes-elements' );
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
		return 'eicon-person';
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
		return [ 'team', 'user', 'member', 'employee' ];
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
			'section_team_inline',
			[
				'label' => __( 'Team Inline', 'adri-ajdethemes-elements' ),
			]
		);

		$this->add_control(
			'image',
			[
				'label' => __( 'Choose Image (recommended: 170x170)', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => 'https://placehold.it/170x170',
				],
			]
		);

		$this->add_control(
			'name_text',
			[
				'label' => __( 'Name', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => __( 'Shane Fisher', 'adri-ajdethemes-elements' ),
				'placeholder' => __( 'Enter your full name', 'adri-ajdethemes-elements' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'role_text',
			[
				'label' => __( 'Role', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => __( 'CEO &amp; Founder', 'adri-ajdethemes-elements' ),
				'placeholder' => __( 'Enter your role/job', 'adri-ajdethemes-elements' ),
				'label_block' => true,
			]
		);
		
		$this->add_control(
			'social_links',
			[
				'label' => __( 'Social Links - Shortcode', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'Enter the shortcode for the social links. Check the Help file (elements section) for detail instructions.', 'adri-ajdethemes-elements' ),
				'label_block' => true,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_content',
			[
				'label' => __( 'Inline Icon', 'adri-ajdethemes-elements' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'name_color',
			[
				'label' => __( 'Name Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .tm-name' => 'color: {{VALUE}};',
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
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .tm-role' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .team-inline .tm-content ul li a' => 'background: {{VALUE}};',
					'{{WRAPPER}} .team-inline .tm-content ul li a:hover,
					{{WRAPPER}} .team-inline .tm-content ul li a:focus' => 'color: {{VALUE}};',
					
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
					'{{WRAPPER}} .team-inline .tm-content ul li a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .team-inline .tm-content ul li a:hover,
					{{WRAPPER}} .team-inline .tm-content ul li a:focus' => 'background: {{VALUE}};',
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
		// Name
		$this->add_render_attribute( 'name_text', 'class', 'tm-name' );
		$this->add_inline_editing_attributes( 'name_text' );
		// Role
		$this->add_render_attribute( 'role_text', 'class', 'tm-role' );
		$this->add_inline_editing_attributes( 'role_text' );

		if ( ! empty( $settings['image']['url'] ) ) {
			$this->add_render_attribute( 'image', 'src', $settings['image']['url'] );
			$this->add_render_attribute( 'image', 'alt', \Elementor\Control_Media::get_image_alt( $settings['image'] ) );
			$this->add_render_attribute( 'image', 'title', \Elementor\Control_Media::get_image_title( $settings['image'] ) );
			$this->add_render_attribute( 'image', 'width', '170' );
			
			$image_html = \Elementor\Group_Control_Image_Size::get_attachment_image_html( $settings, 'full', 'image' );
		} else {
			$image_html = '<img src="https://placehold.it/170x170" alt="Placeholder">';
		}


		$html = '<div class="team-inline">';
		$html .= $image_html;
		$html .= '<div class="tm-content">';
		$html .= sprintf( '<h5 %2$s>%1$s</h5>', $settings['name_text'], $this->get_render_attribute_string( 'name_text' ) );
		$html .= sprintf( '<span %2$s>%1$s</span>', $settings['role_text'], $this->get_render_attribute_string( 'role_text' ) );
		$html .= do_shortcode( shortcode_unautop( $settings['social_links'] ) );
		$html .= '</div></div>';

		echo $html;
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
		<# 
		view.addRenderAttribute( 'name_text', 'class', 'tm-name' );
		view.addInlineEditingAttributes( 'name_text', 'none' );
		
		view.addRenderAttribute( 'role_text', 'class', 'tm-role' );
		view.addInlineEditingAttributes( 'role_text' );
		#>
		<div class="team-inline">
			<img src="{{{ settings.image.url }}}" alt="Placeholder">
			<div class="tm-content">
				<a href="#"><h5 {{{ view.getRenderAttributeString( 'name_text' ) }}}>{{{ settings.name_text }}}</h5></a>
				<span {{{ view.getRenderAttributeString( 'role_text' ) }}}>{{{ settings.role_text }}}</span>
			</div>
        </div>
        <?php
    }

}