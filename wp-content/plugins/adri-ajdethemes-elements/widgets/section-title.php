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
class Ajdethemes_Widget_SectionTitle extends \Elementor\Widget_Base {

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
		return 'section-title';
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
		return __( 'Section Title', 'adri-ajdethemes-elements' );
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
		return 'eicon-archive-title';
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
	 * Register oEmbed widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Content', 'adri-ajdethemes-elements' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
        
        $this->add_control(
			'title_layout',
			[
				'label'   => __( 'Title Layout', 'adri-ajdethemes-elements' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'classic',
				'options' => [
					'classic'   => __( 'Classic', 'adri-ajdethemes-elements' ),
					'underline' => __( 'Underline', 'adri-ajdethemes-elements' ),
					'memphis'   => __( 'Memphis', 'adri-ajdethemes-elements' ),
				],
			]
        );

		$this->add_control(
			'title_text',
			[
				'label' => __( 'Title', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'default' => __( 'This is the title', 'adri-ajdethemes-elements' ),
				'placeholder' => __( 'Enter your title here', 'adri-ajdethemes-elements' ),
				'separator' => 'none',
				'rows' => 5,
				// 'show_label' => false,
			]
		);
        
        $this->add_control(
			'subtitle_text',
			[
				'label'       => __( 'Subtitle', 'adri-ajdethemes-elements' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => __( 'This is the subtitle', 'adri-ajdethemes-elements' ),
				'placeholder' => __( 'Enter your subtitle', 'adri-ajdethemes-elements' ),
				'label_block' => true,
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
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default' => 'h2',
			]
        );
        
        $this->add_responsive_control(
			'align',
			[
				'label' => __( 'Alignment', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'adri-ajdethemes-elements' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'adri-ajdethemes-elements' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'adri-ajdethemes-elements' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
			]
        );
        
        $this->end_controls_section();
        
        $this->start_controls_section(
			'section_title_style',
			[
				'label' => __( 'Title', 'adri-ajdethemes-elements' ),
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
					'{{WRAPPER}} .st-title' => 'color: {{VALUE}};',
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
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .st-subtitle' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'label' => __( 'Title Typography', 'adri-ajdethemes-elements' ),
				'name' => 'title_typography',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .st-title',
			]
		);
		
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'label' => __( 'Subtitle Typography', 'adri-ajdethemes-elements' ),
				'name' => 'subtitle_typography',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .st-subtitle',
			]
		);

		$this->add_control(
			'graphic',
			[
				'label' => __( 'Add Title Graphic (memphis layout only)', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
			]
		);
		
		$this->add_responsive_control(
			'graphic_pos_x',
			[
				'label' => __( 'Graphic Position', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'allowed_dimensions' => [ 'right' ],
				'selectors' => [
					'{{WRAPPER}} .section-title.st-memphis img' => 'right: {{RIGHT}}{{UNIT}};',
				],
				'separator' => 'before',
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
        
        // Subtitle
        $this->add_render_attribute( 'subtitle_text', 'class', 'st-subtitle' );
        $this->add_inline_editing_attributes( 'subtitle_text', 'none' );

        // Title
        $this->add_render_attribute( 'title_text', 'class', 'st-title' );
        $this->add_inline_editing_attributes( 'title_text' );

        if ( $settings['title_layout'] === 'classic' ) {
            $html = '<div class="section-title">';
            $html .= sprintf( '<span %s>%s</span>', $this->get_render_attribute_string( 'subtitle_text' ), $settings['subtitle_text'] );
            $html .= sprintf( '<%1$s %2$s>%3$s</%1$s>', $settings['title_size'], $this->get_render_attribute_string( 'title_text' ), $settings['title_text'] );
            $html .= '</div>';
        } elseif ( $settings['title_layout'] === 'underline' ) {
            $html = '<div class="section-title st-underline">';
            $html .= sprintf( '<span %s>%s</span>', $this->get_render_attribute_string( 'subtitle_text' ), $settings['subtitle_text'] );
            $html .= sprintf( '<%1$s %2$s>%3$s</%1$s>', $settings['title_size'], $this->get_render_attribute_string( 'title_text' ), $settings['title_text'] );
            $html .= '</div>';
        } else {

			if ( ! empty( $settings['graphic'] ) ) {
				$graphic = \Elementor\Group_Control_Image_Size::get_attachment_image_html( $settings, 'graphic' );
			} else {
				$graphic = '';
			}

			$html = '<div class="section-title st-memphis">';
			$html .= $graphic;
            $html .= sprintf( '<span %s>%s</span>', $this->get_render_attribute_string( 'subtitle_text' ), $settings['subtitle_text'] );
            $html .= sprintf( '<%1$s %2$s>%3$s</%1$s>', $settings['title_size'], $this->get_render_attribute_string( 'title_text' ), $settings['title_text'] );
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

        if ( settings.title_layout === 'classic' ) {
            
            var html = '<div class="section-title">';

            if ( settings.subtitle_text ) {
                var subtitle_html = settings.subtitle_text;

                view.addRenderAttribute( 'subtitle_text', 'class', 'st-subtitle' );
                view.addInlineEditingAttributes( 'subtitle_text', 'none' );

                html += '<span ' + view.getRenderAttributeString( 'subtitle_text' ) + '>' + subtitle_html + '</span>';
            }
            
            if ( settings.title_text ) {
                var title_html = settings.title_text;

                view.addRenderAttribute( 'title_text', 'class', [ 'st-title', '', + settings.size ] );
                view.addInlineEditingAttributes( 'title_text', 'none' );


                html += '<' + settings.title_size  + ' ' + view.getRenderAttributeString( 'title_text' ) + '>' + title_html + '</' + settings.title_size  + '>';
            }

            html += '</div>';

        } else if ( settings.title_layout === 'underline' ) {

			var html = '<div class="section-title st-underline">';

			if ( settings.subtitle_text ) {
                var subtitle_html = settings.subtitle_text;

                view.addRenderAttribute( 'subtitle_text', 'class', 'st-subtitle' );
                view.addInlineEditingAttributes( 'subtitle_text', 'none' );

                html += '<span ' + view.getRenderAttributeString( 'subtitle_text' ) + '>' + subtitle_html + '</span>';
			}
			
			if ( settings.title_text ) {
                var title_html = settings.title_text;

                view.addRenderAttribute( 'title_text', 'class', [ 'st-title', '', + settings.size ] );
                view.addInlineEditingAttributes( 'title_text', 'none' );


                html += '<' + settings.title_size  + ' ' + view.getRenderAttributeString( 'title_text' ) + '>' + title_html + '</' + settings.title_size  + '>';
            }

			html += '</div>';

        } else {

			var html = '<div class="section-title st-memphis">';

			if ( settings.graphic.url ) {
                html += '<img src="' + settings.graphic.url + '" />';
			}
			
			if ( settings.subtitle_text ) {
                var subtitle_html = settings.subtitle_text;

                view.addRenderAttribute( 'subtitle_text', 'class', 'st-subtitle' );
                view.addInlineEditingAttributes( 'subtitle_text', 'none' );

                html += '<span ' + view.getRenderAttributeString( 'subtitle_text' ) + '>' + subtitle_html + '</span>';
			}
			
			if ( settings.title_text ) {
                var title_html = settings.title_text;

                view.addRenderAttribute( 'title_text', 'class', [ 'st-title', '', + settings.size ] );
                view.addInlineEditingAttributes( 'title_text', 'none' );


                html += '<' + settings.title_size  + ' ' + view.getRenderAttributeString( 'title_text' ) + '>' + title_html + '</' + settings.title_size  + '>';
            }

			html += '</div>';
		}

        print( html );
		#>
		<?php
    }

}