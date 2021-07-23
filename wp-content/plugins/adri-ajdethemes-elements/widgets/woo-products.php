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
class Ajdethemes_Widget_Woo_Products extends \Elementor\Widget_Base {

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
		return 'adri-ajdethemes-elements-woo-products';
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
		return __( 'Products', 'adri-ajdethemes-elements' );
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
		return 'eicon-woocommerce';
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
		return [ 'store', 'shop', 'woo', 'woocommerce', 'commerce' ];
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
			'section_feature_image',
			[
				'label' => __( 'Products', 'adri-ajdethemes-elements' ),
			]
		);

		$this->add_control(
			'ft_layout',
			[
				'label'   => __( 'Select Style', 'adri-ajdethemes-elements' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'frame',
				'options' => [
					'frame'   => __( 'Frame', 'adri-ajdethemes-elements' ),
					'stacked' => __( 'Stacked', 'adri-ajdethemes-elements' ),
					'classic' => __( 'Classic', 'adri-ajdethemes-elements' ),
				],
			]
        );

		$this->add_control(
			'image',
			[
				'label' => __( 'Choose Image (recommended: 770x540)', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
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
				'default' => __( 'Title example here', 'adri-ajdethemes-elements' ),
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
				'default' => __( 'Velit consequat eu cupidatat irure qui este dolore qui consectetur in non culpa labore duis.', 'adri-ajdethemes-elements' ),
				'placeholder' => __( 'Enter your description', 'adri-ajdethemes-elements' ),
				'rows' => 10,
				'separator' => 'none',
				'show_label' => false,
			]
		);

		$this->add_control(
			'show_link',
			[
				'label' => __( 'Add link', 'adri-ajdethemes-elements' ),
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
		
		$this->add_control(
			'link_text',
			[
				'label' => __( 'Link text', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => __( 'Read more', 'adri-ajdethemes-elements' ),
				'placeholder' => __( 'Enter link text', 'adri-ajdethemes-elements' ),
				'label_block' => true,
				'condition' => [
					'show_link' => 'yes',
					'ft_layout' => 'classic'
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
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .ft-image-frame .ft-title,
					{{WRAPPER}} .ft-image-stacked .ft-title,
					{{WRAPPER}} .ft-image-classic .ft-title' => 'color: {{VALUE}};',
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
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .ft-image-frame .ft-description,
					{{WRAPPER}} .ft-image-stacked .ft-description,
					{{WRAPPER}} .ft-image-classic .ft-description' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .ft-image-frame:hover .ft-link-frame:before' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .ft-image-frame:focus .ft-link-frame:before' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .focus-on.ft-image-frame .ft-link-frame:before' => 'border-color: {{VALUE}};',
					
					'{{WRAPPER}} .ft-image-frame .ft-box' => 'background: {{VALUE}};',

					'{{WRAPPER}} .ft-image-stacked:hover .ft-img-wrapper:before' => 'background: {{VALUE}};',
					'{{WRAPPER}} .ft-image-stacked:focus .ft-img-wrapper:before' => 'background: {{VALUE}};',
					
					'{{WRAPPER}} .ft-image-stacked .ft-btn' => 'background: {{VALUE}};',

					'{{WRAPPER}} .ft-image-classic:hover .ft-img-wrapper' => 'box-shadow: 15px 15px 0 {{VALUE}};',
					'{{WRAPPER}} .ft-image-classic:focus .ft-img-wrapper' => 'box-shadow: 15px 15px 0 {{VALUE}};',

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
					'{{WRAPPER}} .ft-image-frame:hover .ft-img-wrapper:before' => 'background: {{VALUE}};', 
					'{{WRAPPER}} .ft-image-frame:focus .ft-img-wrapper:before' => 'background: {{VALUE}};', 
					'{{WRAPPER}} .focus-on.ft-image-frame .ft-img-wrapper:before' => 'background: {{VALUE}};',

					'{{WRAPPER}} .ft-image-frame .ft-box .css-plus, {{WRAPPER}} .ft-image-frame .ft-box .css-plus:before,' => 'background: {{VALUE}};',

					'{{WRAPPER}} .ft-image-stacked .ft-content' => 'background: {{VALUE}};',
					'{{WRAPPER}} .ft-image-stacked .ft-btn' => 'color: {{VALUE}};',

					'{{WRAPPER}} .ft-image-classic .ft-img-wrapper:before' => 'background: {{VALUE}};',
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
        $this->add_render_attribute( 'title_text', 'class', 'ft-title' );
        $this->add_inline_editing_attributes( 'title_text' );
        // Description
        $this->add_render_attribute( 'description_text', 'class', 'ft-description' );
		$this->add_inline_editing_attributes( 'description_text' );

		if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_link_attributes( 'link', $settings['link'] );
		}
		
		if ( ! empty( $settings['image']['url'] ) ) {
			$this->add_render_attribute( 'image', 'src', $settings['image']['url'] );
			$this->add_render_attribute( 'image', 'alt', \Elementor\Control_Media::get_image_alt( $settings['image'] ) );
			$this->add_render_attribute( 'image', 'title', \Elementor\Control_Media::get_image_title( $settings['image'] ) );
		}

		$image_html = \Elementor\Group_Control_Image_Size::get_attachment_image_html( $settings, 'full', 'image' );

		$html = '';

		if ( $settings['ft_layout'] === 'frame' ) {

			$html .= ' <div class="ft-image-frame">';

			if ( ! empty( $settings['link']['url'] ) ) {
				$html .= sprintf('<a %s class="ft-link-frame">', $this->get_render_attribute_string( 'link' ));
			} else {
				$html .= '<div class="ft-link-frame">';
			}

			$html .= sprintf( '<div class="ft-img-wrapper">%s</div>', $image_html );
			$html .= sprintf( '<%1$s %3$s>%2$s</%1$s>', $settings['title_size'], $settings['title_text'], $this->get_render_attribute_string( 'title_text' ) );
			$html .= sprintf( '<p %2$s>%1$s</p>', $settings['description_text'], $this->get_render_attribute_string( 'description_text' ) );

			if ( ! empty( $settings['link']['url'] ) ) {
				$html .= '<div class="ft-box"><span class="css-plus"></span></div></a>';
			} else {
				$html .= '</div>';
			}

			$html .= '</div>';

		} elseif ( $settings['ft_layout'] === 'stacked' ) {

			$html .= '<div class="ft-image-stacked">';

			if ( ! empty( $settings['link']['url'] ) ) {
				$html .= sprintf( '<a %s>', $this->get_render_attribute_string( 'link' ) );
				$html .= sprintf( '<div class="ft-img-wrapper">%s</div></a>', $image_html );
			} else {
				$html .= sprintf( '<div class="ft-img-wrapper">%s</div>', $image_html );
			}

			$html .= '<div class="ft-content">';
			$html .= sprintf( '<%1$s %3$s>%2$s</%1$s>', $settings['title_size'], $settings['title_text'], $this->get_render_attribute_string( 'title_text' ) );
			$html .= sprintf( '<p %2$s>%1$s</p>', $settings['description_text'], $this->get_render_attribute_string( 'description_text' ) );

			if ( ! empty( $settings['link']['url'] ) ) {
				$html .= sprintf( '<a %s class="ft-btn"><i class="fas fa-chevron-up"></i></a>', $this->get_render_attribute_string( 'link' ) );
			}

			$html .= '</div></div>';

		} else {

			$html .= '<div class="ft-image-classic">';
			
			if ( ! empty( $settings['link']['url'] ) ) {
				$html .= sprintf( '<a %s>', $this->get_render_attribute_string( 'link' ) );
				$html .= sprintf( '<div class="ft-img-wrapper">%s</div></a>', $image_html );
			} else {
				$html .= sprintf( '<div class="ft-img-wrapper">%s</div>', $image_html );
			}

			$html .= sprintf( '<%1$s %3$s>%2$s</%1$s>', $settings['title_size'], $settings['title_text'], $this->get_render_attribute_string( 'title_text' ) );
			$html .= sprintf( '<p %2$s>%1$s</p>', $settings['description_text'], $this->get_render_attribute_string( 'description_text' ) );

			if ( ! empty( $settings['link']['url'] ) ) {
				$html .= sprintf( '<a %1$s class="ft-btn btn-txt-arr">%2$s <span class="arr-box"><i class="icon-Arrow-OutRight"></i></span></a>', $this->get_render_attribute_string( 'link' ), $settings['link_text'] );
			}

			$html .= '</div>';
		}

		echo $html;
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
		view.addRenderAttribute( 'title_text', 'class', 'ft-title' );
		view.addInlineEditingAttributes( 'title_text', 'none' );
		
		view.addRenderAttribute( 'description_text', 'class', 'ft-description' );
		view.addInlineEditingAttributes( 'description_text', 'none' );
		#>

		<# if ( settings.ft_layout === 'frame' ) { #>

		<div class="ft-image-frame">
			<# if ( settings.link.url ) { #>
			<a href="{{{ settings.link.url }}}" class="ft-link-frame">
			<# } else { #>
			<div class="ft-link-frame">
			<# } #>

				<div class="ft-img-wrapper">
					<img src="{{{ settings.image.url }}}" alt="Placeholder image">
				</div>
				<{{{ settings.title_size }}} {{{ view.getRenderAttributeString( 'title_text' ) }}}>{{{ settings.title_text }}}</{{{ settings.title_size }}}>
				<p {{{ view.getRenderAttributeString( 'description_text' ) }}}>{{{ settings.description_text }}}</p>
			
			<# if ( settings.link.url ) { #>
			<div class="ft-box"><span class="css-plus"></span></div>
			</a>
			<# } else { #>
			</div>
			<# } #>
		</div>

		<# } else if ( settings.ft_layout === 'stacked' ) { #>

		<div class="ft-image-stacked">
			<# if ( settings.link.url ) { #>
			<a href="{{{ settings.link.url }}}">
				<div class="ft-img-wrapper">
					<img src="{{{ settings.image.url }}}" alt="Placeholder image">
				</div>
			</a>
			<# } else { #>
			<div class="ft-img-wrapper">
				<img src="{{{ settings.image.url }}}" alt="Placeholder image">
			</div>
			<# } #>

			<div class="ft-content">
				<{{{ settings.title_size }}} {{{ view.getRenderAttributeString( 'title_text' ) }}}>{{{ settings.title_text }}}</{{{ settings.title_size }}}>
				<p {{{ view.getRenderAttributeString( 'description_text' ) }}}>{{{ settings.description_text }}}</p>
				
				<# if ( settings.link.url ) { #>
				<a href="{{{ settings.link.url }}}" class="ft-btn"><i class="fas fa-chevron-up"></i></a>
				<# } #>
			</div>
		</div>

		<# } else { #>

		<div class="ft-image-classic">
			<# if ( settings.link.url ) { #>
			<a href="{{{ settings.link.url }}}">
				<div class="ft-img-wrapper">
					<img src="{{{ settings.image.url }}}" alt="Placeholder image">
				</div>
			</a>
			<# } else { #>
			<div class="ft-img-wrapper">
				<img src="{{{ settings.image.url }}}" alt="Placeholder image">
			</div>
			<# } #>

			<{{{ settings.title_size }}} {{{ view.getRenderAttributeString( 'title_text' ) }}}>{{{ settings.title_text }}}</{{{ settings.title_size }}}>
			<p {{{ view.getRenderAttributeString( 'description_text' ) }}}>{{{ settings.description_text }}}</p>
			
			<# if ( settings.link.url ) { #>
			<a href="{{{ settings.link.url }}}" class="ft-btn btn-txt-arr">{{{ settings.link_text }}} <span class="arr-box"><i class="icon-Arrow-OutRight"></i></span></a>
			<# } #>
		</div>

		<# } #>

        <?php
    }

}