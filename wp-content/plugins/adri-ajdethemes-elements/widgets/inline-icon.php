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
class Ajdethemes_Widget_Inline_Icon extends \Elementor\Widget_Base {

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
		return 'inline-icon';
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
		return __( 'Inline Icon', 'adri-ajdethemes-elements' );
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
		return 'eicon-post-list';
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
		return [ 'icon', 'feature', 'service' ];
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
			'section_inline_icon',
			[
				'label' => __( 'Inline Icon', 'adri-ajdethemes-elements' ),
			]
		);

		$this->add_control(
			'selected_icon',
			[
				'label' => __( 'Icon', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => [
					'value' => 'fas fa-pen-nib',
					'library' => 'fa-solid',
				],
			]
		);

		$this->add_control(
			'title_text',
			[
				'label' => __( 'Title & Description', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => __( 'Title example', 'adri-ajdethemes-elements' ),
				'placeholder' => __( 'Enter your title', 'adri-ajdethemes-elements' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'description_text',
			[
				'label' => '',
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'default' => __( 'Velit consequat eu cupidatat este irure qui este dolore qui conse.', 'adri-ajdethemes-elements' ),
				'placeholder' => __( 'Enter your description', 'adri-ajdethemes-elements' ),
				'rows' => 10,
				'separator' => 'none',
				'show_label' => false,
			]
		);

		$this->add_control(
			'show_link',
			[
				'label' => __( 'Show Link/Button', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => __( 'Show', 'adri-ajdethemes-elements' ),
				'label_off' => __( 'Hide', 'adri-ajdethemes-elements' ),
			]
		);

		$this->add_control(
			'link',
			[
				'label' => __( 'Link', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'https://your-link.com', 'adri-ajdethemes-elements' ),
				'separator' => 'before',
				'condition' => [
					'show_link' => 'yes',
				],
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
			'icon_bg',
			[
				'label' => __( 'Icon Background Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_2,
				],
				'selectors' => [
					'{{WRAPPER}} .ft-icon' => 'background-color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'icon_color',
			[
				'label' => __( 'Icon Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_2,
				],
				'selectors' => [
					'{{WRAPPER}} .ft-icon' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'icon_hover_bg_color',
			[
				'label' => __( 'Hover Accent Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_2,
				],
				'selectors' => [
					'{{WRAPPER}} .ft-inline-icon:hover .ft-icon' => 'color: {{VALUE}}',
				],
			]
		);
		
		$this->add_control(
			'icon_hover_bg_shadow',
			[
				'label' => __( 'Hover Shadow Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_2,
				],
				'selectors' => [
					'{{WRAPPER}} .ft-inline-icon:hover .ft-icon' => 'box-shadow: 5px 5px 0 {{VALUE}};',
				],
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
					'{{WRAPPER}} .ft-inline-icon-title' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .ft-content > p' => 'color: {{VALUE}};',
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
        $this->add_render_attribute( 'title_text', 'class', 'ft-inline-icon-title' );
        $this->add_inline_editing_attributes( 'title_text' );
        // Description
        $this->add_render_attribute( 'description_text', 'class', '' );
        $this->add_inline_editing_attributes( 'description_text' );
		?>
		<div class="ft-inline-icon">
			<?php if ( ! empty( $settings['selected_icon']['value'] ) ) : ?>
			<span class="ft-icon"><?php \Elementor\Icons_Manager::render_icon( $settings['selected_icon'] ); ?></span>
			<?php endif; ?>
			<div class="ft-content">
				<?php 
				echo sprintf( '<h5 %1$s>%2$s</h5>', $this->get_render_attribute_string( 'title_text' ), $settings['title_text'] ); 
				echo sprintf( '<p %1$s>%2$s</p>', $this->get_render_attribute_string( 'description_text' ), $settings['description_text'] );
				?>
				<?php if ( ! empty( $settings['link'] ) ) : ?>
				<a href="<?php echo $settings['link']['url']; ?>" class="ft-inline-icon-btn"><i class="icon-Arrow-OutRight"></i></a>
				<?php endif; ?>
			</div>
		</div>
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
		iconHTML = elementor.helpers.renderIcon( view, settings.selected_icon, { 'aria-hidden': true }, 'i' , 'object' );

		view.addRenderAttribute( 'title_text', 'class', 'ft-inline-icon-title' );
		view.addInlineEditingAttributes( 'title_text', 'none' );
		
		view.addRenderAttribute( 'description_text', 'class', '' );
		view.addInlineEditingAttributes( 'description_text' );
		#>
		<div class="ft-inline-icon">
			<span class="ft-icon">{{{ iconHTML.value }}}</span>
			<div class="ft-content">
				<h5 {{{ view.getRenderAttributeString( 'title_text' ) }}} >{{{ settings.title_text }}}</h5>
				<p {{{ view.getRenderAttributeString( 'description_text' ) }}}>{{{ settings.description_text }}}</p>
				<# if ( settings.link.url && ( settings.show_link === 'yes' ) ) { #>
				<a href="#" class="ft-inline-icon-btn"><i class="icon-Arrow-OutRight"></i></a>
				<# } #>
			</div>
		</div>
        <?php
    }

}