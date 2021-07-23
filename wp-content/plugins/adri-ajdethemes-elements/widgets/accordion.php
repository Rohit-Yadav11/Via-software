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
class Ajdethemes_Widget_Accordion extends \Elementor\Widget_Base {

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
		return 'accordion';
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
		return __( 'Accordion', 'adri-ajdethemes-elements' );
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
		return 'eicon-accordion';
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
		return [ 'accordion', 'tabs', 'toggle', 'faq' ];
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
			'section_title',
			[
				'label' => __( 'Accordion', 'adri-ajdethemes-elements' ),
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'tab_title',
			[
				'label' => __( 'Title & Description', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Accordion Title', 'adri-ajdethemes-elements' ),
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'tab_content',
			[
				'label' => __( 'Content', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::WYSIWYG,
				'default' => __( 'Accordion Content', 'adri-ajdethemes-elements' ),
				'show_label' => false,
			]
		);

		$this->add_control(
			'tabs',
			[
				'label' => __( 'Accordion Items', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'tab_title' => __( 'Accordion #1', 'adri-ajdethemes-elements' ),
						'tab_content' => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'adri-ajdethemes-elements' ),
					],
					[
						'tab_title' => __( 'Accordion #2', 'adri-ajdethemes-elements' ),
						'tab_content' => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'adri-ajdethemes-elements' ),
					],
				],
				'title_field' => '{{{ tab_title }}}',
			]
        );
        
        $this->add_control(
			'title_html_tag',
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
					'div' => 'div',
				],
				'default' => 'h5',
				'separator' => 'before',
			]
        );
        
        $this->add_control(
			'show_icon',
			[
				'label' => __( 'Show Icon', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => __( 'Show', 'adri-ajdethemes-elements' ),
				'label_off' => __( 'Hide', 'adri-ajdethemes-elements' ),
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_content',
			[
				'label' => __( 'Accordion', 'adri-ajdethemes-elements' ),
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
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .ft-tab-title .ft-accordion-title' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .ft-tab-content' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .ft-accordion.elementor-accordion .ft-accordion-title span i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ft-accordion.elementor-accordion .ft-accordion-title:hover span' => 'background: {{VALUE}};',
					'{{WRAPPER}} .ft-accordion.elementor-accordion .ft-tab-title.elementor-active .ft-accordion-title span' => 'background: {{VALUE}};',
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
                    '{{WRAPPER}} .ft-accordion.elementor-accordion .ft-accordion-title:hover span i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .ft-accordion.elementor-accordion .ft-tab-title.elementor-active .ft-accordion-title span i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .ft-accordion.elementor-accordion .ft-accordion-title:hover span' => 'box-shadow: 5px 5px 0 {{VALUE}};',
                    '{{WRAPPER}} .ft-accordion.elementor-accordion .ft-tab-title.elementor-active .ft-accordion-title span' => 'box-shadow: 5px 5px 0 {{VALUE}};',
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
        $id_int = substr( $this->get_id_int(), 0, 3 );

        $settings['show_icon'] === 'yes' ? $icon = '<span class="accordion-icon"><i class="fas fa-chevron-down"></i></span>' : $icon = '';

        echo '<div class="ft-accordion elementor-accordion" role="tablist">';

        foreach ( $settings['tabs'] as $index => $item ) {
            $tab_count = $index + 1;
            $tab_title_setting_key = $this->get_repeater_setting_key( 'tab_title', 'tabs', $index );
            $tab_content_setting_key = $this->get_repeater_setting_key( 'tab_content', 'tabs', $index );

            $this->add_render_attribute( $tab_title_setting_key, [
                'id' => 'elementor-tab-title-' . $id_int . $tab_count,
                'class' => [ 'ft-tab-title elementor-tab-title' ],
                'data-tab' => $tab_count,
                'role' => 'tab',
            ] );

            $this->add_render_attribute( $tab_content_setting_key, [
                'id' => 'elementor-tab-content-' . $id_int . $tab_count,
                'class' => [ 'ft-tab-content elementor-tab-content', 'elementor-clearfix' ],
                'data-tab' => $tab_count,
                'role' => 'tabpanel',
            ] );

            $this->add_inline_editing_attributes( $tab_content_setting_key, 'advanced' );
            
            $html = '<div class="ft-accordion-item elementor-accordion-item">';
            $html .= sprintf( '<%3$s %2$s><a class="ft-accordion-title elementor-accordion-title" href="">%1$s %4$s</a></%3$s>', $item['tab_title'], $this->get_render_attribute_string( $tab_title_setting_key ), $settings['title_html_tag'], $icon );
            $html .= sprintf( '<div %2$s>%1$s</div>', $this->parse_text_editor( $item['tab_content'] ), $this->get_render_attribute_string( $tab_content_setting_key ) );
            $html .= '</div>';

            echo $html;
        }

        echo '</div>';
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
        <div class="ft-accordion elementor-accordion" role="tablist">
            <#
            if ( settings.tabs ) {
                var tabindex = view.getIDInt().toString().substr( 0, 3 );

                _.each( settings.tabs, function( item, index ) {
                    var tabCount = index + 1,
						tabTitleKey = view.getRepeaterSettingKey( 'tab_title', 'tabs', index ),
						tabContentKey = view.getRepeaterSettingKey( 'tab_content', 'tabs', index );

                    view.addRenderAttribute( tabTitleKey, {
						'id': 'elementor-tab-title-' + tabindex + tabCount,
						'class': [ 'ft-tab-title elementor-tab-title' ],
						'tabindex': tabindex + tabCount,
						'data-tab': tabCount,
						'role': 'tab',
					} );

					view.addRenderAttribute( tabContentKey, {
						'id': 'elementor-tab-content-' + tabindex + tabCount,
						'class': [ 'ft-tab-content elementor-tab-content', 'elementor-clearfix' ],
						'data-tab': tabCount,
						'role': 'tabpanel',
					} );

                    view.addInlineEditingAttributes( tabContentKey, 'advanced' );
                    #>
                    <div class="ft-accordion-item elementor-accordion-item">
                        <{{{ settings.title_html_tag }}} {{{ view.getRenderAttributeString( tabTitleKey ) }}}>
                            <a class="ft-accordion-title elementor-accordion-title" href="">{{{ item.tab_title }}}</a>
                            <# if ( settings.show_icon ) { #>
                            <span class="accordion-icon"><i class="fas fa-chevron-down"></i></span>
                            <# } #>
                        </{{{ settings.title_html_tag }}}>
                        <div {{{ view.getRenderAttributeString( tabContentKey ) }}}>{{{ item.tab_content }}}</div>
                    </div>
                    <#
                } );
            } #>
        </div>
        <?php
    }

}