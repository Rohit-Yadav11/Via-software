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
class Ajdethemes_Widget_Button extends \Elementor\Widget_Base {

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
		return 'adri-ajdethemes-elements-button';
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
		return __( 'Button', 'adri-ajdethemes-elements' );
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
		return 'eicon-button';
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
		return [ 'button', 'click', 'cta', 'link' ];
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
				'label' => __( 'Button', 'adri-ajdethemes-elements' ),
			]
		);

		$this->add_control(
			'style',
			[
				'label'   => __( 'Style', 'adri-ajdethemes-elements' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'btn-reg',
				'options' => [
					'btn-reg'  => __( 'Regular', 'adri-ajdethemes-elements' ),
					'btn-ghost'  => __( 'Ghost', 'adri-ajdethemes-elements' ),
					'btn-classic' => __( 'Classic', 'adri-ajdethemes-elements' ),
					'btn-txt-arr'  => __( 'Text Arrow', 'adri-ajdethemes-elements' ),
					'btn-int'  => __( 'Interactive', 'adri-ajdethemes-elements' ),
					'btn-play'  => __( 'Play Button (circle)', 'adri-ajdethemes-elements' ),
				],
			]
		);
		
		$this->add_control(
			'size',
			[
				'label'   => __( 'Size', 'adri-ajdethemes-elements' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => ' ',
				'options' => [
					' '  => __( 'Normal', 'adri-ajdethemes-elements' ),
					'btn-lg'  => __( 'Large', 'adri-ajdethemes-elements' ),
					'btn-sm' => __( 'Small', 'adri-ajdethemes-elements' )
				],
				'condition' => [
					'style' => ['btn-reg', 'btn-ghost', 'btn-classic', 'btn-txt-arr', 'btn-int'],
				],
			]
		);

		$this->add_control(
			'text',
			[
				'label' => __( 'Text', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Learn More', 'adri-ajdethemes-elements' ),
				'placeholder' => __( 'Enter button text', 'adri-ajdethemes-elements' ),
				'label_block' => true,
				'condition' => [
					'style' => ['btn-reg', 'btn-ghost', 'btn-classic', 'btn-txt-arr', 'btn-int'],
				],
			]
		);

		$this->add_control(
			'link',
			[
				'label' => __( 'Link', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'adri-ajdethemes-elements' ),
			]
		);

		$this->add_control(
			'has_video_lightbox',
			[
				'label' => __( 'Open video in lightbox', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => __( 'On', 'adri-ajdethemes-elements' ),
				'label_off' => __( 'Off', 'adri-ajdethemes-elements' ),
				'condition' => [
					'style' => ['btn-play', 'btn-int'],
				],
			]
		);
		
		$this->add_control(
			'has_image_lightbox',
			[
				'label' => __( 'Open image in lightbox', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => __( 'On', 'adri-ajdethemes-elements' ),
				'label_off' => __( 'Off', 'adri-ajdethemes-elements' ),
				'condition' => [
					'style' => ['btn-int'],
				],
			]
		);

		$this->add_control(
			'has_hex_icon',
			[
				'label' => __( 'Show Icon', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => __( 'Show', 'adri-ajdethemes-elements' ),
				'label_off' => __( 'Hide', 'adri-ajdethemes-elements' ),
				'separator' => 'before',
				'condition' => [
					'style' => ['btn-int'],
				],
			]
		);

		$this->add_control(
			'icon_hex_code',
			[
				'label' => __( 'Icon Unicode', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'f061', 'adri-ajdethemes-elements' ),
				'placeholder' => __( 'Check fontawesome.com/icons for icon code', 'adri-ajdethemes-elements' ),
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .btn-int-icon:after' => "content: '\{{VALUE}}';",
					'{{WRAPPER}} .btn-int-icon-left:before' => "content: '\{{VALUE}}';"
				],
				'condition' => [
					'style' => ['btn-int'],
					'has_hex_icon' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'icon_hex_position',
			[
				'label' => __( 'Icon Position', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'adri-ajdethemes-elements' ),
						'icon' => 'eicon-text-align-left',
					],
					'right' => [
						'title' => __( 'Right', 'adri-ajdethemes-elements' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'right',
				'condition' => [
					'has_hex_icon' => 'yes',
				],
			]
		);

		$this->add_control(
			'has_icon',
			[
				'label' => __( 'Show Icon', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => __( 'Show', 'adri-ajdethemes-elements' ),
				'label_off' => __( 'Hide', 'adri-ajdethemes-elements' ),
				'separator' => 'before',
				'condition' => [
					'style' => ['btn-reg', 'btn-ghost', 'btn-classic', 'btn-txt-arr'],
				],
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
					'library' => 'solid',
				],
				'condition' => [
					'has_icon' => 'yes',
				],
			]
		);
		
		$this->add_responsive_control(
			'icon_position',
			[
				'label' => __( 'Icon Position', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'adri-ajdethemes-elements' ),
						'icon' => 'eicon-text-align-left',
					],
					'right' => [
						'title' => __( 'Right', 'adri-ajdethemes-elements' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'right',
				'condition' => [
					'has_icon' => 'yes',
				],
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
				'default' => 'left',
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
			]
        );

        $this->end_controls_section();
        
        $this->start_controls_section(
			'section_style_content',
			[
				'label' => __( 'Style', 'adri-ajdethemes-elements' ),
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
					'{{WRAPPER}} .btn' => 'color: {{VALUE}};',
					'{{WRAPPER}} .btn-reg' => 'box-shadow: 0 0 0 {{VALUE}};',
					'{{WRAPPER}} .btn-reg:hover, .btn-reg:focus' => 'box-shadow: 7px 7px 0px {{VALUE}};',
					'{{WRAPPER}} .btn-sm.btn-reg:hover, .btn-sm.btn-reg:focus' => 'box-shadow: 5px 5px 0px {{VALUE}};',
					'{{WRAPPER}} .btn-lg.btn-reg:hover, .btn-lg.btn-reg:focus' => 'box-shadow: 10px 10px 0px {{VALUE}};',
					'{{WRAPPER}} .btn-classic:hover, .btn-classic:focus' => 'background: {{VALUE}};',
					'{{WRAPPER}} .btn-txt-arr .arr-box' => 'background: {{VALUE}};',
					'{{WRAPPER}} .btn-int' => 'background: {{VALUE}};',
				],
				'condition' => [
					'style' => ['btn-txt-arr', 'btn-reg', 'btn-classic', 'btn-int']
				]
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
					'{{WRAPPER}} .btn' => 'background: {{VALUE}};',
					'{{WRAPPER}} .btn-classic:hover, .btn-classic:focus' => 'color: {{VALUE}};',
					'{{WRAPPER}} .btn-txt-arr' => 'color: {{VALUE}};',
					'{{WRAPPER}} .btn-play' => 'color: {{VALUE}};',
					'{{WRAPPER}} .btn-int' => 'color: {{VALUE}};',
					'{{WRAPPER}} .btn-int span:after' => 'border-left-color: {{VALUE}};',
					'{{WRAPPER}} .btn-int span:before' => 'border-left-color: {{VALUE}};',
				],
				'condition' => [
					'style' => ['btn-txt-arr', 'btn-reg', 'btn-classic', 'btn-play', 'btn-int']
				],
				'separator' => 'after',
			]
		);

		$this->add_control(
			'shadow_color',
			[
				'label' => __( 'Shadow Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .btn-reg:hover, {{WRAPPER}} .btn-reg:focus' => 'box-shadow: 7px 7px 0px {{VALUE}};',
					'{{WRAPPER}} .btn-lg.btn-reg:hover, {{WRAPPER}} .btn-lg.btn-reg:focus' => 'box-shadow: 10px 10px 0px {{VALUE}};',
					'{{WRAPPER}} .btn-sm.btn-reg:hover, {{WRAPPER}} .btn-sm.btn-reg:focus' => 'box-shadow: 5px 5px 0px {{VALUE}};',
				],
				'condition' => [
					'style' => 'btn-reg',
				],
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => __( 'Text Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .btn' => 'color: {{VALUE}};',
				],
				'condition' => [
					'style' => ['btn-reg', 'btn-ghost', 'btn-classic', 'btn-txt-arr'],
				],
			]
		);
		
		$this->add_control(
			'text_hover_color',
			[
				'label' => __( 'Text Hover Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .btn:hover, {{WRAPPER}} .btn:focus' => 'color: {{VALUE}};',
				],
				'condition' => [
					'style' => ['btn-reg', 'btn-ghost', 'btn-classic', 'btn-txt-arr'],
				],
			]
		);

		$this->add_control(
			'border_color',
			[
				'label' => __( 'Border Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .btn-ghost, .btn-ghost .btn-main:before' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'style' => 'btn-ghost',
				],
			]
		);

		$this->add_control(
			'background_color',
			[
				'label' => __( 'Background Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .btn-classic' => 'background: {{VALUE}};',
				],
				'condition' => [
					'style' => 'btn-classic',
				],
			]
		);
		
		$this->add_control(
			'background_hover_color',
			[
				'label' => __( 'Background Hover Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .btn-ghost:hover' => 'background: {{VALUE}};',
					'{{WRAPPER}} .btn-classic:hover, .btn-classic:focus' => 'background: {{VALUE}};',
				],
				'condition' => [
					'style' => ['btn-ghost', 'btn-classic'],
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

		$target = $settings['link']['is_external'] ? ' target="_blank"' : '';
		$nofollow = $settings['link']['nofollow'] ? ' rel="nofollow"' : '';
		$vid_lightbox = $settings['has_video_lightbox'] ? 'open-lightbox-video' : '';
		$img_lightbox = $settings['has_image_lightbox'] ? 'open-lightbox' : '';

		$btn_int_icon = '';
		if ( $settings['has_hex_icon'] ) {
			if ( $settings['icon_hex_position'] === 'left' ) {
				$btn_int_icon = 'btn-int-icon-left';
			} else {
				$btn_int_icon = 'btn-int-icon';
			}
		}

		$html_btn_content = '';
		if ( ! empty( $settings['selected_icon']['value'] ) ) {

			if ( $settings['icon_position'] === 'left' ) {
				$html_btn_content .= sprintf( '<span class="btn-icon-left"><i class="%1$s"></i></span>%2$s', $settings['selected_icon']['value'], $settings['text'] );
			} else {
				$html_btn_content .= sprintf( '%2$s<span class="btn-icon-right"><i class="%1$s"></i></span>', $settings['selected_icon']['value'], $settings['text'] );
			}

		} else {
			$html_btn_content .= $settings['text'];
		}

		$html = '';
		if ( $settings['style'] === 'btn-reg' || $settings['style'] === 'btn-classic' ) {
			
			$html .= sprintf( '<a href="%1$s" class="btn %3$s %2$s" %4$s %5$s>', $settings['link']['url'], $settings['size'], $settings['style'], $target, $nofollow );
			$html .= $html_btn_content;
			$html .= '</a>';

		} else if ( $settings['style'] === 'btn-ghost' ) {

			$html .= sprintf( '<a href="%1$s" class="btn %3$s %2$s" %4$s %5$s>', $settings['link']['url'], $settings['size'], $settings['style'], $target, $nofollow );
			$html .= '<span class="btn-main">';
			$html .= $html_btn_content;
			$html .= '</span>';
			$html .= '</a>';

		} else if ( $settings['style'] === 'btn-play' ) {

			$html .= sprintf( '<a href="%1$s" class="btn-play %4$s" %2$s %3$s>', $settings['link']['url'], $target, $nofollow, $vid_lightbox );
			$html .= '<i class="fas fa-play"></i>';
			$html .= '</a>';

		} else if ( $settings['style'] === 'btn-int' ) {

			$html .= sprintf( '<a href="%1$s" class="btn btn-int %8$s %5$s %6$s %7$s" %2$s %3$s>%4$s', $settings['link']['url'], $target, $nofollow, $settings['text'], $vid_lightbox, $img_lightbox, $settings['size'], $btn_int_icon );
			$html .= '</a>';

		} else {

			$html .= sprintf( '<a href="%1$s" class="btn-txt-arr %2$s" %3$s %4$s>', $settings['link']['url'], $settings['size'], $target, $nofollow );
			$html .= $html_btn_content;
			$html .= '<span class="arr-box"><i class="icon-Arrow-OutRight"></i></span>';
			$html .= '</a>';

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
    protected function content_template() {}
}