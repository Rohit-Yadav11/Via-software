<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use \Elementor\Core\Schemes;
use \Elementor\Core\Settings\Manager;

use Elementor\Modules\DynamicTags\Module as TagsModule;

/**
 * Section Title - Ajdethemes Elementor Widget.
 *
 * @since 1.0.0
 */
class Ajdethemes_Widget_Contact extends \Elementor\Widget_Base {

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
		return 'adri-ajdethemes-elements-contact';
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
		return __( 'Contact', 'adri-ajdethemes-elements' );
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
		return 'eicon-google-maps';
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
		return [ 'contact', 'map', 'address', 'info' ];
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
			'section',
			[
				'label' => __( 'Contact Layout', 'adri-ajdethemes-elements' ),
			]
		);

		$this->add_control(
			'style',
			[
				'label'   => __( 'Select Layout', 'adri-ajdethemes-elements' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'classic',
				'options' => [
					'tabs' => __( 'Tabs', 'adri-ajdethemes-elements' ),
					'classic' => __( 'Classic', 'adri-ajdethemes-elements' ),
				],
			]
        );

		$default_address = __( 'London Eye, London, United Kingdom', 'adri-ajdethemes-elements' );
		$this->add_control(
			'address',
			[
				'label' => __( 'Location', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => $default_address,
				'default' => $default_address,
				'label_block' => true,
				'separator' => 'before',
				'condition' => [
					'style' => 'classic',
				],
			]
		);

		$this->add_control(
			'zoom',
			[
				'label' => __( 'Zoom', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'default' => [
					'size' => 14,
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 20,
					],
				],
			]
		);

		$this->add_responsive_control(
			'height',
			[
				'label' => __( 'Height', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 40,
						'max' => 1440,
					],
				],
				'selectors' => [
					'{{WRAPPER}} iframe' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'subtitle',
			[
				'label' => __( 'Subtitle', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Contact Us', 'adri-ajdethemes-elements' ),
				'placeholder' => __( 'Enter your title', 'adri-ajdethemes-elements' ),
				'label_block' => true,
				'separator' => 'before',
				'condition' => [
					'style' => 'classic',
				],
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Title', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Fell free to get in touch', 'adri-ajdethemes-elements' ),
				'placeholder' => __( 'Enter your title', 'adri-ajdethemes-elements' ),
				'label_block' => true,
				'condition' => [
					'style' => 'classic',
				],
			]
		);
		
		$this->add_control(
			'description',
			[
				'label' => '',
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'Our office is open Monday to Friday, from 8AM to 4PM, we answer emails within 24 hour on a business day.', 'adri-ajdethemes-elements' ),
				'placeholder' => __( 'Enter your description', 'adri-ajdethemes-elements' ),
				'rows' => 10,
				'separator' => 'none',
				'show_label' => false,
				'condition' => [
					'style' => 'classic',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_2',
			[
				'label' => __( 'Contact Features', 'adri-ajdethemes-elements' ),
				'condition' => [
					'style' => 'classic',
				],
			]
		);

		$this->add_control(
			'show_cnt_features',
			[
				'label' => __( 'Show Contact Features', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => __( 'Show', 'adri-ajdethemes-elements' ),
				'label_off' => __( 'Hide', 'adri-ajdethemes-elements' ),
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'cf_selected_icon',
			[
				'label' => __( 'Icon', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => [
					'value' => 'fas fa-envelope',
					'library' => 'fa-solid',
				],
			]
		);

		$repeater->add_control(
			'cf_title',
			[
				'label' => __( 'Title', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Enter your title', 'adri-ajdethemes-elements' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'cf_has_link',
			[
				'label' => __( 'Has Link?', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => __( 'Yes', 'adri-ajdethemes-elements' ),
				'label_off' => __( 'No', 'adri-ajdethemes-elements' ),
			]
		);

		$repeater->add_control(
			'cf_link',
			[
				'label' => __( 'Link', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::URL,
				'placeholder' => 'https://your-link.com',
				'condition' => [
					'cf_has_link' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'cf_info',
			[
				'label' => __( 'Info/Description', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'placeholder' => __( 'Enter a description', 'adri-ajdethemes-elements' ),
				'rows' => 10,
			]
		);

		$this->add_control(
			'cnt_features',
			[
				'label' => __( 'Contact Features', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'cf_selected_icon' => [
							'value' => 'fas fa-envelope',
							'library' => 'fa-solid',
						],
						'cf_title' => 'info@company.com',
						'cf_link' => '#',
					],					
					[
						'cf_selected_icon' => [
							'value' => 'fas fa-phone',
							'library' => 'fa-solid',
						],
						'cf_title' => '012-3456-789',
						'cf_link' => 'tel:0123456789',
					],
					[
						'cf_selected_icon' => [
							'value' => 'fas fa-map-marker-alt',
							'library' => 'fa-solid',
						],
						'cf_title' => 'London, UK',
						'cf_info' => '180 Piccadilly, St. James\'s, <br/>London W1J 9HF, UK',
						'cf_has_link' => ''
					],
					
				],
				'title_field' => '{{{ cf_title }}}',
			]
        );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_3',
			[
				'label' => __( 'Social Icons', 'adri-ajdethemes-elements' ),
				'condition' => [
					'style' => 'classic',
				],
			]
		);

		
		$this->add_control(
			'show_social_icons',
			[
				'label' => __( 'Show Social Icons', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => __( 'Show', 'adri-ajdethemes-elements' ),
				'label_off' => __( 'Hide', 'adri-ajdethemes-elements' ),
			]
		);

		$repeater_2 = new \Elementor\Repeater();

		$repeater_2->add_control(
			'si_selected_icon',
			[
				'label' => __( 'Icon', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => [
					'value' => 'fab fa-linkedin-in',
					'library' => 'fa-solid',
				],
			]
		);

		$repeater_2->add_control(
			'si_link',
			[
				'label' => __( 'URL', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::URL,
				'placeholder' => 'https:/linkedin.com/your-account',
			]
		);

		$this->add_control(
			'social_icons',
			[
				'label' => __( 'Social Icons', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater_2->get_controls(),
				'default' => [
					[
						'si_selected_icon' => [
							'value' => 'fab fa-linkedin-in',
							'library' => 'fa-solid',
						],
						'si_link' => '#',
					],					
					[
						'si_selected_icon' => [
							'value' => 'fab fa-twitter',
							'library' => 'fa-solid',
						],
						'si_link' => '#',
					],					
					[
						'si_selected_icon' => [
							'value' => 'fab fa-instagram',
							'library' => 'fa-solid',
						],
						'si_link' => '#',
					],					
					[
						'si_selected_icon' => [
							'value' => 'fab fa-youtube',
							'library' => 'fa-solid',
						],
						'si_link' => '#',
					],					
				],
				'title_field' => '{{{ si_selected_icon.value }}}',
			]
        );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_tabs',
			[
				'label' => __( 'Contact Tabs', 'adri-ajdethemes-elements' ),
				'condition' => [
					'style' => 'tabs',
				],
			]
		);

		$repeater_3 = new \Elementor\Repeater();

		$repeater_3->add_control(
			'tab_title',
			[
				'label' => __( 'Title', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'London, UK',
				'placeholder' => __( 'Enter a title', 'adri-ajdethemes-elements' ),
				'label_block' => true,
			]
		);
		
		$repeater_3->add_control(
			'tab_location',
			[
				'label' => __( 'Location', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '180 Piccadilly, St. James\'s, London W1J 9HF, UK',
				'placeholder' => __( 'Enter a location', 'adri-ajdethemes-elements' ),
				'label_block' => true,
			]
		);

		$repeater_3->add_control(
			'tab_content',
			[
				'label' => __( 'Address &amp; Contact Info', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::WYSIWYG,
				'default' => '',
				'placeholder' => __( 'Enter your description', 'adri-ajdethemes-elements' ),
			]
		);

		$this->add_control(
			'tabs',
			[
				'label' => __( 'Tabs', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater_3->get_controls(),
				'default' => [
					[
						'tab_title' => 'London, UK',
						'tab_location' => 'London Eye, Riverside Building, County Hall, Bishop\'s, <br/>London SE1 7PB, UK',
						'tab_content' => '<a href="tel:123123123">012-345-6789</a></br><a href="mailto:info@company.com">info@company.com</a>',
					],										
					[
						'tab_title' => 'Frankfurt, Germany',
						'tab_location' => 'Iron Bridge, Mainkai 39, <br/>Frankfurt 60311, Germany',
						'tab_content' => '<a href="tel:123123123">012-345-6789</a></br><a href="mailto:info@company.com">info@company.com</a>',
					],										
					[
						'tab_title' => 'San Francisco, USA',
						'tab_location' => 'Design District, <br/>San Francisco, CA, USA',
						'tab_content' => '<a href="tel:123123123">012-345-6789</a></br><a href="mailto:info@company.com">info@company.com</a>',
					],										
				],
				'title_field' => '{{{ tab_title }}}',
			]
        );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_content',
			[
				'label' => __( 'Contact Layout - Style', 'adri-ajdethemes-elements' ),
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
					'{{WRAPPER}} .cnt-classic .cnt-info-list li:hover .cnt-icon, .cnt-classic .cnt-info-list li:focus .cnt-icon' => 'background: {{VALUE}};',
					'{{WRAPPER}} .social-icons-inline li a:hover, .social-icons-inline li a:focus' => 'color: {{VALUE}};',
					'{{WRAPPER}} .cnt-tabs .tab-control ul li [type=radio]:checked ~ label .tc-header i, .cnt-tabs .tab-control ul li [type=radio]:checked ~ label .tc-header .tc-arrow' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .cnt-classic .cnt-info-list li .cnt-icon' => 'background: {{VALUE}};',
					'{{WRAPPER}} .cnt-classic .cnt-info-list li:hover .cnt-icon, .cnt-classic .cnt-info-list li:focus .cnt-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .cnt-classic .cnt-info-list li h6' => 'color: {{VALUE}};',
					'{{WRAPPER}} .social-icons-inline li a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .cnt-tabs .tab-control ul' => 'background: {{VALUE}};',
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
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .section-title .st-title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .cnt-tabs .tab-control ul li label .tc-header .tc-title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .cnt-tabs .tab-control ul li label .tc-header i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .cnt-tabs .tab-control ul li label .tc-header .tc-arrow' => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'tab_active_title_color',
			[
				'label' => __( 'Active Title Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .cnt-tabs .tab-control ul li [type=radio]:checked ~ label .tc-header .tc-title' => 'color: {{VALUE}};',
				],
				'condition' => [
					'style' => 'tabs',
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
					'{{WRAPPER}} .section-title .st-subtitle' => 'color: {{VALUE}};',
				],
				'condition' => [
					'style' => 'classic',
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
					'{{WRAPPER}} .cnt-classic .cnt-description' => 'color: {{VALUE}};',
					'{{WRAPPER}} .cnt-classic .cnt-info-list .cnt-info-content small' => 'color: {{VALUE}};',
					'{{WRAPPER}} .cnt-tabs .tab-control ul li label .tc-content' => 'color: {{VALUE}};',
				],
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
				'default' => 'h2',
				'condition' => [
					'style' => 'classic',
				],
			]
        );
		
		$this->add_control(
			'title_size_2',
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
				'default' => 'h5',
				'condition' => [
					'style' => 'tabs',
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
        $this->add_render_attribute( 'title', 'class', 'st-title' );
        $this->add_inline_editing_attributes( 'title' );
		// Subtitle
        $this->add_render_attribute( 'subtitle', 'class', 'st-subtitle' );
        $this->add_inline_editing_attributes( 'subtitle' );
		// Description
        $this->add_render_attribute( 'description', 'class', 'cnt-description' );
        $this->add_inline_editing_attributes( 'description' );

		if ( 0 === absint( $settings['zoom']['size'] ) ) {
			$settings['zoom']['size'] = 12;
		}

		if ( $settings['style'] === 'tabs' ) {

			$html ='<div class="cnt-tabs">';
			$html .='<div class="tab-content">';
			$html .='<div class="cnt-map">';
			
			// Google Map
			foreach( $settings['tabs'] as $index => $item ) {
				$tab_count = $index + 1;

				$index == 0 ? $map_active = 'active' : $map_active = '';

				$html .= sprintf(
					'<div id="map-tab-' . $tab_count . '" class="elementor-custom-embed ' . $map_active . '" data-target="cnt-tab-' . $tab_count . '"><iframe src="https://maps.google.com/maps?q=%1$s&amp;t=m&amp;z=%2$d&amp;output=embed&amp;iwloc=near" aria-label="%3$s"></iframe></div>',
					rawurlencode( strip_tags( $item['tab_location'] ) ),
					absint( $settings['zoom']['size'] ),
					esc_attr( $item['tab_location'] )
				);
			}

			$html .='</div>';
			$html .='</div>';

			$html .='<div class="tab-control">';
			$html .='<ul>';
			
			// Tabs
			foreach( $settings['tabs'] as $index => $item ) {
				$tab_count = $index + 1;
				$index == 0 ? $checked = 'checked' : $checked = '';

				$html .= '<li>';

				$html .= '<input type="radio" id="cnt-tab-' . $tab_count . '" name="cnt-tabs" ' . $checked . '>';

				$html .= '<label for="cnt-tab-' . $tab_count . '">';	
				$html .= '<span class="tc-header" data-tab="map-tab-' . $tab_count . '">';
				$html .= '<i class="fas fa-map-marker-alt"></i>';
				$html .= sprintf( '<span class="tc-title">%1$s</span>', $item['tab_title'] );
				$html .= '<span class="tc-arrow"><i class="fas fa-chevron-up"></i></span>';
				$html .= '</span>';

				$html .= '<span class="tc-content">';
				$html .= sprintf( '%s<br/>', $item['tab_location'] );
				$html .= sprintf( '%s', $item['tab_content'] );
				$html .= '</span>';
				$html .= '</label>';
				$html .= '</li>';
			}

			$html .='</ul>';
			$html .='</div>';
			
			$html .='</div>';

			echo $html;

		} else {

				$html = '<div class="cnt-classic">';
				$html .= '<div class="row">';
				$html .= '<div class="col-lg-6">';

				$html .= '<div class="cnt-map">';
				// Google Map
				$html .= sprintf(
					'<div class="elementor-custom-embed"><iframe src="https://maps.google.com/maps?q=%1$s&amp;t=m&amp;z=%2$d&amp;output=embed&amp;iwloc=near" aria-label="%3$s"></iframe></div>',
					rawurlencode( $settings['address'] ),
					absint( $settings['zoom']['size'] ),
					esc_attr( $settings['address'] )
				);

				// Social Icons
				if ( $settings['show_social_icons'] === 'yes' ) {
					$html .= '<ul class="social-icons">';
					foreach( $settings['social_icons'] as $index => $item ) {
						$html .= sprintf( '<li><a href="%2$s"><i class="%1$s"></i></a></li>',
							$item['si_selected_icon']['value'],
							$item['si_link']['url']
						);
					}
					$html .= '</ul>';
				}

				$html .= '</div>';

				$html .= '</div>';
				$html .= '<div class="offset-lg-1 col-lg-5">';

				$html .= '<div class="section-title">';
				$html .= $settings['subtitle'] ? sprintf( '<span %2$s>%1$s</span>', $settings['subtitle'], $this->get_render_attribute_string( 'subtitle' ) ) : '';
				$html .= sprintf( '<%3$s %2$s>%1$s</%3$s>', $settings['title'], $this->get_render_attribute_string( 'title' ), $settings['title_size'] );
				$html .= '</div>';
				$html .= sprintf( '<p %2$s>%1$s</p>', $settings['description'], $this->get_render_attribute_string( 'description' ) );

				// Contact Features
				if ( $settings['show_cnt_features'] === 'yes' ) {
					$html .= '<ul class="cnt-info-list">';
					foreach( $settings['cnt_features'] as $index => $item ) {
						$html .= '<li>';
						$html .= sprintf( '<span class="cnt-icon"><i class="%s"></i></span>', $item['cf_selected_icon']['value'] );
						$html .= '<div class="cnt-info-content">';
						$html .= $item['cf_has_link'] === 'yes' ? sprintf( '<a href="%1$s"><h6>%2$s</h6></a>', $item['cf_link']['url'], $item['cf_title'] ) : sprintf( '<h6>%s</h6>',  $item['cf_title'] );
						$html .= $item['cf_info'] ? sprintf( '<small>%s</small>', $item['cf_info'] ) : '';
						$html .= '</div>';
						$html .= '</li>';
					}
					$html .= '</ul>';
				}

				$html .= '</div>';
				$html .= '</div>';
				$html .= '</div>';

			echo $html;
		}
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