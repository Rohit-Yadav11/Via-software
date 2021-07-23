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
class Ajdethemes_Widget_Pricing_Table extends \Elementor\Widget_Base {

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
		return 'pricing-table';
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
		return __( 'Pricing Table', 'adri-ajdethemes-elements' );
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
		return 'eicon-price-table';
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
		return [ 'table', 'pricing', 'cost' ];
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
				'label' => __( 'Pricing Table', 'adri-ajdethemes-elements' ),
			]
		);

		$this->add_control(
			'title_text',
			[
				'label' => __( 'Title', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => __( 'Pro', 'adri-ajdethemes-elements' ),
				'placeholder' => __( 'Enter a title', 'adri-ajdethemes-elements' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'price_text',
			[
				'label' => __( 'Price/Amount', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => __( '479', 'adri-ajdethemes-elements' ),
				'placeholder' => __( 'Enter amount', 'adri-ajdethemes-elements' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'currency_text',
			[
				'label' => __( 'Currency', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => __( '$', 'adri-ajdethemes-elements' ),
				'placeholder' => __( 'Enter currency', 'adri-ajdethemes-elements' ),
				'label_block' => true,
			]
		);
		
		$this->add_control(
			'interval_text',
			[
				'label' => __( 'Interval', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => __( '\mo', 'adri-ajdethemes-elements' ),
				'placeholder' => __( 'Enter interval (month, year)', 'adri-ajdethemes-elements' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'editor',
			[
				'label' => '',
				'type' => \Elementor\Controls_Manager::WYSIWYG,
				'default' => '<ul>
				<li>Support available 24/7</li>
				<li>Uptime guarantee 99%</li>
				<li>Automatic updates</li>
			</ul>',
			]
		);

		$this->add_control(
			'pt_featured_on',
			[
				'label' => __( 'Featured', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => __( 'Show', 'adri-ajdethemes-elements' ),
				'label_off' => __( 'Hide', 'adri-ajdethemes-elements' ),
			]
		);

		$this->add_control(
			'featured_text',
			[
				'label' => __( 'Text', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => __( 'recommended', 'adri-ajdethemes-elements' ),
				'placeholder' => __( 'Enter "featured" text', 'adri-ajdethemes-elements' ),
				'label_block' => false,
				'condition' => [
					'pt_featured_on' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_link',
			[
				'label' => __( 'Show Button', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => __( 'Show', 'adri-ajdethemes-elements' ),
				'label_off' => __( 'Hide', 'adri-ajdethemes-elements' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'btn_text',
			[
				'label' => __( 'Button Text', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => __( 'Start Pro', 'adri-ajdethemes-elements' ),
				'placeholder' => __( 'Enter button text', 'adri-ajdethemes-elements' ),
				'label_block' => false,
				'condition' => [
					'show_link' => 'yes',
				],
			]
		);

		$this->add_control(
			'link',
			[
				'label' => __( 'URL', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'https://your-link.com', 'adri-ajdethemes-elements' ),
				'condition' => [
					'show_link' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_content',
			[
				'label' => __( 'Pricing Table', 'adri-ajdethemes-elements' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'price_color',
			[
				'label' => __( 'Price Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .pricing-table .pt-header .price .amount' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'currency_interval_color',
			[
				'label' => __( 'Currency & Interval Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .pricing-table .pt-header .price .currency, {{WRAPPER}} .pricing-table .pt-header .price .interval' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pt_border_color',
			[
				'label' => __( 'Border Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .pricing-table' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'pt_featured_on' => '',
				],
			]
		);
		
		$this->add_control(
			'pt_ft_border_color',
			[
				'label' => __( 'Featured Border Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .featured.pricing-table' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'pt_featured_on' => 'yes',
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
					'{{WRAPPER}} .pricing-table .pt-header .pt-title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .pricing-table .pt-content:before' => 'background: {{VALUE}};',
					'{{WRAPPER}} .pricing-table .pt-cta .cta-btn' => 'background: {{VALUE}};',
					'{{WRAPPER}} .pricing-table .pt-cta .cta-btn:hover, {{WRAPPER}} .pricing-table .pt-cta .cta-btn:focus' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .pricing-table .pt-featured' => 'color: {{VALUE}};',
					'{{WRAPPER}} .pricing-table .pt-cta .cta-btn' => 'color: {{VALUE}};',
					'{{WRAPPER}} .pricing-table .pt-cta .cta-btn:hover, {{WRAPPER}} .pricing-table .pt-cta .cta-btn:focus' => 'background: {{VALUE}};',
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

		$this->add_render_attribute( 'title_text', 'class', 'pt-title' );
        $this->add_inline_editing_attributes( 'title_text' );

		$this->add_render_attribute( 'price_text', 'class', 'amount' );
		$this->add_inline_editing_attributes( 'price_text' );
		
		$this->add_render_attribute( 'currency_text', 'class', 'currency' );
		$this->add_inline_editing_attributes( 'currency_text' );
		
		$this->add_render_attribute( 'interval_text', 'class', 'interval' );
		$this->add_inline_editing_attributes( 'interval_text' );
		
		$this->add_render_attribute( 'featured_text', 'class', 'pt-featured' );
		$this->add_inline_editing_attributes( 'featured_text' );
		
		$this->add_render_attribute( 'btn_text', 'class', 'cta-btn' );
		$this->add_inline_editing_attributes( 'btn_text' );

		if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_link_attributes( 'link', $settings['link'] );
		}

		$settings['pt_featured_on'] === 'yes' ? $pt_ft_class = 'featured' : $pt_ft_class = '';

		$editor_content = $this->get_settings_for_display( 'editor' );
		$editor_content = $this->parse_text_editor( $editor_content );
		$this->add_render_attribute( 'editor', 'class', [ 'elementor-text-editor', 'elementor-clearfix' ] );
		$this->add_inline_editing_attributes( 'editor', 'advanced' );


		$html = '<div class="pricing-table ' . $pt_ft_class . '">';
		$settings['featured_text'] ? $html .= sprintf( '<span class="pt-featured">%s</span>', $settings['featured_text'] ) : $html .= '';

		$html .= '<div class="pt-header">';
		$html .= sprintf( '<h5 %2$s>%1$s</h5>', $settings['title_text'], $this->get_render_attribute_string( 'title_text' ) );
		$html .= '<div class="price">';
		$html .= sprintf( '<span %2$s>%1$s</span>', $settings['currency_text'], $this->get_render_attribute_string( 'currency_text' ) );
		$html .= sprintf( '<span %2$s>%1$s</span>', $settings['price_text'], $this->get_render_attribute_string( 'price_text' ) );
		$html .= sprintf( '<span %2$s>%1$s</span>', $settings['interval_text'], $this->get_render_attribute_string( 'interval_text' ) );
		$html .= '</div>';
		$html .= '</div>';
		
		$html .= '<div class="pt-content">';
		$html .= sprintf( '<div %2$s>%1$s</div>', $editor_content, $this->get_render_attribute_string( 'editor' ) );
		$html .= '</div>';
		
		$html .= '<div class="pt-cta">';
		if ( ! empty( $settings['link']['url'] ) ) {
			$html .= sprintf( '<a %2$s class="cta-btn">%1$s</a>', $settings['btn_text'], $this->get_render_attribute_string( 'link' ) );
		}
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
		view.addRenderAttribute( 'title_text', 'class', 'pt-title' );
		view.addInlineEditingAttributes( 'title_text', 'none' );
		
		view.addRenderAttribute( 'price_text', 'class', 'amount' );
		view.addInlineEditingAttributes( 'price_text' );
		
		view.addRenderAttribute( 'currency_text', 'class', 'currency' );
		view.addInlineEditingAttributes( 'currency_text' );
		
		view.addRenderAttribute( 'interval_text', 'class', 'interval' );
		view.addInlineEditingAttributes( 'interval_text' );
		
		view.addRenderAttribute( 'featured_text', 'class', 'pt-featured' );
		view.addInlineEditingAttributes( 'featured_text' );

		view.addRenderAttribute( 'editor', 'class', [ 'elementor-text-editor', 'elementor-clearfix' ] );
		view.addInlineEditingAttributes( 'editor', 'advanced' );

		settings.pt_featured_on === 'yes' ? view.addRenderAttribute( 'pt_featured_on', 'class', 'pricing-table featured' ) : view.addRenderAttribute( 'pt_featured_on', 'class', 'pricing-table' );
		#>
		<div {{{ view.getRenderAttributeString( 'pt_featured_on' ) }}}>
			<# if ( settings.pt_featured_on === 'yes' ) { #>
			<span {{{ view.getRenderAttributeString( 'featured_text' ) }}}>{{{ settings.featured_text }}}</span>
			<# } #>

			<div class="pt-header">
				<h5 {{{ view.getRenderAttributeString( 'title_text' ) }}}>{{{ settings.title_text }}}</h5>
				<div class="price">
					<span {{{ view.getRenderAttributeString( 'currency_text' ) }}}>{{{ settings.currency_text }}}</span>
					<span {{{ view.getRenderAttributeString( 'price_text' ) }}}>{{{ settings.price_text }}}</span>
					<span {{{ view.getRenderAttributeString( 'interval_text' ) }}}>{{{ settings.interval_text }}}</span>
				</div>
			</div>

			<div class="pt-content">
				<div {{{ view.getRenderAttributeString( 'editor' ) }}}>{{{ settings.editor }}}</div>
			</div>
			
			<# if ( settings.show_link === 'yes' ) { #>
			<div class="pt-cta">
				<a href="{{{ settings.link.url }}}" class="cta-btn">{{{ settings.btn_text }}}</a>
			</div>
			<# } #>
        </div>
        <?php
    }

}