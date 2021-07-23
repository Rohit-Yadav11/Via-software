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
class Ajdethemes_Widget_Team_Card extends \Elementor\Widget_Base {

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
		return 'team-card';
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
		return __( 'Team Card', 'adri-ajdethemes-elements' );
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
		return [ 'image', 'team', 'user', 'member', 'employee', 'bio' ];
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
			'section_team_card',
			[
				'label' => __( 'Team Card', 'adri-ajdethemes-elements' ),
			]
		);

		$this->add_control(
			'image',
			[
				'label' => __( 'Choose Image (recommended: 770x830)', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => 'https://placehold.it/770x830',
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
				'default' => __( 'Chad Champion', 'adri-ajdethemes-elements' ),
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
				'default' => __( 'CEO & Founder', 'adri-ajdethemes-elements' ),
				'placeholder' => __( 'Enter your role', 'adri-ajdethemes-elements' ),
				'label_block' => true,
			]
		);
 
		$this->add_control(
			'description_title',
			[
				'label' => __( 'Title - Bio', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => __( 'Hello', 'adri-ajdethemes-elements' ),
				'placeholder' => __( 'Enter a title', 'adri-ajdethemes-elements' ),
				'label_block' => true,
			]
		);
		
		$this->add_control(
			'description_text',
			[
				'label' => __( 'Text - Bio', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'default' => __( 'Nulla fugiat incididunt exercitation fugiat sint magna dolor nisi occaes at ecat laborum do duis adipisi es cupidatat este. Laboris aute ipsum occaecat labore ut excepteur, este elitis consequat eu cupidatat.', 'adri-ajdethemes-elements' ),
				'placeholder' => __( 'Write bio.', 'adri-ajdethemes-elements' ),
				'label_block' => true,
			]
		);
		
		$this->add_control(
			'description_social_links',
			[
				'label' => __( 'Social Links - Bio', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'default' => '[slinks]
				[siconlink link="#" icon="fab fa-linkedin-in"]
				[siconlink link="#" icon="fab fa-twitter"]
				[siconlink link="#" icon="fas fa-envelope"]
				[/slinks]',
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
			'name_size',
			[
				'label' => __( 'Name HTML Tag', 'adri-ajdethemes-elements' ),
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
			'bio_text_color',
			[
				'label' => __( 'Bio Text Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					 '{{WRAPPER}} .team-card .tm-bio .tm-title, {{WRAPPER}} .team-card .tm-bio .tm-description' => 'color: {{VALUE}};',
					 '{{WRAPPER}} .team-card .tm-bio ul li' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'bio_bg_color',
			[
				'label' => __( 'Bio Background Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					 '{{WRAPPER}} .team-card .tm-bio' => 'background: {{VALUE}};',
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
					'{{WRAPPER}} .open.team-card .tm-bio' => 'box-shadow: 15px 15px 0 {{VALUE}};',
					'{{WRAPPER}} .team-card .tm-bio .tm-icon-links:before' => 'background: {{VALUE}};',
					'{{WRAPPER}} .team-card .tm-bio ul li a:hover, {{WRAPPER}} .team-card .tm-bio ul li a:focus' => 'color: {{VALUE}};',
					'{{WRAPPER}} .team-card .btn-bio' => 'background: {{VALUE}};'
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
					'{{WRAPPER}} .team-card .btn-bio span, {{WRAPPER}} .team-card .btn-bio span:before' => 'background: {{VALUE}};',
					'{{WRAPPER}} .team-card .tm-bio' => 'background: {{VALUE}};'
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
		}
		$image_html = \Elementor\Group_Control_Image_Size::get_attachment_image_html( $settings, 'full', 'image' );

		$html = '<div class="team-card">';
		$html .= '<div class="tm-img-wrapper">'. $image_html;
		$html .= '<div class="tm-bio">';
		$html .= sprintf( '<h3 class="tm-title">%1$s</h3>', $settings['description_title'] );
		$html .= sprintf( '<p class="tm-description">%1$s</p>', $settings['description_text'] );

		$html .= '<div class="tm-icon-links">';
		$html .= sprintf( '%s', $settings['description_social_links'] );
		$html .= '</div>';

		$html .= '</div>';
		$html .= '</div>';

		$html .= '<div class="tm-info">';
		$html .= sprintf( '<%1$s %3$s>%2$s</%1$s>', $settings['name_size'], $settings['name_text'], $this->get_render_attribute_string( 'name_text' ) );
		$html .= sprintf( '<span %2$s>%1$s</span>', $settings['role_text'], $this->get_render_attribute_string( 'role_text' ) );
		$html .= '<a href="#" class="btn-bio"><span></span></a>';
		$html .= '</div>';

		$html .= '</div>';
		
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
		<div class="team-card">
			<div class="tm-img-wrapper">
            	<img src="{{{ settings.image.url }}}" alt="Placeholder">
				<div class="tm-bio">
					<h3 class="tm-title">Hello</h3>
					<p class="tm-description">Nulla fugiat incididunt exercitation fugiat sint magna dolor nisi occaes at ecat laborum do duis adipisi es cupidatat este.  Laboris aute ipsum occaecat labore ut excepteur, este elitis consequat eu cupidatat.</p>
					<div class="tm-icon-links">
						<ul>
							<li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
							<li><a href="#"><i class="fab fa-twitter"></i></a></li>
							<li><a href="#"><i class="fas fa-envelope"></i></a></li>
						</ul>
					</div>
				</div>
			</div>

			<div class="tm-info">
				<{{{ settings.name_size }}} {{{ view.getRenderAttributeString( 'name_text' ) }}}>{{{ settings.name_text }}}</{{{ settings.name_size }}}>
				<span {{{ view.getRenderAttributeString( 'role_text' ) }}}>{{{ settings.role_text }}}</span>
				<a href="#" class="btn-bio"><span></span></a>
			</div>
        </div>
        <?php
    }

}