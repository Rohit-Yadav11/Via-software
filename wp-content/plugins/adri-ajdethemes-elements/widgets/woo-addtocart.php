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
class Ajdethemes_Widget_Woo_AddToCart extends \Elementor\Widget_Base {

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
		return 'adri-ajdethemes-elements-woo-addtocart';
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
		return __( 'Add to Cart', 'adri-ajdethemes-elements' );
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
		return [ 'button', 'click', 'cart', 'shop', 'store', 'woo' ];
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
				'label' => __( 'Add to Cart', 'adri-ajdethemes-elements' ),
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
			]
        );
        
        // $this->add_control(
		// 	'v_icon',
		// 	[
		// 		'label' => __( 'Icons', 'adri-ajdethemes-elements' ),
		// 		'type' => \Elementor\Controls_Manager::ICON,
		// 		'include' => [
		// 			'icon-Knight',
		// 			'icon-Coins'
		// 		],
		// 		'default' => 'icon-Knight',
		// 	]
		// );

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
				'default' => __( 'Velit consequat eu cupidatat irure qui dolore qui consectetur in non culpa labore duis. Velit ad nisi dolore fugiat pariatur consequat.', 'adri-ajdethemes-elements' ),
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

        $this->end_controls_section();
        
        $this->start_controls_section(
			'section_style_content',
			[
				'label' => __( 'Style', 'adri-ajdethemes-elements' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
        );
        
        $this->add_control(
			'icon_mask',
			[
				'label' => __( 'Icon Mask (same as the background)', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .ft-frame-icon .ft-icon' => 'background: {{VALUE}};',
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
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .ft-frame-icon .ft-icon' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .ft-frame-icon .ft-title' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .ft-frame-icon' => 'border-color: {{VALUE}};',
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
					'{{WRAPPER}} .ft-frame-icon .ft-description' => 'color: {{VALUE}};',
				],
			]
        );
        
        $this->add_control(
			'description_hover_color',
			[
				'label' => __( 'Description Hover Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .ft-frame-icon:hover .ft-description' => 'color: {{VALUE}};',
				],
			]
        );
        
        $this->add_control(
			'hover_accent_color',
			[
				'label' => __( 'Hover Accent Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .ft-frame-icon:hover' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .ft-frame-icon:hover .ft-title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ft-frame-icon:hover .ft-icon' => 'color: {{VALUE}};',
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
		
        <?php
    }
}